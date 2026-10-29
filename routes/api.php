<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\SwaggerController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

/**
 * Public authentication endpoints.
 */
Route::get('/swagger/info', [SwaggerController::class, 'index']);

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware(['auth:sanctum', IsAdmin::class])->group(function () {
    /**
     * Authenticated user endpoints.
     */
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    /**
     * Category endpoints.
     */
    Route::get('/categories', [CategoryController::class, 'index']);

    /**
     * Product endpoints.
     */
    Route::get('/products/export', [ProductController::class, 'export'])->name('api.products.export');
    Route::apiResource('products', ProductController::class)->except(['destroy']);
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/bulk-delete', [ProductController::class, 'bulkDestroy'])
        ->name('api.products.bulk-destroy');
});
