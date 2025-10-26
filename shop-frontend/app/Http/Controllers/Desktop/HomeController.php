<?php

namespace App\Http\Controllers\Desktop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\FeaturedProduct;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
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

            // 获取最新商品
            $latestProducts = Product::with(['category', 'merchant'])
                                    ->where('status', 'active')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(12)
                                    ->get();

            // 使用默认商品作为特色商品
            $savingsProducts = Product::with(['category', 'merchant'])
                ->where('status', 'active')
                ->where('discount_price', '>', 0)
                ->orderBy('discount_price', 'desc')
                ->limit(4)
                ->get();

            $topDealsProducts = Product::with(['category', 'merchant'])
                ->where('status', 'active')
                ->orderBy('sales_count', 'desc')
                ->limit(2)
                ->get();

            return view('desktop.home', compact(
                'categories',
                'popularProducts',
                'savingsProducts',
                'topDealsProducts',
                'latestProducts'
            ));
        } catch (\Exception $e) {
            // 如果出错，返回简单响应
            return response('Desktop Home - Device Type: ' . (app('device_type') ?? 'not set') . ' - Error: ' . $e->getMessage());
        }
    }
}
