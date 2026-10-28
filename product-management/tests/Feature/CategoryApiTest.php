<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
    }

    public function test_authenticated_user_can_list_categories(): void
    {
        Category::create(['name' => 'Electronics']);
        Category::create(['name' => 'Books']);

        $this->actingAs($this->admin)
            ->getJson('/api/categories')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name']]])
            ->assertJsonCount(2, 'data');
    }

    public function test_unauthenticated_access_is_forbidden(): void
    {
        $this->getJson('/api/categories')
            ->assertUnauthorized();
    }
}
