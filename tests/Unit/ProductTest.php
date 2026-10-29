<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category(): void
    {
        $category = Category::create(['name' => 'Electronics']);
        $product = Product::create([
            'name' => 'Phone',
            'category_id' => $category->id,
            'price' => 500.00,
            'stock' => 10,
        ]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals('Electronics', $product->category->name);
    }

    public function test_category_has_many_products(): void
    {
        $category = Category::create(['name' => 'Books']);
        Product::create(['name' => 'Book A', 'category_id' => $category->id, 'price' => 10, 'stock' => 5]);
        Product::create(['name' => 'Book B', 'category_id' => $category->id, 'price' => 12, 'stock' => 3]);

        $this->assertCount(2, $category->products);
    }

    public function test_product_price_is_cast_to_decimal(): void
    {
        $product = Product::create([
            'name' => 'Item',
            'category_id' => Category::create(['name' => 'Cat'])->id,
            'price' => 19.995,
            'stock' => 1,
        ]);

        $this->assertSame(20.0, (float) $product->price);
    }

    public function test_product_stock_is_cast_to_integer(): void
    {
        $product = Product::create([
            'name' => 'Item',
            'category_id' => Category::create(['name' => 'Cat'])->id,
            'price' => 10.00,
            'stock' => 10,
        ]);

        $this->assertIsInt($product->stock);
        $this->assertSame(10, $product->stock);
    }

    public function test_product_enabled_is_cast_to_boolean(): void
    {
        $category = Category::create(['name' => 'Cat']);
        $enabled = Product::create(['name' => 'On', 'category_id' => $category->id, 'price' => 10, 'stock' => 1, 'enabled' => true]);
        $disabled = Product::create(['name' => 'Off', 'category_id' => $category->id, 'price' => 10, 'stock' => 1, 'enabled' => false]);

        $this->assertTrue($enabled->enabled);
        $this->assertFalse($disabled->enabled);
    }

    public function test_product_can_be_soft_deleted(): void
    {
        $category = Category::create(['name' => 'Cat']);
        $product = Product::create(['name' => 'Item', 'category_id' => $category->id, 'price' => 10, 'stock' => 1]);

        $product->delete();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertNull(Product::find($product->id));
        $this->assertNotNull(Product::withTrashed()->find($product->id));
    }

    public function test_product_scopes(): void
    {
        $category = Category::create(['name' => 'Cat']);
        $enabled = Product::create(['name' => 'A', 'category_id' => $category->id, 'price' => 10, 'stock' => 1, 'enabled' => true]);
        $disabled = Product::create(['name' => 'B', 'category_id' => $category->id, 'price' => 10, 'stock' => 1, 'enabled' => false]);

        $this->assertCount(1, Product::enabled()->get());
        $this->assertCount(1, Product::disabled()->get());
        $this->assertTrue(Product::enabled()->find($enabled->id)->enabled);
        $this->assertFalse(Product::disabled()->find($disabled->id)->enabled);
    }
}
