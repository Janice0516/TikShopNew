@extends('admin.layouts.app')

@section('title', '用户管理')
@section('page-title', '用户管理')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">用户管理</h1>
            <p class="text-gray-600">管理平台用户账户</p>
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
                       placeholder="用户名、邮箱..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">用户类型</label>
                <select name="user_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部类型</option>
                    <option value="merchant" {{ request('user_type') == 'merchant' ? 'selected' : '' }}>商家</option>
                    <option value="customer" {{ request('user_type') == 'customer' ? 'selected' : '' }}>客户</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">状态</label>
                <select name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部状态</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>正常</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>停用</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">开始日期</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ request('start_date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">结束日期</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ request('end_date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="md:col-span-5 flex justify-end">
                <button type="submit" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
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
                            <option value="delete">批量删除</option>
                        </select>
                        <button type="button" 
                                id="apply-bulk-action"
                                class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            应用
                        </button>
                    </div>
                </div>
                
                <div class="text-sm text-gray-500">
                    共 {{ $users->total() }} 个用户
                </div>
            </div>
        </div>

        <!-- 用户列表 -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">用户信息</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">用户类型</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">注册时间</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">最后登录</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                   name="user_ids[]" 
                                   value="{{ $user->id }}"
                                   class="user-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    @if($user->merchant)
                                        <div class="text-xs text-gray-400">店铺: {{ $user->merchant->shop_name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->merchant)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-store mr-1"></i>
                                    商家
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-user mr-1"></i>
                                    客户
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->merchant)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($user->merchant->status === 'active') bg-green-100 text-green-800
                                    @elseif($user->merchant->status === 'inactive') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $user->merchant->status_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    正常
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($user->merchant && $user->merchant->last_login_at)
                                {{ $user->merchant->last_login_at->format('Y-m-d H:i') }}
                            @else
                                <span class="text-gray-400">从未登录</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('admin.users.destroy', $user) }}" 
                                      class="inline"
                                      onsubmit="return confirm('确定要删除这个用户吗？')">
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
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">暂无用户</p>
                                <p class="text-sm">还没有任何用户记录</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 批量操作表单 -->
<form id="bulk-action-form" method="POST" action="{{ route('admin.users.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <div id="bulk-user-ids"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 全选功能
    const selectAllCheckbox = document.getElementById('select-all');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // 单个复选框变化时更新全选状态
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === userCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < userCheckboxes.length;
        });
    });
    
    // 批量操作
    document.getElementById('apply-bulk-action').addEventListener('click', function() {
        const action = document.getElementById('bulk-action').value;
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        
        if (!action) {
            alert('请选择批量操作');
            return;
        }
        
        if (checkedBoxes.length === 0) {
            alert('请选择要操作的用户');
            return;
        }
        
        if (action === 'delete' && !confirm('确定要删除选中的用户吗？此操作不可恢复！')) {
            return;
        }
        
        if (!confirm('确定要执行批量操作吗？')) {
            return;
        }
        
        // 设置表单数据
        document.getElementById('bulk-action-input').value = action;
        const bulkUserIds = document.getElementById('bulk-user-ids');
        bulkUserIds.innerHTML = '';
        
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_ids[]';
            input.value = checkbox.value;
            bulkUserIds.appendChild(input);
        });
        
        // 提交表单
        document.getElementById('bulk-action-form').submit();
    });
});
</script>
@endsection
