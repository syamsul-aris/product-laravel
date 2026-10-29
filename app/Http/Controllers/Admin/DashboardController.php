<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $productsCount = Product::count();
        $productsEnabledCount = Product::enabled()->count();
        $productsDisabledCount = Product::disabled()->count();
        $categoriesCount = Category::count();
        $categoriesWithProducts = Category::has('products')->count();

        return view('admin.dashboard', [
            'productsCount' => $productsCount,
            'productsEnabledCount' => $productsEnabledCount,
            'productsDisabledCount' => $productsDisabledCount,
            'categoriesCount' => $categoriesCount,
            'categoriesWithProducts' => $categoriesWithProducts,
        ]);
    }
}
