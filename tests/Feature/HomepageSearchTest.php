<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SearchHistory;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HomepageSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_without_a_search(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('welcome');
        $response->assertViewHas('searched', false);
    }

    public function test_ai_result_above_threshold_is_shown_and_logged(): void
    {
        config(['ai.confidence_threshold' => 0.25]);

        $category = Category::factory()->create(['slug' => 'jadwal-perkuliahan']);
        $service = Service::factory()->for($category)->create();

        Http::fake([
            '*/predict' => Http::response([
                'question' => 'jadwal kuliah',
                'intent' => 'jadwal-perkuliahan',
                'confidence' => 0.51,
            ], 200),
        ]);

        $response = $this->get('/?q=jadwal+kuliah');

        $response->assertOk();
        $response->assertViewHas('result', fn ($result) => $result->is($service));
        $response->assertViewHas('source', 'ai');

        $this->assertDatabaseHas('search_histories', [
            'question' => 'jadwal kuliah',
            'predicted_category_id' => $category->id,
            'service_id' => $service->id,
        ]);
    }

    public function test_ai_result_below_threshold_shows_low_confidence_fallback_instead_of_a_result(): void
    {
        // Regression guard for the bug where "jadwal kuliah" (a correct,
        // obvious match) was rejected because the threshold was
        // miscalibrated. This test locks in the *mechanism*: a confidence
        // below config('ai.confidence_threshold') must never show a result,
        // regardless of what that threshold is currently set to.
        config(['ai.confidence_threshold' => 0.6]);

        $category = Category::factory()->create(['slug' => 'jadwal-perkuliahan']);
        Service::factory()->for($category)->create();

        Http::fake([
            '*/predict' => Http::response([
                'question' => 'jadwal kuliah',
                'intent' => 'jadwal-perkuliahan',
                'confidence' => 0.51,
            ], 200),
        ]);

        $response = $this->get('/?q=jadwal+kuliah');

        $response->assertOk();
        $response->assertViewHas('result', null);
        $response->assertViewHas('lowConfidence', true);
    }

    public function test_ai_unreachable_falls_back_to_keyword_search(): void
    {
        $category = Category::factory()->create();
        $service = Service::factory()->for($category)->create(['name' => 'Pendaftaran Sidang Skripsi']);

        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('AI service down');
        });

        $response = $this->get('/?q=sidang+skripsi');

        $response->assertOk();
        $response->assertViewHas('source', 'keyword');
        $response->assertViewHas('result', fn ($result) => $result?->is($service));
    }

    public function test_search_with_no_match_still_gets_logged(): void
    {
        Http::fake([
            '*/predict' => Http::response([], 503),
        ]);

        $this->get('/?q=zzz+tidak+relevan+sekali');

        $this->assertDatabaseHas('search_histories', [
            'question' => 'zzz tidak relevan sekali',
            'service_id' => null,
        ]);
        $this->assertSame(1, SearchHistory::count());
    }
}
