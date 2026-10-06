<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Service;
use App\Services\SearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_matches_a_service_whose_name_contains_the_keyword(): void
    {
        $category = Category::factory()->create();
        $service = Service::factory()->for($category)->create(['name' => 'Pembayaran Kuliah']);

        $result = (new SearchService())->search('mau bayar kuliah semester ini');

        $this->assertNotNull($result);
        $this->assertTrue($result->is($service));
    }

    public function test_name_match_outranks_a_description_only_match(): void
    {
        $category = Category::factory()->create();
        $nameMatch = Service::factory()->for($category)->create([
            'name' => 'Wisuda',
            'description' => 'Informasi umum kampus',
        ]);
        $descriptionMatch = Service::factory()->for($category)->create([
            'name' => 'Layanan Lain',
            'description' => 'Info wisuda periode berikutnya',
        ]);

        $result = (new SearchService())->search('wisuda');

        $this->assertTrue($result->is($nameMatch));
    }

    public function test_returns_null_when_nothing_matches(): void
    {
        Service::factory()->create(['name' => 'Jadwal Kuliah', 'description' => 'Lihat jadwal']);

        $result = (new SearchService())->search('xyzxyz benar benar tidak nyambung');

        $this->assertNull($result);
    }

    public function test_ignores_inactive_services(): void
    {
        Service::factory()->inactive()->create(['name' => 'Layanan Nonaktif']);

        $result = (new SearchService())->search('layanan nonaktif');

        $this->assertNull($result);
    }

    public function test_short_words_and_stopwords_are_ignored(): void
    {
        // A query built entirely from stopwords / short filler words should
        // behave the same as an empty query: no keywords left to match on.
        Service::factory()->create(['name' => 'Sesuatu']);

        $result = (new SearchService())->search('saya mau tolong ke di ini itu');

        $this->assertNull($result);
    }
}
