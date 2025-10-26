<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'merchant'])
                       ->where('status', 'active');

        // 搜索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 分类筛选
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 价格范围（使用markup_price进行筛选）
        if ($request->has('min_price')) {
            $query->where(function($q) use ($request) {
                $q->where('markup_price', '>=', $request->min_price)
                  ->orWhere(function($subQ) use ($request) {
                      $subQ->whereNull('markup_price')
                           ->where('price', '>=', $request->min_price);
                  });
            });
        }
        if ($request->has('max_price')) {
            $query->where(function($q) use ($request) {
                $q->where('markup_price', '<=', $request->max_price)
                  ->orWhere(function($subQ) use ($request) {
                      $subQ->whereNull('markup_price')
                           ->where('price', '<=', $request->max_price);
                  });
            });
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderByRaw('COALESCE(markup_price, price) ' . $sortOrder);
                break;
            case 'sales':
                $query->orderBy('sales_count', $sortOrder);
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        // 获取分类列表用于筛选
        $categories = Category::where('status', 'active')
                             ->orderBy('sort_order')
                             ->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        // 增加浏览量
        $product->increment('views_count');

        // 获取相关商品
        $relatedProducts = Product::with(['category', 'merchant'])
                                 ->where('status', 'active')
                                 ->where('category_id', $product->category_id)
                                 ->where('id', '!=', $product->id)
                                 ->limit(4)
                                 ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
