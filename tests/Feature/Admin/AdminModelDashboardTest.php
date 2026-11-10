<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminModelDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_dashboard_reflects_whatever_ml_model_metadata_json_currently_says(): void
    {
        // ModelController reads ml/model/metadata.json straight off disk (by
        // design — see its docblock), so this test doesn't fake that file;
        // it just asserts the page renders whichever branch matches the
        // project's real current state, trained or not.
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/model-ai');

        $response->assertOk();

        if (file_exists(base_path('ml/model/metadata.json'))) {
            $response->assertSee('Train / Retrain Model');
        } else {
            $response->assertSee('Model belum pernah dilatih');
        }
    }

    public function test_retrain_button_posts_to_the_ai_service_and_reports_success(): void
    {
        $admin = User::factory()->create();

        Http::fake([
            '*/retrain' => Http::response([
                'status' => 'ok',
                'metadata' => ['model' => 'MultinomialNB'],
                'accuracy' => 0.92,
            ], 200),
        ]);

        $response = $this->actingAs($admin)->post('/admin/model-ai/retrain');

        $response->assertRedirect(route('admin.model.index'));
        $response->assertSessionHas('success');
    }

    public function test_retrain_reports_an_error_when_the_ai_service_is_unreachable(): void
    {
        $admin = User::factory()->create();

        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('AI service down');
        });

        $response = $this->actingAs($admin)->post('/admin/model-ai/retrain');

        $response->assertRedirect(route('admin.model.index'));
        $response->assertSessionHas('error');
    }
}
