@extends('layouts.merchant')

@section('title', '商品管理')

@section('content')
<div class="space-y-6">
    <!-- 页面标题 -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">商品管理</h1>
            <p class="text-gray-600 mt-1">管理您店铺中的所有商品</p>
        </div>
        <div class="flex space-x-3">
            <a href="/merchant/select-products" 
               class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                添加商品
            </a>
        </div>
    </div>

    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-box text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">总商品数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">在售商品</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">库存不足</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['low_stock'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">缺货商品</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['out_of_stock'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(isset($error))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">加载失败: {{ $error }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- 搜索和筛选 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form method="GET" action="/merchant/products" class="space-y-4">
            <!-- 搜索框 -->
            <div class="flex space-x-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="keyword"
                               value="{{ $searchKeyword }}"
                               placeholder="搜索商品名称、SKU、描述"
                               class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    搜索
                </button>
                <a href="/merchant/products" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    重置
                </a>
            </div>
            
            <!-- 筛选选项 -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">分类</label>
                    <select name="category" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">全部分类</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">品牌</label>
                    <select name="brand" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">全部品牌</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ $selectedBrand === $brand ? 'selected' : '' }}>{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">状态</label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">全部状态</option>
                        <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>在售</option>
                        <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>停售</option>
                        <option value="low_stock" {{ $selectedStatus === 'low_stock' ? 'selected' : '' }}>库存不足</option>
                        <option value="out_of_stock" {{ $selectedStatus === 'out_of_stock' ? 'selected' : '' }}>缺货</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">排序</label>
                    <select name="sort" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="created_at" {{ $selectedSort === 'created_at' ? 'selected' : '' }}>最新添加</option>
                        <option value="name" {{ $selectedSort === 'name' ? 'selected' : '' }}>商品名称</option>
                        <option value="price_asc" {{ $selectedSort === 'price_asc' ? 'selected' : '' }}>价格从低到高</option>
                        <option value="price_desc" {{ $selectedSort === 'price_desc' ? 'selected' : '' }}>价格从高到低</option>
                        <option value="stock" {{ $selectedSort === 'stock' ? 'selected' : '' }}>库存数量</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        应用筛选
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 批量操作 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4" id="bulkActions" style="display: none;">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">已选择 <span id="selectedCount">0</span> 个商品</span>
                <div class="flex space-x-2">
                    <button onclick="bulkAction('activate')" 
                            class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        激活
                    </button>
                    <button onclick="bulkAction('deactivate')" 
                            class="px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                        停用
                    </button>
                    <button onclick="bulkAction('delete')" 
                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                        删除
                    </button>
                </div>
            </div>
            <button onclick="clearSelection()" 
                    class="text-sm text-gray-500 hover:text-gray-700">
                取消选择
            </button>
        </div>
    </div>

    <!-- 商品列表 -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                商品列表 
                @if(count($products) > 0)
                    <span class="text-sm text-gray-500">({{ count($products) }} 个商品)</span>
                @endif
            </h3>
        </div>
        
        @if(count($products) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商品</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">售价</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">库存</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" 
                                           class="product-checkbox rounded" 
                                           value="{{ $product->id }}"
                                           onchange="updateSelection()">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <img class="h-12 w-12 rounded-lg object-cover" 
                                                 src="{{ $product->main_image }}" 
                                                 alt="{{ $product->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $product->name }}
                                                @if($product->is_featured)
                                                    <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">推荐</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $product->category }} | {{ $product->brand }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->sku }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                    <div class="font-medium">RM{{ number_format($product->sale_price, 2) }}</div>
                                    @if($product->cost_price)
                                        <div class="text-xs text-gray-500">
                                            成本: RM{{ number_format($product->cost_price, 2) }}
                                            <span class="text-green-600">({{ $product->profit_margin }}%)</span>
                                        </div>
                                    @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        <div class="font-medium">{{ $product->stock }}</div>
                                        <div class="text-xs {{ $product->stock_status_color }}">
                                            {{ $product->stock_status_text }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->is_active)
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            在售
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                            停售
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/merchant/products/edit/{{ $product->id }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteProduct({{ $product->id }})" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- 分页 -->
            @if(isset($pagination) && $pagination->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pagination->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">暂无商品</h3>
                <p class="text-gray-500 mb-4">您还没有添加任何商品到店铺</p>
                <a href="/merchant/select-products" 
                   class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i>
                    去选品
                </a>
            </div>
        @endif
    </div>
</div>

<script>
// 全选功能
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelection();
}

// 更新选择状态
function updateSelection() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    selectedCount.textContent = checkedBoxes.length;
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
    
    // 更新全选状态
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = checkedBoxes.length === checkboxes.length;
    selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < checkboxes.length;
}

// 清除选择
function clearSelection() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    selectAll.checked = false;
    selectAll.indeterminate = false;
    
    updateSelection();
}

// 批量操作
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
    const productIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (productIds.length === 0) {
        alert('请选择要操作的商品');
        return;
    }
    
    let confirmMessage = '';
    switch (action) {
        case 'activate':
            confirmMessage = `确定要激活选中的 ${productIds.length} 个商品吗？`;
            break;
        case 'deactivate':
            confirmMessage = `确定要停用选中的 ${productIds.length} 个商品吗？`;
            break;
        case 'delete':
            confirmMessage = `确定要删除选中的 ${productIds.length} 个商品吗？此操作不可撤销！`;
            break;
    }
    
    if (confirm(confirmMessage)) {
        fetch('/merchant/products/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: action,
                product_ids: productIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('操作失败: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('操作失败，请稍后重试');
        });
    }
}

// 删除商品
function deleteProduct(productId) {
    if (confirm('确定要删除这个商品吗？此操作不可撤销！')) {
        fetch(`/merchant/products/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('删除失败: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('删除失败，请稍后重试');
        });
    }
}
</script>
@endsection