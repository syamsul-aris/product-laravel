<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_page_renders_login_form(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('admin.auth.login');
        $response->assertSee('Sign in');
        $response->assertSee('Email address');
        $response->assertSee('Password');
        $response->assertSee('Sign in');
        $response->assertSee('admin/login');
    }

    public function test_admin_login_page_renders_login_form(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertViewIs('admin.auth.login');
        $response->assertSee('Sign in');
        $response->assertSee('admin/login');
    }

    public function test_login_page_has_logout_link_hidden_until_signed_in(): void
    {
        $response = $this->get('/');

        $response->assertSee('Need help? Contact your administrator.');
        $response->assertDontSee('Sign out');
    }

    public function test_authenticated_admin_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user, 'admin')->get('/');

        $response->assertRedirect('admin/dashboard');
    }

    public function test_successful_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email'    => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('admin/dashboard');
        $this->assertAuthenticatedAs($user, 'admin');
    }

    public function test_failed_login_returns_errors(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->from('/')
            ->post('/admin/login', [
                'email'    => 'admin@example.com',
                'password' => 'wrong-password',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_logout_clears_session_and_redirects_to_root(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->actingAs($user, 'admin')
            ->post('/admin/logout')
            ->assertRedirect('/');

        $this->assertGuest('admin');
    }
}
