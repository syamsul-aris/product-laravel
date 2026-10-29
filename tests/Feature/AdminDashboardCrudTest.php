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
            'name' => 'Keyboard',
            'category_id' => $this->category->id,
            'description' => 'Mechanical keyboard',
            'price' => 49.99,
            'stock' => 5,
            'enabled' => '1',
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

    public function test_product_can_be_disabled_via_update(): void
    {
        $payload = [
            'name' => 'Toggle Me',
            'category_id' => $this->category->id,
            'description' => 'Toggling test',
            'price' => 10.00,
            'stock' => 1,
            'enabled' => '1',
        ];

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.store'), $payload)
            ->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Toggle Me')->firstOrFail();
        $this->assertTrue((bool) $product->enabled, 'Product should start enabled.');

        // The edit form must always submit `enabled` — unchecked checkboxes are
        // omitted from POST bodies, so a hidden enabled=0 field is required.
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('<input type="hidden" name="enabled" value="0">', false);

        // The form posts enabled=0 via a hidden field when the toggle is off.
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.products.update', $product), array_merge($payload, ['enabled' => '0']))
            ->assertRedirect(route('admin.products.index'));

        $this->assertFalse(
            (bool) $product->fresh()->enabled,
            'Disabling a product through the form must persist to the database.'
        );
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

    public function test_category_with_products_cannot_be_deleted(): void
    {
        Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.categories.destroy', $this->category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $empty = Category::create(['name' => 'Unused']);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.categories.destroy', $empty))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        // Categories soft-delete, so the row remains flagged as deleted.
        $this->assertSoftDeleted('categories', ['id' => $empty->id]);
    }

    public function test_deleted_product_can_be_restored_from_trash(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.products.destroy', $product));

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.products.trash'))
            ->assertOk()
            ->assertSee($product->name);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.restore', $product))
            ->assertRedirect(route('admin.products.trash'));

        $this->assertNotNull(Product::find($product->id), 'Product should be restored.');
    }

    public function test_deleted_product_can_be_purged_from_trash(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.products.destroy', $product));

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.products.force-destroy', $product))
            ->assertRedirect(route('admin.products.trash'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_products_index_filters_by_category_and_search(): void
    {
        $other = Category::create(['name' => 'Books']);
        Product::factory()->create(['category_id' => $this->category->id, 'name' => 'Gadget Pro']);
        Product::factory()->create(['category_id' => $other->id, 'name' => 'Novel Book']);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/products?q=Gadget')
            ->assertOk()
            ->assertSee('Gadget Pro')
            ->assertDontSee('Novel Book');

        $this->actingAs($this->admin, 'admin')
            ->get("/admin/products?category_id={$other->id}")
            ->assertOk()
            ->assertSee('Novel Book')
            ->assertDontSee('Gadget Pro');
    }

    public function test_export_accepts_the_active_status_filter(): void
    {
        Product::factory()->enabled()->create(['category_id' => $this->category->id]);
        Product::factory()->disabled()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/products/export?status=0');

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type', '')
        );
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
