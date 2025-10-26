@extends('admin.layouts.app')

@section('title', '手动推荐商品')
@section('page-title', '手动推荐商品')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">手动推荐商品</h1>
            <p class="text-gray-600">选择商品进行推荐</p>
        </div>
        <div>
            <a href="{{ route('admin.recommend-products.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>
                返回列表
            </a>
        </div>
    </div>

    <!-- 推荐表单 -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.recommend-products.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 推荐设置 -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">推荐设置</h3>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">推荐类型 <span class="text-red-500">*</span></label>
                    <select name="recommend_type" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('recommend_type') border-red-500 @enderror">
                        <option value="">请选择推荐类型</option>
                        <option value="manual" {{ old('recommend_type') === 'manual' ? 'selected' : '' }}>手动推荐</option>
                        <option value="auto" {{ old('recommend_type') === 'auto' ? 'selected' : '' }}>自动推荐</option>
                    </select>
                    @error('recommend_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">推荐位置</label>
                    <input type="text" 
                           name="position" 
                           value="{{ old('position') }}"
                           placeholder="首页轮播、分类推荐等"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror">
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">排序值</label>
                    <input type="number" 
                           name="sort_order" 
                           value="{{ old('sort_order', 0) }}"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('sort_order') border-red-500 @enderror">
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">开始时间</label>
                    <input type="datetime-local" 
                           name="start_date" 
                           value="{{ old('start_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">结束时间</label>
                    <input type="datetime-local" 
                           name="end_date" 
                           value="{{ old('end_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 商品选择 -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">选择商品</h3>
                
                <!-- 筛选选项 -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">按分类筛选</label>
                        <select id="category-filter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">全部分类</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">按商家筛选</label>
                        <select id="merchant-filter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">全部商家</option>
                            @foreach($merchants as $merchant)
                                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">搜索商品</label>
                        <input type="text" 
                               id="product-search"
                               placeholder="商品名称、SKU..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- 商品列表 -->
                <div class="border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <input type="checkbox" 
                                   id="select-all-products" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="select-all-products" class="text-sm font-medium text-gray-700">全选</label>
                        </div>
                        <div class="text-sm text-gray-500">
                            已选择 <span id="selected-count">0</span> 个商品
                        </div>
                    </div>
                    
                    <div id="products-list" class="space-y-3">
                        @foreach($products as $product)
                        <div class="product-item border border-gray-200 rounded-lg p-4 hover:bg-gray-50" 
                             data-category="{{ $product->category_id }}" 
                             data-merchant="{{ $product->merchant_id }}"
                             data-name="{{ strtolower($product->name) }}"
                             data-sku="{{ strtolower($product->sku) }}">
                            <div class="flex items-center space-x-4">
                                <input type="checkbox" 
                                       name="product_ids[]" 
                                       value="{{ $product->id }}"
                                       class="product-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                
                                <div class="flex-shrink-0 h-16 w-16">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ $product->images[0] }}" 
                                             alt="{{ $product->name }}" 
                                             class="h-16 w-16 rounded-lg object-cover">
                                    @else
                                        <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                                    <div class="text-sm text-gray-500">
                                        分类: {{ $product->category->name ?? '未分类' }} | 
                                        商家: {{ $product->merchant->name ?? '未知商家' }} | 
                                        价格: RM {{ number_format($product->price, 2) }}
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    @if($product->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            上架
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            下架
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 保存按钮 -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.recommend-products.index') }}" 
                   class="px-6 py-3 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    取消
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-star mr-2"></i>
                    推荐选中商品
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-products');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const productItems = document.querySelectorAll('.product-item');
    const selectedCountSpan = document.getElementById('selected-count');
    
    // 全选功能
    selectAllCheckbox.addEventListener('change', function() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });
    
    // 单个复选框变化
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            updateSelectAllState();
        });
    });
    
    // 更新选中数量
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount;
    }
    
    // 更新全选状态
    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        const totalCount = productCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }
    
    // 分类筛选
    document.getElementById('category-filter').addEventListener('change', function() {
        filterProducts();
    });
    
    // 商家筛选
    document.getElementById('merchant-filter').addEventListener('change', function() {
        filterProducts();
    });
    
    // 搜索筛选
    document.getElementById('product-search').addEventListener('input', function() {
        filterProducts();
    });
    
    // 筛选商品
    function filterProducts() {
        const categoryFilter = document.getElementById('category-filter').value;
        const merchantFilter = document.getElementById('merchant-filter').value;
        const searchTerm = document.getElementById('product-search').value.toLowerCase();
        
        productItems.forEach(item => {
            const category = item.dataset.category;
            const merchant = item.dataset.merchant;
            const name = item.dataset.name;
            const sku = item.dataset.sku;
            
            let show = true;
            
            if (categoryFilter && category !== categoryFilter) {
                show = false;
            }
            
            if (merchantFilter && merchant !== merchantFilter) {
                show = false;
            }
            
            if (searchTerm && !name.includes(searchTerm) && !sku.includes(searchTerm)) {
                show = false;
            }
            
            item.style.display = show ? 'block' : 'none';
        });
    }
    
    // 表单提交验证
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('请至少选择一个商品');
            return false;
        }
        
        if (!confirm(`确定要推荐选中的 ${checkedBoxes.length} 个商品吗？`)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection
