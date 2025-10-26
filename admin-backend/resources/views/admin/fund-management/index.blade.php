@extends('admin.layouts.app')

@section('title', '资金管理')

@section('content')
<div class="p-6">
    <!-- 页面标题 -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">资金管理</h1>
        <div class="flex space-x-3">
            <button onclick="showTransferModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-exchange-alt mr-2"></i>转账
            </button>
            <a href="{{ route('admin.fund-management.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-plus mr-2"></i>创建账户
            </a>
            <button onclick="exportData()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-download mr-2"></i>导出数据
            </button>
            <button onclick="showStatistics()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
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
                        <i class="fas fa-wallet text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">总账户数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_accounts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-coins text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">总余额</p>
                    <p class="text-2xl font-semibold text-gray-900">RM {{ number_format($stats['total_balance'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">冻结金额</p>
                    <p class="text-2xl font-semibold text-gray-900">RM {{ number_format($stats['total_frozen_amount'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">可用余额</p>
                    <p class="text-2xl font-semibold text-gray-900">RM {{ number_format($stats['total_available_balance'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.fund-management.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- 搜索 -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">搜索</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="搜索账户名称、账户号码、所有者..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- 账户类型筛选 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">账户类型</label>
                    <select name="account_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">全部类型</option>
                        <option value="platform" {{ request('account_type') === 'platform' ? 'selected' : '' }}>平台账户</option>
                        <option value="merchant" {{ request('account_type') === 'merchant' ? 'selected' : '' }}>商家账户</option>
                        <option value="customer" {{ request('account_type') === 'customer' ? 'selected' : '' }}>客户账户</option>
                        <option value="system" {{ request('account_type') === 'system' ? 'selected' : '' }}>系统账户</option>
                    </select>
                </div>

                <!-- 状态筛选 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">状态</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">全部状态</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>正常</option>
                        <option value="frozen" {{ request('status') === 'frozen' ? 'selected' : '' }}>冻结</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>关闭</option>
                    </select>
                </div>

                <!-- 所有者筛选 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">所有者</label>
                    <select name="owner_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">全部所有者</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('owner_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 操作按钮 -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>搜索
                    </button>
                    <a href="{{ route('admin.fund-management.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-undo mr-2"></i>重置
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 资金账户列表 -->
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">账户信息</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">所有者</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">账户余额</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">冻结金额</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">可用余额</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">最后交易</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($fundAccounts as $fundAccount)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $fundAccount->account_name }}</div>
                                <div class="text-sm text-gray-500">{{ $fundAccount->account_number }}</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $fundAccount->account_type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($fundAccount->owner)
                                    <div class="text-sm text-gray-900">{{ $fundAccount->owner->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $fundAccount->owner->email }}</div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $fundAccount->owner_type === 'merchant' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $fundAccount->owner_type === 'merchant' ? '商家' : '客户' }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">系统账户</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">RM {{ number_format($fundAccount->balance, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">RM {{ number_format($fundAccount->frozen_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">RM {{ number_format($fundAccount->available_balance, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($fundAccount->status === 'active') bg-green-100 text-green-800
                                    @elseif($fundAccount->status === 'frozen') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $fundAccount->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $fundAccount->last_transaction_at ? $fundAccount->last_transaction_at->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.fund-management.show', $fundAccount) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="adjustBalance({{ $fundAccount->id }})" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                暂无资金账户
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        @if($fundAccounts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $fundAccounts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- 转账模态框 -->
<div id="transferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">账户转账</h3>
                <form id="transferForm" method="POST" action="{{ route('admin.fund-management.transfer') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">转出账户</label>
                        <select name="from_account_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">选择转出账户</option>
                            @foreach($fundAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">转入账户</label>
                        <select name="to_account_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">选择转入账户</option>
                            @foreach($fundAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">转账金额</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="请输入转账金额">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">转账说明</label>
                        <textarea name="description" rows="3" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  placeholder="请输入转账说明"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeTransferModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            取消
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            确认转账
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 调整余额模态框 -->
<div id="adjustBalanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">调整账户余额</h3>
                <form id="adjustBalanceForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">调整类型</label>
                        <select name="adjustment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">选择调整类型</option>
                            <option value="add">增加余额</option>
                            <option value="deduct">减少余额</option>
                            <option value="freeze">冻结资金</option>
                            <option value="unfreeze">解冻资金</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">调整金额</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="请输入调整金额">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">调整说明</label>
                        <textarea name="description" rows="3" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  placeholder="请输入调整说明"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAdjustBalanceModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            取消
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            确认调整
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// 显示转账模态框
function showTransferModal() {
    document.getElementById('transferModal').classList.remove('hidden');
}

function closeTransferModal() {
    document.getElementById('transferModal').classList.add('hidden');
}

// 调整余额
function adjustBalance(fundAccountId) {
    const form = document.getElementById('adjustBalanceForm');
    form.action = `/admin/fund-management/${fundAccountId}/adjust-balance`;
    document.getElementById('adjustBalanceModal').classList.remove('hidden');
}

function closeAdjustBalanceModal() {
    document.getElementById('adjustBalanceModal').classList.add('hidden');
}

// 导出数据
function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.open(`{{ route('admin.fund-management.export') }}?${params.toString()}`, '_blank');
}

// 显示统计信息
function showStatistics() {
    // 这里可以显示更详细的统计信息
    alert('统计功能开发中...');
}
</script>
@endsection
