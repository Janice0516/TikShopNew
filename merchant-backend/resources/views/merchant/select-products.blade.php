@extends('layouts.merchant')

@section('title', '平台选品')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">平台选品</h1>
            <p class="text-gray-600">从平台选择商品添加到您的店铺</p>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">加载失败</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $error }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 搜索和筛选 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form method="GET" action="/merchant/select-products" class="space-y-4">
            <!-- 搜索框 -->
            <div class="flex space-x-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="keyword"
                               value="{{ $searchKeyword }}"
                               placeholder="搜索商品名称、描述、SKU"
                               class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    搜索
                </button>
                <a href="/merchant/select-products" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    重置
                </a>
            </div>
            
            <!-- 筛选选项 -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">排序</label>
                    <select name="sort" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="rating" {{ $selectedSort === 'rating' ? 'selected' : '' }}>按评分</option>
                        <option value="sales" {{ $selectedSort === 'sales' ? 'selected' : '' }}>按销量</option>
                        <option value="price_asc" {{ $selectedSort === 'price_asc' ? 'selected' : '' }}>价格从低到高</option>
                        <option value="price_desc" {{ $selectedSort === 'price_desc' ? 'selected' : '' }}>价格从高到低</option>
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

    <!-- 商品列表 -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                平台商品 
                <span class="text-sm text-gray-500">({{ count($products) }} 个商品)</span>
            </h3>
        </div>
        
        @if(count($products) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($products as $product)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- 商品图片 -->
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                            <img src="{{ $product->main_image }}" alt="{{ $product->name }}" 
                                 class="w-full h-48 object-cover">
                        </div>
                        
                        <!-- 商品信息 -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-medium text-gray-900 line-clamp-2 flex-1">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-center ml-2">
                                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                                    <span class="text-sm text-gray-600 ml-1">{{ $product->rating }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 mb-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $product->category }}</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">{{ $product->brand }}</span>
                            </div>
                            
                            <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                                {{ $product->description }}
                            </p>
                            
                            <!-- 价格信息 -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">平台价格:</span>
                                    <span class="text-lg font-semibold text-gray-900">RM{{ number_format($product->price, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">成本价:</span>
                                    <span class="text-sm text-gray-600">RM{{ number_format($product->cost_price, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">利润率:</span>
                                    <span class="text-sm text-green-600 font-medium">{{ $product->profit_margin }}%</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">销量:</span>
                                    <span class="text-sm text-gray-600">{{ number_format($product->sales) }}</span>
                                </div>
                            </div>
                            
                            <!-- 售价设置 -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">设置售价</label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">RM</span>
                                    <input type="number" 
                                           step="0.01" 
                                           min="0"
                                           id="price_{{ $product->id }}"
                                           value="{{ $product->suggested_price }}"
                                           class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">建议售价: RM{{ $product->suggested_price }}</p>
                            </div>
                            
                            <!-- 操作按钮 -->
                            <div class="flex space-x-2">
                                <button onclick="addToStore({{ $product->id }})" 
                                        class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-plus mr-1"></i>
                                    添加到店铺
                                </button>
                                <button onclick="viewProduct({{ $product->id }})" 
                                        class="px-3 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- 分页 -->
            @if(isset($pagination) && $pagination->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pagination->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-shopping-bag text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">暂无商品</h3>
                <p class="text-gray-500 mb-4">没有找到符合条件的商品</p>
                <a href="/merchant/select-products" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    重新搜索
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function addToStore(productId) {
    const priceInput = document.getElementById('price_' + productId);
    const salePrice = priceInput.value;
    
    if (!salePrice || parseFloat(salePrice) <= 0) {
        alert('请输入有效的售价');
        return;
    }
    
           if (confirm('确定要将此商品添加到您的店铺吗？\n售价: RM' + salePrice)) {
        // 发送AJAX请求
        fetch('/merchant/add-product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                sale_price: salePrice
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // 可以在这里更新UI，比如禁用按钮或显示已添加状态
                const button = document.querySelector(`button[onclick="addToStore(${productId})"]`);
                if (button) {
                    button.textContent = '已添加';
                    button.disabled = true;
                    button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                    button.classList.add('bg-gray-400', 'cursor-not-allowed');
                }
            } else {
                alert('添加失败: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('添加失败，请稍后重试');
        });
    }
}

function viewProduct(productId) {
    // 实现商品详情查看
    alert('查看商品详情: ' + productId);
}
</script>
@endsection
