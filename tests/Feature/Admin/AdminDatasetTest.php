<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDatasetTest extends TestCase
{
    use RefreshDatabase;

    private string $csvPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Point the dataset controller at a throwaway CSV so tests never
        // touch the real curated ml/dataset/intent_dataset.csv.
        $this->csvPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'intent_dataset_test_'.uniqid().'.csv';
        file_put_contents($this->csvPath, "text,intent\n");

        config(['ai.dataset_path' => $this->csvPath]);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->csvPath)) {
            unlink($this->csvPath);
        }

        parent::tearDown();
    }

    private function seedRow(string $text, string $intent): void
    {
        $handle = fopen($this->csvPath, 'a');
        fputcsv($handle, [$text, $intent]);
        fclose($handle);
    }

    public function test_guest_cannot_access_dataset_management(): void
    {
        $response = $this->get('/admin/dataset');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_list_dataset_rows(): void
    {
        $admin = User::factory()->create();
        Category::factory()->create(['name' => 'Jadwal Perkuliahan', 'slug' => 'jadwal-perkuliahan']);
        $this->seedRow('Saya mau lihat jadwal kuliah', 'jadwal-perkuliahan');

        $response = $this->actingAs($admin)->get('/admin/dataset');

        $response->assertOk();
        $response->assertSee('Saya mau lihat jadwal kuliah');
    }

    public function test_admin_can_add_a_dataset_row(): void
    {
        $admin = User::factory()->create();
        Category::factory()->create(['name' => 'KRS', 'slug' => 'krs']);

        $response = $this->actingAs($admin)->post('/admin/dataset', [
            'text' => 'Bagaimana cara mengisi KRS semester ini',
            'intent' => 'krs',
        ]);

        $response->assertRedirect(route('admin.dataset.index'));

        $contents = file_get_contents($this->csvPath);
        $this->assertStringContainsString('Bagaimana cara mengisi KRS semester ini', $contents);
        $this->assertStringContainsString('krs', $contents);
    }

    public function test_adding_a_dataset_row_requires_text_and_a_valid_intent(): void
    {
        $admin = User::factory()->create();
        Category::factory()->create(['name' => 'KRS', 'slug' => 'krs']);

        $response = $this->actingAs($admin)->post('/admin/dataset', [
            'text' => '',
            'intent' => 'intent-yang-tidak-ada',
        ]);

        $response->assertSessionHasErrors(['text', 'intent']);
    }

    public function test_admin_can_update_a_dataset_row(): void
    {
        $admin = User::factory()->create();
        Category::factory()->create(['name' => 'KRS', 'slug' => 'krs']);
        Category::factory()->create(['name' => 'Wisuda', 'slug' => 'wisuda']);
        $this->seedRow('Teks lama', 'krs');

        $response = $this->actingAs($admin)->put('/admin/dataset/0', [
            'text' => 'Teks baru',
            'intent' => 'wisuda',
        ]);

        $response->assertRedirect(route('admin.dataset.index'));

        $contents = file_get_contents($this->csvPath);
        $this->assertStringContainsString('Teks baru', $contents);
        $this->assertStringNotContainsString('Teks lama', $contents);
    }

    public function test_admin_can_delete_a_dataset_row(): void
    {
        $admin = User::factory()->create();
        Category::factory()->create(['name' => 'KRS', 'slug' => 'krs']);
        $this->seedRow('Baris yang akan dihapus', 'krs');

        $response = $this->actingAs($admin)->delete('/admin/dataset/0');

        $response->assertRedirect(route('admin.dataset.index'));

        $contents = file_get_contents($this->csvPath);
        $this->assertStringNotContainsString('Baris yang akan dihapus', $contents);
    }

    public function test_editing_a_row_that_does_not_exist_returns_404(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/dataset/999/edit');

        $response->assertNotFound();
    }

    public function test_admin_can_export_dataset_as_csv(): void
    {
        $admin = User::factory()->create();
        Category::factory()->create(['name' => 'KRS', 'slug' => 'krs']);
        $this->seedRow('Contoh pertanyaan', 'krs');

        $response = $this->actingAs($admin)->get('/admin/dataset/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
