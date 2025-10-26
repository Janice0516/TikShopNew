@extends('admin.layouts.app')

@section('title', '提现详情')

@section('content')
<div class="p-6">
    <!-- 页面标题 -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">提现详情</h1>
            <p class="text-sm text-gray-600 mt-1">提现单号: {{ $withdrawal->withdrawal_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.withdrawals.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>返回列表
            </a>
            @if($withdrawal->canProcess())
                <button onclick="processWithdrawal('approve')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-check mr-2"></i>处理
                </button>
                <button onclick="processWithdrawal('reject')" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-times mr-2"></i>拒绝
                </button>
            @endif
            @if($withdrawal->status === 'processing')
                <button onclick="completeWithdrawal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-check-circle mr-2"></i>完成
                </button>
            @endif
            @if($withdrawal->canCancel())
                <button onclick="cancelWithdrawal()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <i class="fas fa-ban mr-2"></i>取消
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 提现信息 -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">提现信息</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">提现单号</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->withdrawal_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">申请时间</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->created_at->format('Y-m-d H:i:s') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">提现金额</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">RM {{ number_format($withdrawal->amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">手续费</dt>
                            <dd class="mt-1 text-sm text-gray-900">RM {{ number_format($withdrawal->fee, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">实际到账金额</dt>
                            <dd class="mt-1 text-sm font-medium text-green-600">RM {{ number_format($withdrawal->actual_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">提现方式</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $withdrawal->method_label }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">状态</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($withdrawal->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($withdrawal->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($withdrawal->status === 'completed') bg-green-100 text-green-800
                                    @elseif($withdrawal->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $withdrawal->status_label }}
                                </span>
                            </dd>
                        </div>
                        @if($withdrawal->processed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">处理时间</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->processed_at->format('Y-m-d H:i:s') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- 账户信息 -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">账户信息</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">账户姓名</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->account_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">账户号码</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->account_number }}</dd>
                        </div>
                        @if($withdrawal->bank_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">银行名称</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->bank_name }}</dd>
                            </div>
                        @endif
                        @if($withdrawal->bank_branch)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">开户支行</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->bank_branch }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- 备注信息 -->
            @if($withdrawal->merchant_notes || $withdrawal->admin_notes)
                <div class="bg-white rounded-lg shadow mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">备注信息</h3>
                    </div>
                    <div class="p-6">
                        @if($withdrawal->merchant_notes)
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 mb-2">商家备注</dt>
                                <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $withdrawal->merchant_notes }}</dd>
                            </div>
                        @endif
                        @if($withdrawal->admin_notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">管理员备注</dt>
                                <dd class="text-sm text-gray-900 bg-blue-50 p-3 rounded-md">{{ $withdrawal->admin_notes }}</dd>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 拒绝原因 -->
            @if($withdrawal->rejection_reason)
                <div class="bg-white rounded-lg shadow mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">拒绝原因</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-red-600 bg-red-50 p-3 rounded-md">{{ $withdrawal->rejection_reason }}</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- 商家信息 -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">商家信息</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $withdrawal->merchant->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $withdrawal->merchant->email }}</p>
                        </div>
                    </div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">商家ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->merchant->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">注册时间</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->merchant->created_at->format('Y-m-d') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">状态</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $withdrawal->merchant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $withdrawal->merchant->is_active ? '正常' : '停用' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- 处理人信息 -->
            @if($withdrawal->processor)
                <div class="bg-white rounded-lg shadow mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">处理人信息</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-shield text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $withdrawal->processor->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $withdrawal->processor->username }}</p>
                            </div>
                        </div>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">管理员ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->processor->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">处理时间</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $withdrawal->processed_at->format('Y-m-d H:i:s') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- 操作日志 -->
    <div class="bg-white rounded-lg shadow mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">操作日志</h3>
        </div>
        <div class="p-6">
            @if($withdrawal->logs->count() > 0)
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($withdrawal->logs as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-user text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $log->description ?? '操作记录' }}
                                                </p>
                                                @if($log->operator)
                                                    <p class="text-xs text-gray-400">
                                                        操作人: {{ $log->operator->name }} ({{ $log->operator->username }})
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-sm text-gray-500">暂无操作日志</p>
            @endif
        </div>
    </div>
</div>

<!-- 处理提现模态框 -->
<div id="processModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">处理提现申请</h3>
                <form id="processForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">操作</label>
                        <select name="action" id="processAction" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="approve">处理</option>
                            <option value="reject">拒绝</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">管理员备注</label>
                        <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="请输入备注信息..."></textarea>
                    </div>
                    <div id="rejectionReasonDiv" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">拒绝原因</label>
                        <textarea name="rejection_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="请输入拒绝原因..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeProcessModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            取消
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            确认
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 完成提现模态框 -->
<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">完成提现</h3>
                <form id="completeForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">管理员备注</label>
                        <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="请输入备注信息..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCompleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            取消
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            确认完成
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 取消提现模态框 -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">取消提现</h3>
                <form id="cancelForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">管理员备注</label>
                        <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="请输入取消原因..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            取消
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            确认取消
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// 处理提现
function processWithdrawal(action) {
    const form = document.getElementById('processForm');
    form.action = `{{ route('admin.withdrawals.process', $withdrawal) }}`;
    document.getElementById('processAction').value = action;
    
    if (action === 'reject') {
        document.getElementById('rejectionReasonDiv').classList.remove('hidden');
    } else {
        document.getElementById('rejectionReasonDiv').classList.add('hidden');
    }
    
    document.getElementById('processModal').classList.remove('hidden');
}

function closeProcessModal() {
    document.getElementById('processModal').classList.add('hidden');
}

// 完成提现
function completeWithdrawal() {
    const form = document.getElementById('completeForm');
    form.action = `{{ route('admin.withdrawals.complete', $withdrawal) }}`;
    document.getElementById('completeModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
}

// 取消提现
function cancelWithdrawal() {
    const form = document.getElementById('cancelForm');
    form.action = `{{ route('admin.withdrawals.cancel', $withdrawal) }}`;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}
</script>
@endsection
