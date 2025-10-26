<?php

namespace App\Http\Controllers\Mobile;

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
                                 ->limit(8)
                                 ->get();

            // 获取热门商品
            $popularProducts = Product::with(['category', 'merchant'])
                                     ->where('status', 'active')
                                     ->orderBy('sales_count', 'desc')
                                     ->orderBy('rating', 'desc')
                                     ->limit(8)
                                     ->get();

            return view('mobile.home', compact('categories', 'popularProducts'));
        } catch (\Exception $e) {
            // 如果出错，返回简单响应
            return response('
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TikTok Shop - 移动端</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: #000; 
            color: #fff; 
        }
        .container { max-width: 400px; margin: 0 auto; }
        .section { margin: 20px 0; padding: 20px; background: #333; border-radius: 8px; }
        .btn { 
            display: block; 
            width: 100%; 
            padding: 12px; 
            margin: 8px 0; 
            background: #ff0050; 
            color: white; 
            text-decoration: none; 
            border-radius: 8px; 
            text-align: center; 
        }
        .btn:hover { background: #e6004a; }
    </style>
</head>
<body>
    <div class="container">
        <h1>TikTok Shop - 移动端</h1>
        
        <div class="section">
            <h2>设备信息</h2>
            <p>设备类型: ' . (app('device_type') ?? '未设置') . '</p>
            <p>当前路径: ' . request()->path() . '</p>
            <p>Session设备类型: ' . (session('device_type') ?? '未设置') . '</p>
        </div>
        
        <div class="section">
            <h2>测试链接</h2>
            <a href="/desktop" class="btn">桌面端首页</a>
            <a href="/mobile" class="btn">移动端首页</a>
            <a href="/switch-device/desktop" class="btn">切换到桌面端</a>
        </div>
        
        <div class="section">
            <h2>错误信息</h2>
            <p>Error: ' . $e->getMessage() . '</p>
        </div>
    </div>
</body>
</html>');
        }
    }
}
