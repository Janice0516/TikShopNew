@extends('layouts.merchant')

@section('title', '提现管理')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">提现管理</h1>
            <p class="text-gray-600">管理您的提现申请和记录</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="openWithdrawalModal()" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                申请提现
            </button>
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-sync-alt mr-2"></i>
                刷新数据
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

    <!-- 余额和统计卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-wallet text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">当前余额</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($currentBalance ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <i class="fas fa-list text-gray-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总提现</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_withdrawals'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">已完成</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['completed_amount'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">待处理</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['pending_amount'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">失败</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['failed_amount'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form method="GET" action="/merchant/withdrawals" class="space-y-4">
            <!-- 筛选选项 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">提现状态</label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">全部状态</option>
                        <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>待处理</option>
                        <option value="processing" {{ $selectedStatus === 'processing' ? 'selected' : '' }}>处理中</option>
                        <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>已完成</option>
                        <option value="failed" {{ $selectedStatus === 'failed' ? 'selected' : '' }}>失败</option>
                        <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>已取消</option>
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
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    筛选
                </button>
                <a href="/merchant/withdrawals" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    重置
                </a>
            </div>
        </form>
    </div>

    <!-- 提现记录列表 -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                提现记录 
                @if(count($withdrawals) > 0)
                    <span class="text-sm text-gray-500">({{ count($withdrawals) }} 条记录)</span>
                @endif
            </h3>
        </div>
        
        @if(count($withdrawals) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">提现单号</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">提现金额</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">手续费</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">实际到账</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">提现方式</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">申请时间</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($withdrawals as $withdrawal)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    {{ $withdrawal->withdrawal_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-medium">RM{{ number_format($withdrawal->amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="text-red-600">-RM{{ number_format($withdrawal->fee, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-medium text-green-600">RM{{ number_format($withdrawal->actual_amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $withdrawal->method_text }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($withdrawal->status_color === 'yellow') bg-yellow-100 text-yellow-800
                                        @elseif($withdrawal->status_color === 'blue') bg-blue-100 text-blue-800
                                        @elseif($withdrawal->status_color === 'green') bg-green-100 text-green-800
                                        @elseif($withdrawal->status_color === 'red') bg-red-100 text-red-800
                                        @elseif($withdrawal->status_color === 'gray') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $withdrawal->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $withdrawal->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewWithdrawalDetails({{ $withdrawal->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
                <i class="fas fa-money-bill-wave text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">暂无提现记录</h3>
                <p class="text-gray-500 mb-4">您还没有申请过提现</p>
                <button onclick="openWithdrawalModal()" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i>
                    申请提现
                </button>
            </div>
        @endif
    </div>
</div>

<!-- 提现申请模态框 -->
<div id="withdrawalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">申请提现</h3>
            <form id="withdrawalForm">
                <!-- 当前余额显示 -->
                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">当前余额:</span>
                        <span class="text-lg font-semibold text-blue-600">RM{{ number_format($currentBalance ?? 0, 2) }}</span>
                    </div>
                </div>
                
                <!-- 提现金额 -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">提现金额</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">RM</span>
                        </div>
                        <input type="number" 
                               id="withdrawalAmount"
                               name="amount"
                               step="0.01"
                               min="10"
                               max="{{ $currentBalance ?? 0 }}"
                               placeholder="请输入提现金额"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">最低提现金额: RM10.00</p>
                </div>
                
                <!-- 提现方式 -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">提现方式</label>
                    <select id="withdrawalMethod" 
                            name="method"
                            onchange="updatePaymentInfoFields()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">请选择提现方式</option>
                        <option value="bank_transfer">银行转账</option>
                        <option value="alipay">支付宝</option>
                        <option value="wechat">微信支付</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                
                <!-- 收款信息 -->
                <div id="paymentInfoFields" class="mb-4 space-y-3 hidden">
                    <!-- 银行转账 -->
                    <div id="bankTransferFields" class="hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">银行名称</label>
                            <input type="text" 
                                   name="payment_info[bank_name]"
                                   placeholder="请输入银行名称"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <!-- 支付宝 -->
                    <div id="alipayFields" class="hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">支付宝账号</label>
                            <input type="text" 
                                   name="payment_info[alipay_account]"
                                   placeholder="请输入支付宝账号"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <!-- 微信支付 -->
                    <div id="wechatFields" class="hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">微信账号</label>
                            <input type="text" 
                                   name="payment_info[wechat_account]"
                                   placeholder="请输入微信账号"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <!-- PayPal -->
                    <div id="paypalFields" class="hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PayPal邮箱</label>
                            <input type="email" 
                                   name="payment_info[paypal_email]"
                                   placeholder="请输入PayPal邮箱"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <!-- 通用字段 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">收款人姓名</label>
                        <input type="text" 
                               name="payment_info[account_name]"
                               placeholder="请输入收款人姓名"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">账号/卡号</label>
                        <input type="text" 
                               name="payment_info[account_number]"
                               placeholder="请输入账号或卡号"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <!-- 手续费说明 -->
                <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium">手续费说明:</p>
                            <p>提现手续费为2%，最低RM2.00</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeWithdrawalModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        取消
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        提交申请
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openWithdrawalModal() {
    document.getElementById('withdrawalModal').classList.remove('hidden');
}

function closeWithdrawalModal() {
    document.getElementById('withdrawalModal').classList.add('hidden');
    document.getElementById('withdrawalForm').reset();
    document.getElementById('paymentInfoFields').classList.add('hidden');
}

function updatePaymentInfoFields() {
    const method = document.getElementById('withdrawalMethod').value;
    const paymentInfoFields = document.getElementById('paymentInfoFields');
    
    // 隐藏所有字段组
    document.getElementById('bankTransferFields').classList.add('hidden');
    document.getElementById('alipayFields').classList.add('hidden');
    document.getElementById('wechatFields').classList.add('hidden');
    document.getElementById('paypalFields').classList.add('hidden');
    
    if (method) {
        paymentInfoFields.classList.remove('hidden');
        
        // 显示对应的字段组
        switch(method) {
            case 'bank_transfer':
                document.getElementById('bankTransferFields').classList.remove('hidden');
                break;
            case 'alipay':
                document.getElementById('alipayFields').classList.remove('hidden');
                break;
            case 'wechat':
                document.getElementById('wechatFields').classList.remove('hidden');
                break;
            case 'paypal':
                document.getElementById('paypalFields').classList.remove('hidden');
                break;
        }
    } else {
        paymentInfoFields.classList.add('hidden');
    }
}

function viewWithdrawalDetails(withdrawalId) {
    // 这里可以实现查看提现详情的功能
    alert('查看提现详情功能待实现');
}

document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // 构建payment_info对象
    const paymentInfo = {};
    for (const [key, value] of Object.entries(data)) {
        if (key.startsWith('payment_info[')) {
            const fieldName = key.match(/payment_info\[(.*?)\]/)[1];
            paymentInfo[fieldName] = value;
        }
    }
    
    const requestData = {
        amount: parseFloat(data.amount),
        method: data.method,
        payment_info: paymentInfo
    };
    
    fetch('/merchant/withdrawals', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('提现申请失败: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('提现申请失败，请稍后重试');
    });
    
    closeWithdrawalModal();
});

// 点击模态框外部关闭
document.getElementById('withdrawalModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeWithdrawalModal();
    }
});
</script>
@endsection