<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_category_management(): void
    {
        $response = $this->get('/admin/kategori');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_list_categories(): void
    {
        $admin = User::factory()->create();
        Category::factory()->create(['name' => 'Akademik']);

        $response = $this->actingAs($admin)->get('/admin/kategori');

        $response->assertOk();
        $response->assertSee('Akademik');
    }

    public function test_admin_can_create_a_category(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/kategori', [
            'name' => 'Kemahasiswaan',
            'description' => 'Layanan kemahasiswaan',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Kemahasiswaan',
            'slug' => 'kemahasiswaan',
            'status' => true,
        ]);
    }

    public function test_creating_a_category_requires_a_name(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/kategori', [
            'description' => 'Tanpa nama',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_admin_can_update_a_category(): void
    {
        $admin = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put("/admin/kategori/{$category->slug}", [
            'name' => 'Nama Baru',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Nama Baru',
        ]);
    }

    public function test_admin_can_delete_a_category_and_its_services_cascade(): void
    {
        $admin = User::factory()->create();
        $category = Category::factory()->create();
        $service = Service::factory()->for($category)->create();

        $response = $this->actingAs($admin)->delete("/admin/kategori/{$category->slug}");

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}
