<?php

namespace Tests\Feature;

use App\Exports\ProductExport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductExportIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_returns_collection_and_correct_mapping(): void
    {
        $category = Category::create(['name' => 'Electronics']);
        Product::create([
            'name'        => 'Mouse',
            'category_id' => $category->id,
            'price'       => 10.00,
            'stock'       => 5,
            'enabled'     => true,
        ]);

        $export = new ProductExport($category->id);
        $rows = $export->collection();

        $this->assertCount(1, $rows);
        $this->assertInstanceOf(\App\Models\Product::class, $rows[0]);

        $mapping = $export->map($rows[0]);
        $this->assertIsArray($mapping);
        $this->assertCount(10, $mapping);
        $this->assertSame('Mouse', $mapping[1]);
        $this->assertSame('Electronics', $mapping[2]);
    }

    public function test_export_all_products_when_no_category_filter(): void
    {
        $categoryA = Category::create(['name' => 'Electronics']);
        $categoryB = Category::create(['name' => 'Books']);
        Product::create(['name' => 'Phone', 'category_id' => $categoryA->id, 'price' => 100.00, 'stock' => 10, 'enabled' => true]);
        Product::create(['name' => 'Novel', 'category_id' => $categoryB->id, 'price' => 9.00, 'stock' => 50, 'enabled' => false]);

        $export = new ProductExport(null);
        $rows = $export->collection();

        $this->assertCount(2, $rows);
        $this->assertSame('Phone', $export->map($rows[0])[1]);
        $this->assertSame('Novel', $export->map($rows[1])[1]);
    }

    public function test_export_handles_soft_deleted_products(): void
    {
        $category = Category::create(['name' => 'Electronics']);
        $product = Product::create(['name' => 'Deleted Product', 'category_id' => $category->id, 'price' => 5.00, 'stock' => 1, 'enabled' => false]);
        $product->delete();

        $export = new ProductExport($category->id);
        $rows = $export->collection();

        $this->assertCount(1, $rows);
        $mapping = $export->map($rows[0]);
        $this->assertNotEmpty($mapping[7]); // deleted_at
    }
}
