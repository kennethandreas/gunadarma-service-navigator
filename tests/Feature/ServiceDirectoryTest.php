<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_lists_active_categories_and_services(): void
    {
        $category = Category::factory()->create(['name' => 'Akademik']);
        $service = Service::factory()->for($category)->create(['name' => 'Jadwal Kuliah']);

        $response = $this->get('/layanan');

        $response->assertOk();
        $response->assertSee('Akademik');
        $response->assertSee('Jadwal Kuliah');
        $response->assertViewHas('categories', fn ($categories) => $categories->pluck('id')->contains($category->id));
    }

    public function test_inactive_category_is_hidden_from_directory(): void
    {
        $inactiveCategory = Category::factory()->inactive()->create(['name' => 'Kategori Nonaktif']);
        Service::factory()->for($inactiveCategory)->create();

        $response = $this->get('/layanan');

        $response->assertOk();
        $response->assertDontSee('Kategori Nonaktif');
    }

    public function test_inactive_service_is_hidden_from_directory(): void
    {
        $category = Category::factory()->create();
        Service::factory()->for($category)->inactive()->create(['name' => 'Layanan Nonaktif']);

        $response = $this->get('/layanan');

        $response->assertOk();
        $response->assertDontSee('Layanan Nonaktif');
    }

    public function test_directory_search_filters_by_keyword(): void
    {
        $category = Category::factory()->create();
        $match = Service::factory()->for($category)->create(['name' => 'Pembayaran Kuliah']);
        $noMatch = Service::factory()->for($category)->create(['name' => 'Pendaftaran Wisuda']);

        $response = $this->get('/layanan?q=pembayaran');

        $response->assertOk();
        $response->assertSee('Pembayaran Kuliah');
        $response->assertDontSee('Pendaftaran Wisuda');
    }
}
