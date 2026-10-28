<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->category = Category::create(['name' => 'Electronics']);
    }

    public function test_dashboard_and_admin_pages_render_for_authenticated_admin(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin, 'admin')->get('/admin/dashboard')->assertOk();
        $this->actingAs($this->admin, 'admin')->get('/admin/products')->assertOk();
        $this->actingAs($this->admin, 'admin')->get('/admin/products/create')->assertOk();
        $this->actingAs($this->admin, 'admin')->get(route('admin.products.show', $product))->assertOk();
        $this->actingAs($this->admin, 'admin')->get(route('admin.products.edit', $product))->assertOk();
        $this->actingAs($this->admin, 'admin')->get('/admin/categories')->assertOk();
        $this->actingAs($this->admin, 'admin')->get('/admin/categories/create')->assertOk();
        $this->actingAs($this->admin, 'admin')->get(route('admin.categories.edit', $this->category))->assertOk();
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_product_can_be_created_updated_and_deleted_from_dashboard(): void
    {
        $payload = [
            'name'        => 'Keyboard',
            'category_id' => $this->category->id,
            'description' => 'Mechanical keyboard',
            'price'       => 49.99,
            'stock'       => 5,
            'enabled'     => '1',
        ];

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.store'), $payload)
            ->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Keyboard')->firstOrFail();
        $this->assertDatabaseHas('products', ['name' => 'Keyboard', 'price' => 49.99]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.products.update', $product), array_merge($payload, ['price' => 59.99]))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSame(59.99, (float) $product->fresh()->price);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_products_can_be_bulk_deleted_from_dashboard(): void
    {
        $products = Product::factory()->count(3)->create(['category_id' => $this->category->id]);
        $ids = $products->pluck('id')->all();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.bulk-destroy'), ['ids' => $ids])
            ->assertRedirect(route('admin.products.index'));

        foreach ($ids as $id) {
            $this->assertSoftDeleted('products', ['id' => $id]);
        }
    }

    public function test_products_can_be_exported_to_excel(): void
    {
        Product::factory()->count(2)->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/products/export');

        $response->assertOk();

        $contentType = $response->headers->get('content-type', '');
        $disposition = $response->headers->get('content-disposition', '');

        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $contentType);
        $this->assertStringContainsString('products.xlsx', $disposition);
    }

    public function test_products_index_filters_by_status(): void
    {
        Product::factory()->enabled()->create(['name' => 'Enabled One']);
        Product::factory()->disabled()->create(['name' => 'Disabled One']);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/products?status=1')
            ->assertOk()
            ->assertSee('Enabled One')
            ->assertDontSee('Disabled One');
    }

    public function test_category_can_be_created_and_updated_from_dashboard(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.categories.store'), ['name' => 'Books'])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Books')->firstOrFail();

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.categories.update', $category), ['name' => 'Fiction'])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertSame('Fiction', $category->fresh()->name);
    }
}
