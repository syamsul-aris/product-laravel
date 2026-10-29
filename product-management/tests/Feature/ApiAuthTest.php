<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_receive_token(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => bcrypt('password'),
        ]);

        $this->postJson('/api/auth/login', [
            'email'    => $admin->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure(['message', 'token', 'user' => ['id', 'email', 'is_admin']]);
    }

    public function test_non_admin_cannot_obtain_api_token(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'password' => bcrypt('password'),
        ]);

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertForbidden();
    }

    public function test_non_admin_authenticated_user_is_rejected_by_api_routes(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->getJson('/api/products')->assertForbidden();
        $this->actingAs($user)->getJson('/api/categories')->assertForbidden();
    }

    public function test_api_login_is_rate_limited_after_five_attempts(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->postJson('/api/auth/login', [
                'email'    => 'nobody@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $this->postJson('/api/auth/login', [
            'email'    => 'nobody@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }
}
