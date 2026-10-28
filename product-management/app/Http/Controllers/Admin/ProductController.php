<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('enabled', (bool) $request->string('status')->toString() === '1');
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

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

    public function export(): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        return Excel::download(new ProductExport, 'products.xlsx');
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
