<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\FeaturedProduct;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 获取分类
        $categories = Category::where('is_active', true)
                             ->orderBy('sort_order')
                             ->orderBy('id')
                             ->limit(28)
                             ->get();

        // 获取热门商品
        $popularProducts = Product::with(['category', 'merchant'])
                                 ->where('status', 'active')
                                 ->orderBy('sales_count', 'desc')
                                 ->orderBy('rating', 'desc')
                                 ->limit(8)
                                 ->get();

        // 获取推荐商品
        $recommendedProducts = Product::with(['category', 'merchant'])
                                    ->where('status', 'active')
                                    ->where('is_recommended', true)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(8)
                                    ->get();

        // 获取特色商品
        $savingsProducts = FeaturedProduct::where('type', 'savings')
            ->where('is_active', true)
            ->with('product')
            ->orderBy('sort_order')
            ->get();

        $topDealsProducts = FeaturedProduct::where('type', 'top_deals')
            ->where('is_active', true)
            ->with('product')
            ->orderBy('sort_order')
            ->get();

        // 如果没有特色商品，使用默认商品
        if ($savingsProducts->isEmpty()) {
            $savingsProducts = Product::with(['category', 'merchant'])
                ->where('status', 'active')
                ->where('discount_price', '>', 0)
                ->orderBy('discount_price', 'desc')
                ->limit(4)
                ->get();
        }

        if ($topDealsProducts->isEmpty()) {
            $topDealsProducts = Product::with(['category', 'merchant'])
                ->where('status', 'active')
                ->orderBy('sales_count', 'desc')
                ->limit(2)
                ->get();
        }

        // 获取最新商品
        $latestProducts = Product::with(['category', 'merchant'])
                                ->where('status', 'active')
                                ->orderBy('created_at', 'desc')
                                ->limit(12)
                                ->get();

        return view('home', compact(
            'categories',
            'popularProducts',
            'recommendedProducts',
            'savingsProducts',
            'topDealsProducts',
            'latestProducts'
        ));
    }
}
