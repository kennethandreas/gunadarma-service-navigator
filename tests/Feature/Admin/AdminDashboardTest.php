<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\SearchHistory;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_correct_totals(): void
    {
        config(['ai.confidence_threshold' => 0.25]);

        $admin = User::factory()->create();
        $category = Category::factory()->create();
        Service::factory()->for($category)->count(2)->create();

        SearchHistory::create([
            'question' => 'pertanyaan yakin',
            'predicted_category_id' => $category->id,
            'confidence' => 0.8,
        ]);
        SearchHistory::create([
            'question' => 'pertanyaan ragu',
            'predicted_category_id' => $category->id,
            'confidence' => 0.1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertViewHas('stats', function (array $stats) {
            return $stats['total_services'] === 2
                && $stats['total_categories'] === 1
                && $stats['total_questions'] === 2
                && $stats['low_confidence'] === 1;
        });
    }
}
