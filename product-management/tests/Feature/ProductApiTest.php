<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $this->category = Category::create(['name' => 'Electronics']);
    }

    public function test_unauthenticated_access_is_forbidden(): void
    {
        $this->getJson('/api/products')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_products(): void
    {
        Product::factory()->count(3)->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin)->getJson('/api/products')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'category_id',
                        'category' => ['id', 'name'],
                        'description',
                        'price',
                        'stock',
                        'enabled',
                    ],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_products_can_be_filtered_by_category(): void
    {
        $otherCategory = Category::create(['name' => 'Books']);
        Product::factory()->create(['category_id' => $this->category->id, 'name' => 'Phone']);
        Product::factory()->create(['category_id' => $otherCategory->id, 'name' => 'Novel']);

        $this->actingAs($this->admin)
            ->getJson("/api/products?category_id={$this->category->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Phone');
    }

    public function test_products_can_be_filtered_by_status(): void
    {
        Product::factory()->enabled()->create(['name' => 'Enabled Product']);
        Product::factory()->disabled()->create(['name' => 'Disabled Product']);

        $this->actingAs($this->admin)
            ->getJson('/api/products?status=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Enabled Product');
    }

    public function test_category_id_in_json_response(): void
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'name'        => 'Test Product',
        ]);

        $this->actingAs($this->admin)
            ->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.category_id', $this->category->id)
            ->assertJsonPath('data.category.name', 'Electronics');
    }

    public function test_product_can_be_created(): void
    {
        $payload = [
            'name'        => 'Wireless Mouse',
            'category_id' => $this->category->id,
            'description' => 'A nice mouse',
            'price'       => 29.99,
            'stock'       => 100,
            'enabled'     => true,
        ];

        $this->actingAs($this->admin)
            ->postJson('/api/products', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Wireless Mouse')
            ->assertJsonPath('data.category_id', $this->category->id)
            ->assertJsonPath('data.category.name', 'Electronics')
            ->assertJsonPath('data.price', '29.99')
            ->assertJsonPath('data.stock', 100)
            ->assertJsonPath('data.enabled', true);

        $this->assertDatabaseHas('products', [
            'name'        => 'Wireless Mouse',
            'category_id' => $this->category->id,
            'price'       => 29.99,
            'stock'       => 100,
            'enabled'     => true,
        ]);
    }

    public function test_product_creation_validates_request(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/products', ['name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_product_can_be_updated(): void
    {
        $product = Product::factory()->create([
            'name'        => 'Old Name',
            'price'       => 10.00,
            'stock'       => 10,
            'enabled'     => false,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->admin)
            ->patchJson("/api/products/{$product->id}", [
                'name'  => 'New Name',
                'price' => 15.00,
                'stock' => 20,
                'enabled' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.price', '15.00')
            ->assertJsonPath('data.stock', 20)
            ->assertJsonPath('data.enabled', true);

        $this->assertDatabaseHas('products', [
            'id'      => $product->id,
            'name'    => 'New Name',
            'price'   => 15.00,
            'stock'   => 20,
            'enabled' => true,
        ]);
    }

    public function test_product_can_be_deleted(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin)
            ->deleteJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Product deleted successfully.');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_products_can_be_bulk_deleted(): void
    {
        $products = Product::factory()->count(3)->create(['category_id' => $this->category->id]);
        $ids = $products->pluck('id')->toArray();

        $this->actingAs($this->admin)
            ->postJson('/api/products/bulk-delete', ['ids' => $ids])
            ->assertOk()
            ->assertJsonPath('message', 'Products deleted successfully.')
            ->assertJsonPath('deleted', 3);

        foreach ($ids as $id) {
            $this->assertSoftDeleted('products', ['id' => $id]);
        }
    }

    public function test_bulk_delete_rejects_invalid_ids(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/products/bulk-delete', ['ids' => [9999]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ids.0']);
    }

    public function test_products_export_returns_excel(): void
    {
        Product::factory()->count(2)->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)
            ->get('/api/products/export');

        $response->assertOk();
        $contentType = $response->headers->get('content-type', '');
        $disposition = $response->headers->get('content-disposition', '');

        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $contentType);
        $this->assertStringContainsString('products.xlsx', $disposition);
    }
}
