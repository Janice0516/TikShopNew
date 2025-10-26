@extends('layouts.merchant')

@section('title', '订单详情')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">订单详情</h1>
            <p class="text-gray-600">订单号: {{ $order->order_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="/merchant/orders" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i>
                返回订单列表
            </a>
            @if(in_array($order->status, ['pending', 'confirmed', 'processing']))
                <button onclick="updateOrderStatus()" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-edit mr-2"></i>
                    更新状态
                </button>
            @endif
        </div>
    </div>

    <!-- 订单状态卡片 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-indigo-600 text-xl"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $order->order_number }}</h3>
                    <p class="text-sm text-gray-500">创建时间: {{ $order->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                    @if($order->status_color === 'yellow') bg-yellow-100 text-yellow-800
                    @elseif($order->status_color === 'blue') bg-blue-100 text-blue-800
                    @elseif($order->status_color === 'green') bg-green-100 text-green-800
                    @elseif($order->status_color === 'red') bg-red-100 text-red-800
                    @elseif($order->status_color === 'purple') bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $order->status_text }}
                </span>
                <p class="text-sm text-gray-500 mt-1">订单状态</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 左侧：订单信息 -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 客户信息 -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">客户信息</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">客户姓名</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">联系电话</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->customer_phone ?? '未提供' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">邮箱地址</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->customer_email ?? '未提供' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">客户ID</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->customer_id ?? '游客' }}</p>
                    </div>
                </div>
            </div>

            <!-- 配送信息 -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">配送信息</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">收货人</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->shipping_address['name'] ?? '未提供' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">联系电话</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->shipping_address['phone'] ?? '未提供' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">收货地址</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $order->shipping_address['address'] ?? '未提供' }}
                            @if(isset($order->shipping_address['city']))
                                <br>{{ $order->shipping_address['city'] }}
                            @endif
                            @if(isset($order->shipping_address['state']))
                                {{ $order->shipping_address['state'] }}
                            @endif
                            @if(isset($order->shipping_address['zip']))
                                {{ $order->shipping_address['zip'] }}
                            @endif
                            @if(isset($order->shipping_address['country']))
                                <br>{{ $order->shipping_address['country'] }}
                            @endif
                        </p>
                    </div>
                    @if($order->shipping_method)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">配送方式</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->shipping_method }}</p>
                    </div>
                    @endif
                    @if($order->tracking_number)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">快递单号</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $order->tracking_number }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 商品列表 -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">商品列表</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商品</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">单价</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">数量</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">小计</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($item->product_image)
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <img class="h-12 w-12 rounded-lg object-cover" src="{{ $item->product_image }}" alt="{{ $item->product_name }}">
                                                </div>
                                            @else
                                                <div class="flex-shrink-0 h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                        {{ $item->product_sku }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        RM{{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        RM{{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 订单备注 -->
            @if($order->notes || $order->customer_notes)
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">订单备注</h3>
                @if($order->customer_notes)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">客户备注</label>
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $order->customer_notes }}</p>
                </div>
                @endif
                @if($order->notes)
                <div>
                    <label class="block text-sm font-medium text-gray-700">商家备注</label>
                    <p class="mt-1 text-sm text-gray-900 bg-blue-50 p-3 rounded-md">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- 右侧：订单摘要 -->
        <div class="space-y-6">
            <!-- 订单摘要 -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">订单摘要</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">商品小计</span>
                        <span class="text-sm text-gray-900">RM{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->shipping_fee > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">运费</span>
                        <span class="text-sm text-gray-900">RM{{ number_format($order->shipping_fee, 2) }}</span>
                    </div>
                    @endif
                    @if($order->tax_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">税费</span>
                        <span class="text-sm text-gray-900">RM{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">优惠</span>
                        <span class="text-sm text-red-600">-RM{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-base font-medium text-gray-900">订单总额</span>
                            <span class="text-base font-medium text-gray-900">RM{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 支付信息 -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">支付信息</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">支付状态</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($order->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                            @elseif($order->payment_status === 'refunded') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $order->payment_status_text }}
                        </span>
                    </div>
                    @if($order->payment_method)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">支付方式</span>
                        <span class="text-sm text-gray-900">{{ $order->payment_method }}</span>
                    </div>
                    @endif
                    @if($order->payment_reference)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">支付单号</span>
                        <span class="text-sm text-gray-900 font-mono">{{ $order->payment_reference }}</span>
                    </div>
                    @endif
                    @if($order->paid_at)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">支付时间</span>
                        <span class="text-sm text-gray-900">{{ $order->paid_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 订单时间线 -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">订单时间线</h3>
                <div class="flow-root">
                    <ul class="-mb-8">
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-plus text-white text-xs"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">订单创建</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $order->created_at->format('m-d H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                        @if($order->paid_at)
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-credit-card text-white text-xs"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">支付完成</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $order->paid_at->format('m-d H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif
                        
                        @if($order->shipped_at)
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-truck text-white text-xs"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">已发货</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $order->shipped_at->format('m-d H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif
                        
                        @if($order->delivered_at)
                        <li>
                            <div class="relative">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">已送达</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $order->delivered_at->format('m-d H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 订单状态更新模态框 -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">更新订单状态</h3>
            <form id="statusForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">订单状态</label>
                    <select id="newStatus" name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>待处理</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>已确认</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>处理中</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>已发货</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>已送达</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>已取消</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">快递单号</label>
                    <input type="text" 
                           id="trackingNumber" 
                           name="tracking_number"
                           value="{{ $order->tracking_number }}"
                           placeholder="可选"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">备注</label>
                    <textarea id="notes" 
                              name="notes"
                              rows="3"
                              placeholder="可选"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ $order->notes }}</textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeStatusModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        取消
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        更新状态
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateOrderStatus() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch(`/merchant/orders/{{ $order->id }}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('更新失败: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('更新失败，请稍后重试');
    });
    
    closeStatusModal();
});

// 点击模态框外部关闭
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});
</script>
@endsection
