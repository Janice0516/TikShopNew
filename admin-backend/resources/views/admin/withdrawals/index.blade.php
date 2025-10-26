@extends('admin.layouts.app')

@section('title', '提现管理')

@section('content')
<div class="p-6">
    <!-- 页面标题 -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">提现管理</h1>
        <div class="flex space-x-3">
            <button onclick="exportData()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-download mr-2"></i>导出数据
            </button>
            <button onclick="showStatistics()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-chart-bar mr-2"></i>统计信息
            </button>
        </div>
    </div>

    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">总提现申请</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_withdrawals'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">待处理</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_withdrawals'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">已完成</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_withdrawals'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">已拒绝</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['rejected_withdrawals'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- 搜索 -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">搜索</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="搜索提现单号、商家名称、账户信息..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- 状态筛选 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">状态</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">全部状态</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>待处理</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>处理中</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>已完成</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>已拒绝</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>已取消</option>
                    </select>
                </div>

                <!-- 商家筛选 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">商家</label>
                    <select name="merchant_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">全部商家</option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}" {{ request('merchant_id') == $merchant->id ? 'selected' : '' }}>
                                {{ $merchant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 提现方式 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">提现方式</label>
                    <select name="method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">全部方式</option>
                        <option value="bank" {{ request('method') === 'bank' ? 'selected' : '' }}>银行转账</option>
                        <option value="alipay" {{ request('method') === 'alipay' ? 'selected' : '' }}>支付宝</option>
                        <option value="wechat" {{ request('method') === 'wechat' ? 'selected' : '' }}>微信支付</option>
                    </select>
                </div>

                <!-- 操作按钮 -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>搜索
                    </button>
                    <a href="{{ route('admin.withdrawals.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-undo mr-2"></i>重置
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 批量操作 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form id="bulkActionForm" method="POST" action="{{ route('admin.withdrawals.bulk-action') }}">
                @csrf
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="selectAll" class="ml-2 text-sm font-medium text-gray-700">全选</label>
                    </div>
                    
                    <select name="action" id="bulkAction" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">选择操作</option>
                        <option value="approve">批量处理</option>
                        <option value="reject">批量拒绝</option>
                        <option value="process">开始处理</option>
                        <option value="complete">完成提现</option>
                    </select>
                    
                    <button type="button" onclick="executeBulkAction()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        执行操作
                    </button>
                </div>
                
                <!-- 批量操作额外字段 -->
                <div id="bulkActionFields" class="mt-4 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">管理员备注</label>
                            <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="请输入备注信息..."></textarea>
                        </div>
                        <div id="rejectionReasonField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">拒绝原因</label>
                            <textarea name="rejection_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="请输入拒绝原因..."></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 提现申请列表 -->
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllTable" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">提现单号</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商家</th>
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
                    @forelse($withdrawals as $withdrawal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="withdrawal_ids[]" value="{{ $withdrawal->id }}" class="withdrawal-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $withdrawal->withdrawal_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $withdrawal->merchant->name }}</div>
                                <div class="text-sm text-gray-500">{{ $withdrawal->merchant->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">RM {{ number_format($withdrawal->amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">RM {{ number_format($withdrawal->fee, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-green-600">RM {{ number_format($withdrawal->actual_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $withdrawal->method_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($withdrawal->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($withdrawal->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($withdrawal->status === 'completed') bg-green-100 text-green-800
                                    @elseif($withdrawal->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $withdrawal->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $withdrawal->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($withdrawal->canProcess())
                                        <button onclick="processWithdrawal({{ $withdrawal->id }}, 'approve')" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="processWithdrawal({{ $withdrawal->id }}, 'reject')" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    @if($withdrawal->status === 'processing')
                                        <button onclick="completeWithdrawal({{ $withdrawal->id }})" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                    @if($withdrawal->canCancel())
                                        <button onclick="cancelWithdrawal({{ $withdrawal->id }})" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                暂无提现申请
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $withdrawals->links() }}
            </div>
        @endif
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
// 全选功能
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.withdrawal-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('selectAllTable').checked = this.checked;
});

document.getElementById('selectAllTable').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.withdrawal-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('selectAll').checked = this.checked;
});

// 批量操作
document.getElementById('bulkAction').addEventListener('change', function() {
    const fields = document.getElementById('bulkActionFields');
    const rejectionField = document.getElementById('rejectionReasonField');
    
    if (this.value) {
        fields.classList.remove('hidden');
        if (this.value === 'reject') {
            rejectionField.classList.remove('hidden');
        } else {
            rejectionField.classList.add('hidden');
        }
    } else {
        fields.classList.add('hidden');
    }
});

function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedCheckboxes = document.querySelectorAll('.withdrawal-checkbox:checked');
    
    if (!action) {
        alert('请选择操作');
        return;
    }
    
    if (selectedCheckboxes.length === 0) {
        alert('请选择要操作的提现申请');
        return;
    }
    
    if (action === 'reject' && !document.querySelector('textarea[name="rejection_reason"]').value) {
        alert('请填写拒绝原因');
        return;
    }
    
    if (confirm(`确定要${action === 'approve' ? '处理' : action === 'reject' ? '拒绝' : action === 'process' ? '开始处理' : '完成'}选中的 ${selectedCheckboxes.length} 个提现申请吗？`)) {
        document.getElementById('bulkActionForm').submit();
    }
}

// 处理提现
function processWithdrawal(withdrawalId, action) {
    const form = document.getElementById('processForm');
    form.action = `/admin/withdrawals/${withdrawalId}/process`;
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
function completeWithdrawal(withdrawalId) {
    const form = document.getElementById('completeForm');
    form.action = `/admin/withdrawals/${withdrawalId}/complete`;
    document.getElementById('completeModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
}

// 取消提现
function cancelWithdrawal(withdrawalId) {
    const form = document.getElementById('cancelForm');
    form.action = `/admin/withdrawals/${withdrawalId}/cancel`;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// 导出数据
function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.open(`{{ route('admin.withdrawals.export') }}?${params.toString()}`, '_blank');
}

// 显示统计信息
function showStatistics() {
    // 这里可以显示更详细的统计信息
    alert('统计功能开发中...');
}
</script>
@endsection
