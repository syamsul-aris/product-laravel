<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Landing / root page serves the styled login form.
|
*/

Route::get('/', [AdminAuthController::class, 'login']);

Route::get('admin/login', [AdminAuthController::class, 'login'])->name('login');

Route::post('admin/login', [AdminAuthController::class, 'loginPost'])
    ->middleware(['guest:admin', 'throttle:5,1'])
    ->name('admin.login.perform');

Route::middleware(['auth:admin', IsAdmin::class])->group(function () {
    Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Resource routes registered with a URI prefix (admin/products) are named
    // without it (products.index), so wrap them in a name group to expose the
    // admin.products.* / admin.categories.* names the views rely on. Custom
    // routes must stay before the wildcard {product} routes to avoid being
    // shadowed.
    Route::name('admin.')->group(function () {
        Route::get('admin/products/export', [ProductController::class, 'export'])
            ->name('products.export');
        Route::post('admin/products/bulk-destroy', [ProductController::class, 'bulkDestroy'])
            ->name('products.bulk-destroy');

        // Trash — registered before the resource so the wildcard
        // admin/products/{product} route cannot swallow them.
        Route::get('admin/products/trash', [ProductController::class, 'trash'])
            ->name('products.trash');
        Route::post('admin/products/{product}/restore', [ProductController::class, 'restore'])
            ->withTrashed()
            ->name('products.restore');
        Route::delete('admin/products/{product}/force', [ProductController::class, 'forceDestroy'])
            ->withTrashed()
            ->name('products.force-destroy');

        Route::resource('admin/products', ProductController::class);
        Route::resource('admin/categories', CategoryController::class)->except(['show']);
    });
});
