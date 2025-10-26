@extends('layouts.merchant')

@section('title', '订单管理')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">订单管理</h1>
            <p class="text-gray-600">管理您的订单状态和发货</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="openExportModal()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-download mr-2"></i>
                导出数据
            </button>
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

    <!-- 统计卡片 -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">总订单</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">待处理</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['confirmed'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">已确认</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['shipped'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">已发货</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['delivered'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">已送达</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-indigo-600">RM{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                <div class="text-sm text-gray-500">总收入</div>
            </div>
        </div>
    </div>
    @endif

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form method="GET" action="/merchant/orders" class="space-y-4">
            <!-- 搜索框 -->
            <div class="flex space-x-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="keyword"
                               value="{{ $searchKeyword }}"
                               placeholder="搜索订单号、客户姓名、邮箱、电话"
                               class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    搜索
                </button>
                <a href="/merchant/orders" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    重置
                </a>
            </div>
            
            <!-- 筛选选项 -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">订单状态</label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">全部状态</option>
                        <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>待处理</option>
                        <option value="confirmed" {{ $selectedStatus === 'confirmed' ? 'selected' : '' }}>已确认</option>
                        <option value="processing" {{ $selectedStatus === 'processing' ? 'selected' : '' }}>处理中</option>
                        <option value="shipped" {{ $selectedStatus === 'shipped' ? 'selected' : '' }}>已发货</option>
                        <option value="delivered" {{ $selectedStatus === 'delivered' ? 'selected' : '' }}>已送达</option>
                        <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>已取消</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">支付状态</label>
                    <select name="payment_status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">全部支付状态</option>
                        <option value="pending" {{ $selectedPaymentStatus === 'pending' ? 'selected' : '' }}>待支付</option>
                        <option value="paid" {{ $selectedPaymentStatus === 'paid' ? 'selected' : '' }}>已支付</option>
                        <option value="failed" {{ $selectedPaymentStatus === 'failed' ? 'selected' : '' }}>支付失败</option>
                        <option value="refunded" {{ $selectedPaymentStatus === 'refunded' ? 'selected' : '' }}>已退款</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">开始日期</label>
                    <input type="date" 
                           name="start_date"
                           value="{{ $startDate }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">结束日期</label>
                    <input type="date" 
                           name="end_date"
                           value="{{ $endDate }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">排序</label>
                    <select name="sort" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="created_at" {{ $selectedSort === 'created_at' ? 'selected' : '' }}>最新订单</option>
                        <option value="order_number" {{ $selectedSort === 'order_number' ? 'selected' : '' }}>订单号</option>
                        <option value="amount_asc" {{ $selectedSort === 'amount_asc' ? 'selected' : '' }}>金额从低到高</option>
                        <option value="amount_desc" {{ $selectedSort === 'amount_desc' ? 'selected' : '' }}>金额从高到低</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- 订单列表 -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                订单列表 
                @if(count($orders) > 0)
                    <span class="text-sm text-gray-500">({{ count($orders) }} 个订单)</span>
                @endif
            </h3>
        </div>
        
        @if(count($orders) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">订单号</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">客户信息</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商品数量</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">订单金额</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">订单状态</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">支付状态</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">创建时间</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="/merchant/orders/{{ $order->id }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $order->order_number }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->customer_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->customer_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->items->count() }} 件商品
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-medium">RM{{ number_format($order->total_amount, 2) }}</div>
                                    @if($order->shipping_fee > 0)
                                        <div class="text-xs text-gray-500">含运费 RM{{ number_format($order->shipping_fee, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($order->status_color === 'yellow') bg-yellow-100 text-yellow-800
                                        @elseif($order->status_color === 'blue') bg-blue-100 text-blue-800
                                        @elseif($order->status_color === 'green') bg-green-100 text-green-800
                                        @elseif($order->status_color === 'red') bg-red-100 text-red-800
                                        @elseif($order->status_color === 'purple') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                        @elseif($order->payment_status === 'refunded') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order->payment_status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/merchant/orders/{{ $order->id }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(in_array($order->status, ['pending', 'confirmed', 'processing']))
                                            <button onclick="updateOrderStatus({{ $order->id }})" 
                                                    class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
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
                <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">暂无订单</h3>
                <p class="text-gray-500 mb-4">您还没有收到任何订单</p>
            </div>
        @endif
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
                        <option value="pending">待处理</option>
                        <option value="confirmed">已确认</option>
                        <option value="processing">处理中</option>
                        <option value="shipped">已发货</option>
                        <option value="delivered">已送达</option>
                        <option value="cancelled">已取消</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">快递单号</label>
                    <input type="text" 
                           id="trackingNumber" 
                           name="tracking_number"
                           placeholder="可选"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">备注</label>
                    <textarea id="notes" 
                              name="notes"
                              rows="3"
                              placeholder="可选"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
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
let currentOrderId = null;

function updateOrderStatus(orderId) {
    currentOrderId = orderId;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    currentOrderId = null;
    document.getElementById('statusForm').reset();
}

document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentOrderId) {
        alert('订单ID不存在');
        return;
    }
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch(`/merchant/orders/${currentOrderId}/status`, {
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

// 导出功能
function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

document.getElementById('exportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // 创建下载链接
    const params = new URLSearchParams(data);
    const downloadUrl = '/merchant/export/orders?' + params.toString();
    
    // 创建隐藏的下载链接
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    closeExportModal();
});

// 点击模态框外部关闭
document.getElementById('exportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeExportModal();
    }
});
</script>

<!-- 导出模态框 -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">导出订单数据</h3>
            <form id="exportForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">导出格式</label>
                    <select id="exportFormat" name="format" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="xlsx">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                        <option value="pdf">PDF (.pdf)</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">日期范围</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" 
                               id="exportStartDate" 
                               name="start_date"
                               class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <input type="date" 
                               id="exportEndDate" 
                               name="end_date"
                               class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">订单状态</label>
                    <select id="exportStatus" name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">全部状态</option>
                        <option value="pending">待处理</option>
                        <option value="confirmed">已确认</option>
                        <option value="processing">处理中</option>
                        <option value="shipped">已发货</option>
                        <option value="delivered">已送达</option>
                        <option value="cancelled">已取消</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeExportModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        取消
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        导出
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection