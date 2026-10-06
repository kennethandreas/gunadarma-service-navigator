<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_away_from_the_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_log_in_with_correct_credentials(): void
    {
        $admin = User::factory()->create(['password' => bcrypt('rahasia123')]);

        $response = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $admin = User::factory()->create(['password' => bcrypt('rahasia123')]);

        $response = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logged_in_admin_can_log_out(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_logged_in_admin_is_redirected_away_from_login_form(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/login');

        $response->assertRedirect('/admin/dashboard');
    }
}
