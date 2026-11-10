<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_shows_the_current_config_values(): void
    {
        config([
            'ai.confidence_threshold' => 0.25,
            'ai.service_url' => 'http://127.0.0.1:5000',
        ]);

        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/pengaturan');

        $response->assertOk();
        $response->assertSee('25%');
        $response->assertSee('http://127.0.0.1:5000');
    }
}
