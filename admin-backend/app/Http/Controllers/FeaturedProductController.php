<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\FeaturedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeaturedProductController extends Controller
{
    /**
     * 显示特色商品管理页面
     */
    public function index()
    {
        $savingsProducts = FeaturedProduct::where('type', 'savings')
            ->with('product')
            ->orderBy('sort_order')
            ->get();
            
        $topDealsProducts = FeaturedProduct::where('type', 'top_deals')
            ->with('product')
            ->orderBy('sort_order')
            ->get();
            
        $allProducts = Product::with('category', 'merchant')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.featured-products.index', compact(
            'savingsProducts', 
            'topDealsProducts', 
            'allProducts'
        ));
    }

    /**
     * 添加商品到特色区域
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:savings,top_deals',
            'sort_order' => 'nullable|integer|min:0',
            'custom_title' => 'nullable|string|max:255',
            'custom_price' => 'nullable|numeric|min:0',
            'custom_original_price' => 'nullable|numeric|min:0',
            'custom_rating' => 'nullable|numeric|min:0|max:5',
            'custom_sales_count' => 'nullable|integer|min:0',
        ]);

        // 检查是否已存在
        $existing = FeaturedProduct::where('product_id', $request->product_id)
            ->where('type', $request->type)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', '该商品已在此区域中');
        }

        // 获取最大排序号
        $maxOrder = FeaturedProduct::where('type', $request->type)->max('sort_order') ?? 0;

        FeaturedProduct::create([
            'product_id' => $request->product_id,
            'type' => $request->type,
            'sort_order' => $request->sort_order ?? ($maxOrder + 1),
            'custom_title' => $request->custom_title,
            'custom_price' => $request->custom_price,
            'custom_original_price' => $request->custom_original_price,
            'custom_rating' => $request->custom_rating,
            'custom_sales_count' => $request->custom_sales_count,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', '商品已添加到特色区域');
    }

    /**
     * 更新特色商品
     */
    public function update(Request $request, FeaturedProduct $featuredProduct)
    {
        $request->validate([
            'sort_order' => 'nullable|integer|min:0',
            'custom_title' => 'nullable|string|max:255',
            'custom_price' => 'nullable|numeric|min:0',
            'custom_original_price' => 'nullable|numeric|min:0',
            'custom_rating' => 'nullable|numeric|min:0|max:5',
            'custom_sales_count' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $featuredProduct->update($request->all());

        return redirect()->back()->with('success', '特色商品已更新');
    }

    /**
     * 删除特色商品
     */
    public function destroy(FeaturedProduct $featuredProduct)
    {
        $featuredProduct->delete();

        return redirect()->back()->with('success', '商品已从特色区域移除');
    }

    /**
     * 批量更新排序
     */
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:featured_products,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                FeaturedProduct::where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * 切换商品状态
     */
    public function toggleStatus(FeaturedProduct $featuredProduct)
    {
        $featuredProduct->update([
            'is_active' => !$featuredProduct->is_active
        ]);

        $status = $featuredProduct->is_active ? '启用' : '禁用';
        return redirect()->back()->with('success', "商品已{$status}");
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:featured_products,id',
        ]);

        $count = 0;
        switch ($request->action) {
            case 'activate':
                $count = FeaturedProduct::whereIn('id', $request->ids)
                    ->update(['is_active' => true]);
                break;
            case 'deactivate':
                $count = FeaturedProduct::whereIn('id', $request->ids)
                    ->update(['is_active' => false]);
                break;
            case 'delete':
                $count = FeaturedProduct::whereIn('id', $request->ids)->delete();
                break;
        }

        return redirect()->back()->with('success', "已处理 {$count} 个商品");
    }
}
