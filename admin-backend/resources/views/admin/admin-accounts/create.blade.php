@extends('admin.layouts.app')

@section('title', '创建管理员')
@section('page-title', '创建管理员')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">创建管理员</h1>
            <p class="text-gray-600">创建新的管理员账户</p>
        </div>
        <div>
            <a href="{{ route('admin.admin-accounts.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>
                返回列表
            </a>
        </div>
    </div>

    <!-- 创建表单 -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.admin-accounts.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 基本信息 -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">基本信息</h3>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">用户名 <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="username" 
                           value="{{ old('username') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror">
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">邮箱 <span class="text-red-500">*</span></label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">姓名 <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">管理员类型 <span class="text-red-500">*</span></label>
                    <select name="role" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror">
                        <option value="">请选择类型</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>管理员</option>
                        <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>超级管理员</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- 密码设置 -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">密码设置</h3>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">密码 <span class="text-red-500">*</span></label>
                    <input type="password" 
                           name="password" 
                           required
                           minlength="6"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">确认密码 <span class="text-red-500">*</span></label>
                    <input type="password" 
                           name="password_confirmation" 
                           required
                           minlength="6"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- 角色分配 -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">角色分配</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($roles as $role)
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="roles[]" 
                                   value="{{ $role->id }}"
                                   {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">
                                {{ $role->name }}
                                @if($role->description)
                                    <span class="text-gray-500">- {{ $role->description }}</span>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- 状态设置 -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">状态设置</h3>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">激活账户</label>
                    </div>
                </div>
            </div>

            <!-- 保存按钮 -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.admin-accounts.index') }}" 
                   class="px-6 py-3 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    取消
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    创建管理员
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
