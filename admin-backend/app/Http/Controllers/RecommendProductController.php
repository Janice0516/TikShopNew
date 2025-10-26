<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class RecommendProductController extends Controller
{
    /**
     * 显示推荐商品列表
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'merchant'])->where('is_featured', true);

        // 搜索
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // 按分类筛选
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 按商家筛选
        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 按价格范围筛选
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 按推荐时间筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $categories = Category::active()->ordered()->get();
        $merchants = User::where('type', 'merchant')->get();

        return view('admin.recommend-products.index', compact('products', 'categories', 'merchants'));
    }

    /**
     * 显示创建推荐商品表单
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        $merchants = User::where('type', 'merchant')->get();
        $products = Product::where('is_featured', false)->where('status', 'active')->get();
        
        return view('admin.recommend-products.create', compact('categories', 'merchants', 'products'));
    }

    /**
     * 创建推荐商品
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'recommend_type' => 'required|string|in:manual,auto',
            'position' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $count = 0;
        foreach ($request->product_ids as $productId) {
            $product = Product::find($productId);
            if ($product && !$product->is_featured) {
                $product->update([
                    'is_featured' => true,
                    'sort_order' => $request->sort_order ?? 0,
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.recommend-products.index')
            ->with('success', "成功推荐 {$count} 个商品");
    }

    /**
     * 显示推荐商品详情
     */
    public function show(Product $product)
    {
        $product->load(['category', 'merchant']);
        
        // 获取商品统计信息
        $stats = [
            'views_count' => rand(100, 1000), // 模拟数据
            'sales_count' => rand(10, 100),
            'favorites_count' => rand(20, 200),
            'recommend_days' => $product->updated_at->diffInDays(now()),
        ];

        return view('admin.recommend-products.show', compact('product', 'stats'));
    }

    /**
     * 显示编辑推荐商品表单
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->ordered()->get();
        $merchants = User::where('type', 'merchant')->get();
        
        return view('admin.recommend-products.edit', compact('product', 'categories', 'merchants'));
    }

    /**
     * 更新推荐商品
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sort_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $product->update([
            'sort_order' => $request->sort_order ?? $product->sort_order,
        ]);

        return redirect()->route('admin.recommend-products.index')
            ->with('success', '推荐商品已更新');
    }

    /**
     * 取消推荐商品
     */
    public function destroy(Product $product)
    {
        $product->update(['is_featured' => false]);

        return redirect()->route('admin.recommend-products.index')
            ->with('success', '已取消推荐该商品');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:recommend,cancel_recommend,update_sort',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $productIds = $request->product_ids;
        $count = 0;

        switch ($request->action) {
            case 'recommend':
                foreach ($productIds as $productId) {
                    $product = Product::find($productId);
                    if ($product && !$product->is_featured) {
                        $product->update(['is_featured' => true]);
                        $count++;
                    }
                }
                $message = "成功推荐 {$count} 个商品";
                break;
                
            case 'cancel_recommend':
                foreach ($productIds as $productId) {
                    $product = Product::find($productId);
                    if ($product && $product->is_featured) {
                        $product->update(['is_featured' => false]);
                        $count++;
                    }
                }
                $message = "成功取消推荐 {$count} 个商品";
                break;
                
            case 'update_sort':
                $sortOrder = $request->sort_order ?? 0;
                foreach ($productIds as $productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $product->update(['sort_order' => $sortOrder]);
                        $count++;
                    }
                }
                $message = "成功更新 {$count} 个商品的排序";
                break;
        }

        return redirect()->route('admin.recommend-products.index')
            ->with('success', $message);
    }

    /**
     * 更新排序
     */
    public function updateSort(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->products as $productData) {
            Product::where('id', $productData['id'])
                ->update(['sort_order' => $productData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * 获取推荐商品统计
     */
    public function statistics(Request $request)
    {
        $query = Product::where('is_featured', true);

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $stats = [
            'total_recommend_products' => $query->count(),
            'active_recommend_products' => $query->clone()->where('status', 'active')->count(),
            'inactive_recommend_products' => $query->clone()->where('status', 'inactive')->count(),
            'total_views' => $query->clone()->sum('views_count'),
            'total_sales' => $query->clone()->sum('sales_count'),
            'avg_price' => $query->clone()->avg('price'),
            'new_recommend_today' => Product::where('is_featured', true)
                ->whereDate('updated_at', today())->count(),
            'new_recommend_this_month' => Product::where('is_featured', true)
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * 自动推荐商品
     */
    public function autoRecommend(Request $request)
    {
        $request->validate([
            'criteria' => 'required|string|in:best_selling,highest_rated,newest,most_viewed',
            'limit' => 'required|integer|min:1|max:50',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $query = Product::where('is_featured', false)
            ->where('status', 'active');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        switch ($request->criteria) {
            case 'best_selling':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'highest_rated':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'most_viewed':
                $query->orderBy('views_count', 'desc');
                break;
        }

        $products = $query->limit($request->limit)->get();
        $count = 0;

        foreach ($products as $product) {
            $product->update(['is_featured' => true]);
            $count++;
        }

        return redirect()->route('admin.recommend-products.index')
            ->with('success', "根据 {$request->criteria} 自动推荐了 {$count} 个商品");
    }
}
