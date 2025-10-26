@extends('admin.layouts.app')

@section('title', '商家管理')
@section('page-title', '商家管理')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">商家管理</h1>
            <p class="text-gray-600">管理平台商家账户</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.merchants.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                添加商家
            </a>
        </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">搜索</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="商家名称、用户名、店铺名..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">状态</label>
                <select name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部状态</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">认证状态</label>
                <select name="verified" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部认证状态</option>
                    <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>已认证</option>
                    <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>未认证</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">排序</label>
                <select name="sort_by" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>创建时间</option>
                    <option value="merchant_name" {{ request('sort_by') == 'merchant_name' ? 'selected' : '' }}>商家名称</option>
                    <option value="balance" {{ request('sort_by') == 'balance' ? 'selected' : '' }}>账户余额</option>
                    <option value="last_login_at" {{ request('sort_by') == 'last_login_at' ? 'selected' : '' }}>最后登录</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
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
                            <option value="activate">批量激活</option>
                            <option value="deactivate">批量停用</option>
                            <option value="suspend">批量暂停</option>
                            <option value="verify">批量认证</option>
                            <option value="unverify">批量取消认证</option>
                        </select>
                        <button type="button" 
                                id="apply-bulk-action"
                                class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            应用
                        </button>
                    </div>
                </div>
                
                <div class="text-sm text-gray-500">
                    共 {{ $merchants->total() }} 个商家
                </div>
            </div>
        </div>

        <!-- 商家列表 -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商家信息</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">联系方式</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">账户余额</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">认证状态</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">最后登录</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($merchants as $merchant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                   name="merchant_ids[]" 
                                   value="{{ $merchant->id }}"
                                   class="merchant-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-store text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $merchant->merchant_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $merchant->shop_name }}</div>
                                    <div class="text-xs text-gray-400">用户名: {{ $merchant->username }}</div>
                                    <div class="text-xs text-gray-400">邀请码: {{ $merchant->invite_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $merchant->contact_name }}</div>
                            <div class="text-gray-500">{{ $merchant->contact_phone }}</div>
                            <div class="text-gray-500">{{ $merchant->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">RM{{ number_format($merchant->balance, 2) }}</div>
                            @if($merchant->frozen_amount > 0)
                                <div class="text-xs text-red-500">冻结: RM{{ number_format($merchant->frozen_amount, 2) }}</div>
                            @endif
                            <div class="text-xs text-gray-500">可用: RM{{ number_format($merchant->available_balance, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($merchant->status === 'active') bg-green-100 text-green-800
                                @elseif($merchant->status === 'inactive') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $merchant->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($merchant->isVerified())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    已认证
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    未认证
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($merchant->last_login_at)
                                {{ $merchant->last_login_at->format('Y-m-d H:i') }}
                            @else
                                <span class="text-gray-400">从未登录</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.merchants.show', $merchant) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.merchants.edit', $merchant) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($merchant->isVerified())
                                    <a href="{{ route('admin.merchants.unverify', $merchant) }}" 
                                       class="text-yellow-600 hover:text-yellow-900"
                                       onclick="return confirm('确定要取消认证吗？')">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                @else
                                    <a href="{{ route('admin.merchants.verify', $merchant) }}" 
                                       class="text-green-600 hover:text-green-900"
                                       onclick="return confirm('确定要认证这个商家吗？')">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                @endif
                                <form method="POST" 
                                      action="{{ route('admin.merchants.destroy', $merchant) }}" 
                                      class="inline"
                                      onsubmit="return confirm('确定要删除这个商家吗？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-store text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">暂无商家</p>
                                <p class="text-sm">开始添加您的第一个商家吧</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        @if($merchants->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $merchants->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 批量操作表单 -->
<form id="bulk-action-form" method="POST" action="{{ route('admin.merchants.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <div id="bulk-merchant-ids"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 全选功能
    const selectAllCheckbox = document.getElementById('select-all');
    const merchantCheckboxes = document.querySelectorAll('.merchant-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        merchantCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // 单个复选框变化时更新全选状态
    merchantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.merchant-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === merchantCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < merchantCheckboxes.length;
        });
    });
    
    // 批量操作
    document.getElementById('apply-bulk-action').addEventListener('click', function() {
        const action = document.getElementById('bulk-action').value;
        const checkedBoxes = document.querySelectorAll('.merchant-checkbox:checked');
        
        if (!action) {
            alert('请选择批量操作');
            return;
        }
        
        if (checkedBoxes.length === 0) {
            alert('请选择要操作的商家');
            return;
        }
        
        if (!confirm('确定要执行批量操作吗？')) {
            return;
        }
        
        // 设置表单数据
        document.getElementById('bulk-action-input').value = action;
        const bulkMerchantIds = document.getElementById('bulk-merchant-ids');
        bulkMerchantIds.innerHTML = '';
        
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'merchant_ids[]';
            input.value = checkbox.value;
            bulkMerchantIds.appendChild(input);
        });
        
        // 提交表单
        document.getElementById('bulk-action-form').submit();
    });
});
</script>
@endsection
