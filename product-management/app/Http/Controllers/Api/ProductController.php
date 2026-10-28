<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    /**
     * List of products (filterable by category).
     */
    #[OA\Operation(
        summary: 'List products',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['0', '1'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product list', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Product')),
                    new OA\Property(property: 'meta', type: 'object', description: 'Pagination metadata'),
                ]
            )),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->integer('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('enabled', $request->string('status')->toString() === '1');
        }

        $products = $query->orderBy('id', 'desc')->paginate(15);

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * Create a new product.
     */
    #[OA\Operation(
        summary: 'Create product',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Wireless Mouse'),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                    new OA\Property(property: 'stock', type: 'integer', example: 100),
                    new OA\Property(property: 'enabled', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    new OA\Property(property: 'message', type: 'string'),
                ]
            )),
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        $product->load('category');

        return response()->json([
            'data'  => new ProductResource($product),
            'message' => 'Product created successfully.',
        ], 201);
    }

    /**
     * Product detail.
     */
    #[OA\Operation(
        summary: 'Product detail',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product detail', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                ]
            )),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        $product->load('category');

        return response()->json([
            'data'     => new ProductResource($product),
            'message'  => 'Product updated successfully.',
        ]);
    }

    /**
     * Delete a product.
     */
    #[OA\Operation(
        summary: 'Delete product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string'),
                ]
            )),
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }

    /**
     * Bulk delete products.
     */
    #[OA\Operation(
        summary: 'Bulk delete products',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2, 3]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Bulk deleted', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string'),
                    new OA\Property(property: 'deleted', type: 'integer'),
                ]
            )),
        ]
    )]
    public function bulkDestroy(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:products,id'],
        ]);
        $ids = $request->input('ids');

        $deleted = Product::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => 'Products deleted successfully.',
            'deleted' => $deleted,
        ]);
    }

    /**
     * Export products to Excel.
     */
    #[OA\Operation(
        summary: 'Export products to Excel',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Excel file download'),
        ]
    )]
    public function export(Request $request)
    {
        return Excel::download(new ProductExport($request->query('category_id')), 'products.xlsx');
    }
}
