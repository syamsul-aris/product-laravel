<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('enabled', $request->string('status')->toString() === '1');
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::pluck('name', 'id'),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('admin.products.show', ['product' => $product]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::pluck('name', 'id'),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $categoryId = $request->filled('category_id') ? $request->integer('category_id') : null;
        $enabled = null;

        if ($request->filled('status')) {
            $enabled = $request->string('status')->toString() === '1';
        }

        return Excel::download(new ProductExport($categoryId, $enabled), 'products.xlsx');
    }

    /**
     * Soft-deleted products awaiting restore.
     */
    public function trash(): View
    {
        $products = Product::onlyTrashed()
            ->with('category')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('admin.products.trash', ['products' => $products]);
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore(Product $product): RedirectResponse
    {
        $product->restore();

        return redirect()->route('admin.products.trash')
            ->with('success', 'Product restored successfully.');
    }

    /**
     * Permanently remove a soft-deleted product.
     */
    public function forceDestroy(Product $product): RedirectResponse
    {
        $product->forceDelete();

        return redirect()->route('admin.products.trash')
            ->with('success', 'Product permanently deleted.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:products,id'],
        ]);

        Product::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Selected products deleted successfully.');
    }
}
