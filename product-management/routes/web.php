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

Route::get('admin/login', [AdminAuthController::class, 'login']);

Route::post('admin/login', [AdminAuthController::class, 'loginPost'])
    ->middleware('guest:admin')
    ->name('admin.login.perform');

Route::middleware('auth:admin')->group(function () {
    Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('admin/categories', CategoryController::class)->except(['show']);
    Route::get('admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::get('admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('admin/products/store', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('admin/products/show/{id}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::get('admin/products/edit/{product}', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::post('admin/products/destroy/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('admin/products/update/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::get('admin/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::get('admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('admin/categories/store', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('admin/categories/show/{id}', [CategoryController::class, 'show'])->name('admin.categories.show');
    Route::get('admin/categories/edit/{id}', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::post('admin/categories/destroy/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::post('admin/categories/update/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');

    Route::post('admin/products/bulk-destroy', [ProductController::class, 'bulkDestroy'])->name('admin.products.bulk-destroy');

    Route::get('admin/products/export', [ProductController::class, 'export'])->name('admin.products.export');
});
