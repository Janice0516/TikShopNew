<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * 显示商品列表
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'merchant']);

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // 按分类筛选
        if ($request->filled('category_id')) {
            $query->category($request->category_id);
        }

        // 按商家筛选
        if ($request->filled('merchant_id')) {
            $query->merchant($request->merchant_id);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $categories = Category::active()->ordered()->get();
        $statuses = [
            'active' => '上架',
            'inactive' => '下架',
            'draft' => '草稿',
        ];

        return view('admin.products.index', compact('products', 'categories', 'statuses'));
    }

    /**
     * 显示创建商品表单
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        $merchants = \App\Models\User::where('type', 'merchant')->get();
        return view('admin.products.create', compact('categories', 'merchants'));
    }

    /**
     * 存储新商品
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
            'category_id' => 'required|exists:categories,id',
            'merchant_id' => 'nullable|exists:users,id',
            'weight' => 'nullable|numeric|min:0',
            'brand' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->boolean('is_featured');

        // 处理图片
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $data['images'] = $images;
        }

        // 处理变体
        if ($request->filled('variants')) {
            $data['variants'] = json_decode($request->variants, true);
        }

        // 处理规格
        if ($request->has('specifications')) {
            $specifications = [];
            foreach ($request->specifications as $spec) {
                if (!empty($spec['key']) && !empty($spec['value'])) {
                    $specifications[] = $spec;
                }
            }
            $data['specifications'] = $specifications;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', '商品创建成功');
    }

    /**
     * 显示商品详情
     */
    public function show(Product $product)
    {
        $product->load(['category', 'merchant']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * 显示编辑商品表单
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->ordered()->get();
        $merchants = \App\Models\User::where('type', 'merchant')->get();
        return view('admin.products.edit', compact('product', 'categories', 'merchants'));
    }

    /**
     * 更新商品
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
            'category_id' => 'required|exists:categories,id',
            'merchant_id' => 'nullable|exists:users,id',
            'weight' => 'nullable|numeric|min:0',
            'brand' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->boolean('is_featured');

        // 处理图片
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $data['images'] = $images;
        }

        // 处理变体
        if ($request->filled('variants')) {
            $data['variants'] = json_decode($request->variants, true);
        }

        // 处理规格
        if ($request->has('specifications')) {
            $specifications = [];
            foreach ($request->specifications as $spec) {
                if (!empty($spec['key']) && !empty($spec['value'])) {
                    $specifications[] = $spec;
                }
            }
            $data['specifications'] = $specifications;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', '商品更新成功');
    }

    /**
     * 删除商品
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', '商品删除成功');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $productIds = $request->product_ids;

        switch ($request->action) {
            case 'activate':
                Product::whereIn('id', $productIds)->update(['status' => 'active']);
                $message = '商品已批量上架';
                break;
            case 'deactivate':
                Product::whereIn('id', $productIds)->update(['status' => 'inactive']);
                $message = '商品已批量下架';
                break;
            case 'delete':
                Product::whereIn('id', $productIds)->delete();
                $message = '商品已批量删除';
                break;
        }

        return redirect()->route('admin.products.index')
            ->with('success', $message);
    }
}
