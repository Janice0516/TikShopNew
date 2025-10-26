@extends('admin.layouts.app')

@section('title', '角色管理')
@section('page-title', '角色管理')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">角色管理</h1>
            <p class="text-gray-600">管理系统角色和权限</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.roles.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                创建角色
            </a>
        </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">搜索</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="角色名称、描述..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">类型</label>
                <select name="type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全部类型</option>
                    <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>系统角色</option>
                    <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>自定义角色</option>
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
                    共 {{ $roles->total() }} 个角色
                </div>
            </div>
        </div>

        <!-- 角色列表 -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">角色信息</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">权限数量</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">管理员数量</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">类型</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">创建时间</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(!$role->is_system)
                            <input type="checkbox" 
                                   name="role_ids[]" 
                                   value="{{ $role->id }}"
                                   class="role-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user-shield text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $role->slug }}</div>
                                    @if($role->description)
                                        <div class="text-xs text-gray-400">{{ Str::limit($role->description, 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $role->permissions->count() }} 个权限
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $role->admins->count() }} 个管理员
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($role->is_active)
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($role->is_system)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-cog mr-1"></i>
                                    系统角色
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-user mr-1"></i>
                                    自定义角色
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $role->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.roles.show', $role) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="查看详情">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.roles.edit', $role) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="编辑">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$role->is_system)
                                <a href="{{ route('admin.roles.duplicate', $role) }}" 
                                   class="text-green-600 hover:text-green-900" title="复制角色"
                                   onclick="return confirm('确定要复制这个角色吗？')">
                                    <i class="fas fa-copy"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('admin.roles.destroy', $role) }}" 
                                      class="inline"
                                      onsubmit="return confirm('确定要删除这个角色吗？')">
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
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-shield text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">暂无角色</p>
                                <p class="text-sm">还没有任何角色记录</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        @if($roles->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $roles->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 批量操作表单 -->
<form id="bulk-action-form" method="POST" action="{{ route('admin.roles.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <div id="bulk-role-ids"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 全选功能
    const selectAllCheckbox = document.getElementById('select-all');
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        roleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // 单个复选框变化时更新全选状态
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.role-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === roleCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < roleCheckboxes.length;
        });
    });
    
    // 批量操作
    document.getElementById('apply-bulk-action').addEventListener('click', function() {
        const action = document.getElementById('bulk-action').value;
        const checkedBoxes = document.querySelectorAll('.role-checkbox:checked');
        
        if (!action) {
            alert('请选择批量操作');
            return;
        }
        
        if (checkedBoxes.length === 0) {
            alert('请选择要操作的角色');
            return;
        }
        
        if (action === 'delete' && !confirm('确定要删除选中的角色吗？此操作不可恢复！')) {
            return;
        }
        
        if (!confirm('确定要执行批量操作吗？')) {
            return;
        }
        
        // 设置表单数据
        document.getElementById('bulk-action-input').value = action;
        const bulkRoleIds = document.getElementById('bulk-role-ids');
        bulkRoleIds.innerHTML = '';
        
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'role_ids[]';
            input.value = checkbox.value;
            bulkRoleIds.appendChild(input);
        });
        
        // 提交表单
        document.getElementById('bulk-action-form').submit();
    });
});
</script>
@endsection
