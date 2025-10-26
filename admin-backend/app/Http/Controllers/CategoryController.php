<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * 显示分类列表
     */
    public function index(Request $request)
    {
        $query = Category::with('parent');

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按状态筛选
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // 按父分类筛选
        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // 排序
        $query->ordered();

        $categories = $query->paginate(20)->withQueryString();

        // 获取所有分类用于筛选
        $allCategories = Category::ordered()->get();

        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

    /**
     * 显示创建分类表单
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * 存储新分类
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        
        // 自动生成slug
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = $request->boolean('is_active');

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', '分类创建成功');
    }

    /**
     * 显示分类详情
     */
    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'products']);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * 显示编辑分类表单
     */
    public function edit(Category $category)
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * 更新分类
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        
        // 自动生成slug
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', '分类更新成功');
    }

    /**
     * 删除分类
     */
    public function destroy(Category $category)
    {
        // 检查是否有子分类
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', '该分类下还有子分类，无法删除');
        }

        // 检查是否有商品
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', '该分类下还有商品，无法删除');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', '分类删除成功');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $categoryIds = $request->category_ids;

        switch ($request->action) {
            case 'activate':
                Category::whereIn('id', $categoryIds)->update(['is_active' => true]);
                $message = '分类已批量激活';
                break;
            case 'deactivate':
                Category::whereIn('id', $categoryIds)->update(['is_active' => false]);
                $message = '分类已批量停用';
                break;
            case 'delete':
                // 检查是否有子分类或商品
                $categories = Category::whereIn('id', $categoryIds)->get();
                foreach ($categories as $category) {
                    if ($category->children()->count() > 0 || $category->products()->count() > 0) {
                        return redirect()->route('admin.categories.index')
                            ->with('error', '部分分类下还有子分类或商品，无法删除');
                    }
                }
                Category::whereIn('id', $categoryIds)->delete();
                $message = '分类已批量删除';
                break;
        }

        return redirect()->route('admin.categories.index')
            ->with('success', $message);
    }

    /**
     * 更新排序
     */
    public function updateSort(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->categories as $categoryData) {
            Category::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
