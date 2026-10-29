<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    /**
     * List of products (filterable by category).
     */
    #[OA\Get(
        path: '/api/products',
        summary: 'List products',
        tags: ['Products'],
        security: [['sanctum' => []]],
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
            new OA\Response(response: 401, description: 'Unauthenticated'),
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
    #[OA\Post(
        path: '/api/products',
        summary: 'Create product',
        tags: ['Products'],
        security: [['sanctum' => []]],
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
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
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
    #[OA\Get(
        path: '/api/products/{product}',
        summary: 'Product detail',
        tags: ['Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product detail', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                ]
            )),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    #[OA\Put(
        path: '/api/products/{product}',
        summary: 'Update product',
        tags: ['Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
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
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    new OA\Property(property: 'message', type: 'string'),
                ]
            )),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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
    #[OA\Delete(
        path: '/api/products/{product}',
        summary: 'Delete product',
        tags: ['Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string'),
                ]
            )),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Product not found'),
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
    #[OA\Post(
        path: '/api/products/bulk-delete',
        summary: 'Bulk delete products',
        tags: ['Products'],
        security: [['sanctum' => []]],
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
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
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
    #[OA\Get(
        path: '/api/products/export',
        summary: 'Export products to Excel',
        tags: ['Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Excel file download'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function export(Request $request)
    {
        $categoryId = $request->filled('category_id') ? $request->integer('category_id') : null;
        $enabled = $this->statusFilter($request);

        return Excel::download(new ProductExport($categoryId, $enabled), 'products.xlsx');
    }

    /**
     * Resolve the optional ?status=0|1 query filter.
     */
    private function statusFilter(Request $request): ?bool
    {
        if (! $request->filled('status')) {
            return null;
        }

        return $request->string('status')->toString() === '1';
    }
}
