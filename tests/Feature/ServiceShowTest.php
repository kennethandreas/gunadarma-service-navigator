<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_service_detail_page_loads(): void
    {
        $category = Category::factory()->create();
        $service = Service::factory()->for($category)->create([
            'name' => 'Jadwal Kuliah',
            'capabilities' => ['Lihat jadwal per semester'],
        ]);

        $response = $this->get('/layanan/'.$service->slug);

        $response->assertOk();
        $response->assertSee('Jadwal Kuliah');
        $response->assertSee('Lihat jadwal per semester');
    }

    public function test_inactive_service_returns_404_on_public_site(): void
    {
        // Regression guard: Service::getRouteKeyName() deliberately does NOT
        // restrict binding to active() (so admin can still open inactive
        // services), so this check must happen explicitly in the public
        // controller instead.
        $category = Category::factory()->create();
        $service = Service::factory()->for($category)->inactive()->create();

        $response = $this->get('/layanan/'.$service->slug);

        $response->assertNotFound();
    }

    public function test_unknown_slug_returns_404(): void
    {
        $response = $this->get('/layanan/tidak-ada-layanan-ini');

        $response->assertNotFound();
    }

    public function test_unknown_slug_renders_the_branded_404_page_not_the_default_one(): void
    {
        // Phase 21 polish: resources/views/errors/404.blade.php should be
        // picked up automatically instead of Laravel's generic error page.
        $response = $this->get('/layanan/tidak-ada-layanan-ini');

        $response->assertNotFound();
        $response->assertSee('Halaman tidak ditemukan');
        $response->assertSee('Kembali ke Beranda');
    }
}
