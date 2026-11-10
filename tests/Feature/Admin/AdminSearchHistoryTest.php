<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSearchHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_confidence_filter_only_shows_matching_rows(): void
    {
        config(['ai.confidence_threshold' => 0.25]);

        $admin = User::factory()->create();
        $category = Category::factory()->create();

        SearchHistory::create([
            'question' => 'pertanyaan yang jelas',
            'predicted_category_id' => $category->id,
            'confidence' => 0.8,
        ]);
        SearchHistory::create([
            'question' => 'pertanyaan yang ambigu',
            'predicted_category_id' => $category->id,
            'confidence' => 0.1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/riwayat?low_confidence=1');

        $response->assertOk();
        $response->assertSee('pertanyaan yang ambigu');
        $response->assertDontSee('pertanyaan yang jelas');
    }

    public function test_without_the_filter_all_history_is_shown(): void
    {
        $admin = User::factory()->create();
        $category = Category::factory()->create();

        SearchHistory::create([
            'question' => 'pertanyaan satu',
            'predicted_category_id' => $category->id,
            'confidence' => 0.8,
        ]);
        SearchHistory::create([
            'question' => 'pertanyaan dua',
            'predicted_category_id' => $category->id,
            'confidence' => 0.1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/riwayat');

        $response->assertOk();
        $response->assertSee('pertanyaan satu');
        $response->assertSee('pertanyaan dua');
    }

    public function test_guest_cannot_export_search_history(): void
    {
        $response = $this->get('/admin/riwayat/export');

        $response->assertRedirect('/admin/login');
    }

    public function test_export_returns_a_csv_containing_the_filtered_rows(): void
    {
        config(['ai.confidence_threshold' => 0.25]);

        $admin = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Jadwal Perkuliahan']);

        SearchHistory::create([
            'question' => 'pertanyaan yang jelas',
            'predicted_category_id' => $category->id,
            'confidence' => 0.8,
        ]);
        SearchHistory::create([
            'question' => 'pertanyaan yang ambigu',
            'predicted_category_id' => $category->id,
            'confidence' => 0.1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/riwayat/export?low_confidence=1');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));

        $csv = $response->streamedContent();

        $this->assertStringContainsString('pertanyaan yang ambigu', $csv);
        $this->assertStringNotContainsString('pertanyaan yang jelas', $csv);
        $this->assertStringContainsString('Jadwal Perkuliahan', $csv);
    }
}
