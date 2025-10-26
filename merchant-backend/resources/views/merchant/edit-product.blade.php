@extends('layouts.merchant')

@section('title', '编辑商品')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- 页面标题 -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">编辑商品</h1>
            <p class="text-gray-600 mt-1">修改商品信息和设置</p>
        </div>
        <a href="/merchant/products" 
           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-arrow-left mr-2"></i>
            返回商品列表
        </a>
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

    <!-- 编辑表单 -->
    <form method="POST" action="/merchant/products/edit/{{ $product->id }}" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">基本信息</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 商品名称 -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        商品名称 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $product->name) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="sku" 
                           name="sku" 
                           value="{{ old('sku', $product->sku) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 分类 -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">分类</label>
                    <input type="text" 
                           id="category" 
                           name="category" 
                           value="{{ old('category', $product->category) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 品牌 -->
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">品牌</label>
                    <input type="text" 
                           id="brand" 
                           name="brand" 
                           value="{{ old('brand', $product->brand) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 商品描述 -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">商品描述</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 价格和库存 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">价格和库存</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 售价 -->
                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">
                        售价 <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">RM</span>
                        </div>
                        <input type="number" 
                               id="sale_price" 
                               name="sale_price" 
                               value="{{ old('sale_price', $product->sale_price) }}"
                               step="0.01"
                               min="0.01"
                               required
                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @error('sale_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 成本价 -->
                <div>
                    <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">成本价</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">RM</span>
                        </div>
                        <input type="number" 
                               id="cost_price" 
                               name="cost_price" 
                               value="{{ old('cost_price', $product->cost_price) }}"
                               step="0.01"
                               min="0"
                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @error('cost_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 利润率显示 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">利润率</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                        <span class="text-sm text-gray-900" id="profitMargin">
                            {{ $product->profit_margin }}%
                        </span>
                    </div>
                </div>

                <!-- 库存 -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                        库存数量 <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="stock" 
                           name="stock" 
                           value="{{ old('stock', $product->stock) }}"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 最低库存 -->
                <div>
                    <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-2">
                        最低库存 <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="min_stock" 
                           name="min_stock" 
                           value="{{ old('min_stock', $product->min_stock) }}"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('min_stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 库存状态显示 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">库存状态</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                        <span class="text-sm {{ $product->stock_status_color }}" id="stockStatus">
                            {{ $product->stock_status_text }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 商品状态 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">商品状态</h3>
            
            <div class="space-y-4">
                <!-- 销售状态 -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        在售状态
                    </label>
                </div>

                <!-- 推荐商品 -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_featured" 
                           name="is_featured" 
                           value="1"
                           {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                        推荐商品
                    </label>
                </div>
            </div>
        </div>

        <!-- 备注 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">备注</h3>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">备注信息</label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3"
                          placeholder="添加商品备注信息..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $product->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 操作按钮 -->
        <div class="flex items-center justify-end space-x-4">
            <a href="/merchant/products" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                取消
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-save mr-2"></i>
                保存更改
            </button>
        </div>
    </form>
</div>

<script>
// 计算利润率
function calculateProfitMargin() {
    const salePrice = parseFloat(document.getElementById('sale_price').value) || 0;
    const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
    
    if (costPrice > 0) {
        const margin = ((salePrice - costPrice) / costPrice) * 100;
        document.getElementById('profitMargin').textContent = margin.toFixed(2) + '%';
    } else {
        document.getElementById('profitMargin').textContent = '0%';
    }
}

// 更新库存状态
function updateStockStatus() {
    const stock = parseInt(document.getElementById('stock').value) || 0;
    const minStock = parseInt(document.getElementById('min_stock').value) || 0;
    
    let status = '';
    let color = '';
    
    if (stock <= 0) {
        status = '缺货';
        color = 'text-red-600';
    } else if (stock <= minStock) {
        status = '库存不足';
        color = 'text-yellow-600';
    } else {
        status = '有库存';
        color = 'text-green-600';
    }
    
    const statusElement = document.getElementById('stockStatus');
    statusElement.textContent = status;
    statusElement.className = 'text-sm ' + color;
}

// 绑定事件
document.getElementById('sale_price').addEventListener('input', calculateProfitMargin);
document.getElementById('cost_price').addEventListener('input', calculateProfitMargin);
document.getElementById('stock').addEventListener('input', updateStockStatus);
document.getElementById('min_stock').addEventListener('input', updateStockStatus);

// 初始化
calculateProfitMargin();
updateStockStatus();
</script>
@endsection
