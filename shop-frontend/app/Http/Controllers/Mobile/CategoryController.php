<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 'active')
                             ->orderBy('sort_order')
                             ->orderBy('id')
                             ->get();

        return view('mobile.categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        // 获取子分类
        $subcategories = Category::where('parent_id', $category->id)
                                ->where('status', 'active')
                                ->orderBy('sort_order')
                                ->get();

        // 获取该分类下的商品
        $products = Product::with(['category', 'merchant'])
                          ->where('category_id', $category->id)
                          ->where('status', 'active')
                          ->orderBy('sales_count', 'desc')
                          ->orderBy('rating', 'desc')
                          ->paginate(20);

        // 获取面包屑导航
        $breadcrumbs = $this->getBreadcrumbs($category);

        return view('mobile.categories.show', compact('category', 'subcategories', 'products', 'breadcrumbs'));
    }

    /**
     * 获取面包屑导航
     */
    private function getBreadcrumbs(Category $category)
    {
        $breadcrumbs = [
            ['name' => 'TikTok Shop', 'url' => route('mobile.home')],
            ['name' => 'Shop All Categories', 'url' => route('mobile.categories')],
        ];

        if ($category->parent_id) {
            $parent = Category::find($category->parent_id);
            if ($parent) {
                $breadcrumbs[] = ['name' => $parent->name, 'url' => route('mobile.categories.show', $parent)];
            }
        }

        $breadcrumbs[] = ['name' => $category->name, 'url' => null];

        return $breadcrumbs;
    }
}
