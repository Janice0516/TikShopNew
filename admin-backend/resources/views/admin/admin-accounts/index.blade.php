@extends('admin.layouts.app')

@section('title', '管理员账户管理')
@section('page-title', '管理员账户管理')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">管理员账户管理</h1>
            <p class="text-gray-600">管理系统管理员账户</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.admin-accounts.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                创建管理员
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
                       placeholder="姓名、用户名、邮箱..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">角色</label>
                <select name="role_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部角色</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
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
                    共 {{ $admins->total() }} 个管理员
                </div>
            </div>
        </div>

        <!-- 管理员列表 -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">管理员信息</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">角色</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">最后登录</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">创建时间</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($admin->id !== auth('admin')->id())
                            <input type="checkbox" 
                                   name="admin_ids[]" 
                                   value="{{ $admin->id }}"
                                   class="admin-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user-shield text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $admin->username }}</div>
                                    <div class="text-sm text-gray-500">{{ $admin->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                @if($admin->role === 'super_admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-crown mr-1"></i>
                                        超级管理员
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-user-tie mr-1"></i>
                                        管理员
                                    </span>
                                @endif
                                
                                @if($admin->roles->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($admin->roles as $role)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($admin->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    正常
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    停用
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($admin->last_login_at)
                                {{ $admin->last_login_at->format('Y-m-d H:i') }}
                            @else
                                <span class="text-gray-400">从未登录</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admin->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.admin-accounts.show', $admin) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="查看详情">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.admin-accounts.edit', $admin) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="编辑">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($admin->id !== auth('admin')->id())
                                <button onclick="resetPassword({{ $admin->id }})" 
                                        class="text-yellow-600 hover:text-yellow-900" title="重置密码">
                                    <i class="fas fa-key"></i>
                                </button>
                                <form method="POST" 
                                      action="{{ route('admin.admin-accounts.destroy', $admin) }}" 
                                      class="inline"
                                      onsubmit="return confirm('确定要删除这个管理员吗？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" title="删除">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-shield text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">暂无管理员</p>
                                <p class="text-sm">还没有任何管理员记录</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        @if($admins->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $admins->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 批量操作表单 -->
<form id="bulk-action-form" method="POST" action="{{ route('admin.admin-accounts.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <div id="bulk-admin-ids"></div>
</form>

<!-- 重置密码模态框 -->
<div id="reset-password-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">重置密码</h3>
                <button onclick="closeResetPasswordModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="reset-password-form" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">新密码</label>
                    <input type="password" 
                           name="password" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">确认密码</label>
                    <input type="password" 
                           name="password_confirmation" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeResetPasswordModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        取消
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        重置密码
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 全选功能
    const selectAllCheckbox = document.getElementById('select-all');
    const adminCheckboxes = document.querySelectorAll('.admin-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        adminCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // 单个复选框变化时更新全选状态
    adminCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.admin-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === adminCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < adminCheckboxes.length;
        });
    });
    
    // 批量操作
    document.getElementById('apply-bulk-action').addEventListener('click', function() {
        const action = document.getElementById('bulk-action').value;
        const checkedBoxes = document.querySelectorAll('.admin-checkbox:checked');
        
        if (!action) {
            alert('请选择批量操作');
            return;
        }
        
        if (checkedBoxes.length === 0) {
            alert('请选择要操作的管理员');
            return;
        }
        
        if (action === 'delete' && !confirm('确定要删除选中的管理员吗？此操作不可恢复！')) {
            return;
        }
        
        if (!confirm('确定要执行批量操作吗？')) {
            return;
        }
        
        // 设置表单数据
        document.getElementById('bulk-action-input').value = action;
        const bulkAdminIds = document.getElementById('bulk-admin-ids');
        bulkAdminIds.innerHTML = '';
        
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'admin_ids[]';
            input.value = checkbox.value;
            bulkAdminIds.appendChild(input);
        });
        
        // 提交表单
        document.getElementById('bulk-action-form').submit();
    });
});

// 重置密码功能
function resetPassword(adminId) {
    const form = document.getElementById('reset-password-form');
    form.action = `/admin/admin-accounts/${adminId}/reset-password`;
    document.getElementById('reset-password-modal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('reset-password-modal').classList.add('hidden');
    document.getElementById('reset-password-form').reset();
}
</script>
@endsection
