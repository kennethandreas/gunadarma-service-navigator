<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServiceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_service_management(): void
    {
        $response = $this->get('/admin/layanan');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_create_a_service_with_capabilities_split_by_line(): void
    {
        $admin = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/layanan', [
            'category_id' => $category->id,
            'name' => 'Jadwal Kuliah',
            'description' => 'Cek jadwal kuliah semester ini',
            'capabilities' => "Lihat jadwal per semester\nUnduh jadwal PDF\n",
            'url' => 'https://baak.gunadarma.ac.id',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.services.index'));

        $service = Service::where('slug', 'jadwal-kuliah')->firstOrFail();
        $this->assertSame($category->id, $service->category_id);
        $this->assertSame(['Lihat jadwal per semester', 'Unduh jadwal PDF'], $service->capabilities);
    }

    public function test_creating_a_service_requires_a_valid_category(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/layanan', [
            'category_id' => 999999,
            'name' => 'Layanan Tanpa Kategori',
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertDatabaseCount('services', 0);
    }

    public function test_creating_a_service_rejects_an_invalid_url(): void
    {
        $admin = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/layanan', [
            'category_id' => $category->id,
            'name' => 'Layanan URL Salah',
            'url' => 'bukan-url-yang-valid',
        ]);

        $response->assertSessionHasErrors('url');
    }

    public function test_admin_can_toggle_a_service_to_inactive(): void
    {
        $admin = User::factory()->create();
        $category = Category::factory()->create();
        $service = Service::factory()->for($category)->create(['status' => true]);

        $response = $this->actingAs($admin)->put("/admin/layanan/{$service->slug}", [
            'category_id' => $category->id,
            'name' => $service->name,
            // 'status' intentionally omitted — an unchecked checkbox sends nothing.
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', ['id' => $service->id, 'status' => false]);
    }

    public function test_admin_can_delete_a_service(): void
    {
        $admin = User::factory()->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/layanan/{$service->slug}");

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_admin_can_still_open_an_inactive_service_for_editing(): void
    {
        // Regression guard: Service::getRouteKeyName() must not restrict
        // binding to active() only, or this admin route would 404.
        $admin = User::factory()->create();
        $service = Service::factory()->inactive()->create();

        $response = $this->actingAs($admin)->get("/admin/layanan/{$service->slug}/edit");

        $response->assertOk();
    }
}
