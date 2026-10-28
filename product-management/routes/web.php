<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
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
    ->middleware('guest:admin')
    ->name('admin.login.perform');

Route::middleware('auth:admin')->group(function () {
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

        Route::resource('admin/products', ProductController::class);
        Route::resource('admin/categories', CategoryController::class)->except(['show']);
    });
});
