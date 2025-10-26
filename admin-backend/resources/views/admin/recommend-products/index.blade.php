@extends('admin.layouts.app')

@section('title', '推荐商品管理')
@section('page-title', '推荐商品管理')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">推荐商品管理</h1>
            <p class="text-gray-600">管理平台推荐商品</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="openAutoRecommendModal()" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-magic mr-2"></i>
                自动推荐
            </button>
            <a href="{{ route('admin.recommend-products.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                手动推荐
            </a>
        </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">搜索</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="商品名称、SKU..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">分类</label>
                <select name="category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部分类</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">商家</label>
                <select name="merchant_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部商家</option>
                    @foreach($merchants as $merchant)
                        <option value="{{ $merchant->id }}" {{ request('merchant_id') == $merchant->id ? 'selected' : '' }}>
                            {{ $merchant->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">状态</label>
                <select name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部状态</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>上架</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>下架</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">最低价格</label>
                <input type="number" 
                       name="min_price" 
                       value="{{ request('min_price') }}"
                       placeholder="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">最高价格</label>
                <input type="number" 
                       name="max_price" 
                       value="{{ request('max_price') }}"
                       placeholder="999999"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="md:col-span-6 flex justify-end">
                <button type="submit" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-search mr-2"></i>
                    搜索
                </button>
            </div>
        </form>
    </div>

    <!-- 批量操作 -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <input type="checkbox" 
                           id="select-all" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="select-all" class="text-sm font-medium text-gray-700">全选</label>
                    
                    <div class="flex items-center space-x-2">
                        <select id="bulk-action" 
                                class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">批量操作</option>
                            <option value="cancel_recommend">取消推荐</option>
                            <option value="update_sort">更新排序</option>
                        </select>
                        <button type="button" 
                                id="apply-bulk-action"
                                class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            应用
                        </button>
                    </div>
                </div>
                
                <div class="text-sm text-gray-500">
                    共 {{ $products->total() }} 个推荐商品
                </div>
            </div>
        </div>

        <!-- 推荐商品列表 -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商品信息</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">分类</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商家</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">价格</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">排序</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">推荐时间</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                   name="product_ids[]" 
                                   value="{{ $product->id }}"
                                   class="product-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
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
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                                    <div class="text-sm text-gray-500">库存: {{ $product->stock }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $product->category->name ?? '未分类' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->merchant->name ?? '未知商家' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">RM {{ number_format($product->price, 2) }}</div>
                            @if($product->cost_price)
                                <div class="text-sm text-gray-500">成本: RM {{ number_format($product->cost_price, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    上架
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    下架
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <input type="number" 
                                       value="{{ $product->sort_order }}"
                                       onchange="updateSort({{ $product->id }}, this.value)"
                                       class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->updated_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.recommend-products.show', $product) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="查看详情">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.recommend-products.edit', $product) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="编辑">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('admin.recommend-products.destroy', $product) }}" 
                                      class="inline"
                                      onsubmit="return confirm('确定要取消推荐这个商品吗？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" title="取消推荐">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-star text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">暂无推荐商品</p>
                                <p class="text-sm">还没有任何推荐商品</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 批量操作表单 -->
<form id="bulk-action-form" method="POST" action="{{ route('admin.recommend-products.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <input type="hidden" name="sort_order" id="bulk-sort-order">
    <div id="bulk-product-ids"></div>
</form>

<!-- 自动推荐模态框 -->
<div id="auto-recommend-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">自动推荐商品</h3>
                <button onclick="closeAutoRecommendModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="auto-recommend-form" method="POST" action="{{ route('admin.recommend-products.auto-recommend') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">推荐标准 <span class="text-red-500">*</span></label>
                    <select name="criteria" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">请选择推荐标准</option>
                        <option value="best_selling">最热销</option>
                        <option value="highest_rated">最高评分</option>
                        <option value="newest">最新上架</option>
                        <option value="most_viewed">最多浏览</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">推荐数量 <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="limit" 
                           value="10"
                           min="1"
                           max="50"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">分类筛选</label>
                    <select name="category_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">全部分类</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeAutoRecommendModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        取消
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        开始推荐
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 全选功能
    const selectAllCheckbox = document.getElementById('select-all');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // 单个复选框变化时更新全选状态
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === productCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < productCheckboxes.length;
        });
    });
    
    // 批量操作
    document.getElementById('apply-bulk-action').addEventListener('click', function() {
        const action = document.getElementById('bulk-action').value;
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        
        if (!action) {
            alert('请选择批量操作');
            return;
        }
        
        if (checkedBoxes.length === 0) {
            alert('请选择要操作的商品');
            return;
        }
        
        if (action === 'update_sort') {
            const sortOrder = prompt('请输入排序值:', '0');
            if (sortOrder === null) return;
            
            if (isNaN(sortOrder) || sortOrder < 0) {
                alert('请输入有效的排序值');
                return;
            }
            
            document.getElementById('bulk-sort-order').value = sortOrder;
        }
        
        if (!confirm('确定要执行批量操作吗？')) {
            return;
        }
        
        // 设置表单数据
        document.getElementById('bulk-action-input').value = action;
        const bulkProductIds = document.getElementById('bulk-product-ids');
        bulkProductIds.innerHTML = '';
        
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = checkbox.value;
            bulkProductIds.appendChild(input);
        });
        
        // 提交表单
        document.getElementById('bulk-action-form').submit();
    });
});

// 更新排序
function updateSort(productId, sortOrder) {
    fetch('/admin/recommend-products/update-sort', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            products: [{
                id: productId,
                sort_order: parseInt(sortOrder)
            }]
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 可以显示成功提示
        }
    })
    .catch(error => {
        console.error('更新排序失败:', error);
        alert('更新排序失败');
    });
}

// 自动推荐模态框
function openAutoRecommendModal() {
    document.getElementById('auto-recommend-modal').classList.remove('hidden');
}

function closeAutoRecommendModal() {
    document.getElementById('auto-recommend-modal').classList.add('hidden');
    document.getElementById('auto-recommend-form').reset();
}

// 点击模态框外部关闭
document.getElementById('auto-recommend-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAutoRecommendModal();
    }
});
</script>
@endsection
