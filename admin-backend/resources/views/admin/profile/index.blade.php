@extends('admin.layouts.app')

@section('title', '个人中心')
@section('page-title', '个人中心')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">个人中心</h1>
            <p class="text-gray-600">管理您的个人信息和设置</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="exportData()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-download mr-2"></i>
                导出数据
            </button>
        </div>
    </div>

    <!-- 个人信息卡片 -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">个人信息</h3>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-6">
                <!-- 头像 -->
                <div class="flex-shrink-0">
                    <div class="relative">
                        @if($admin->avatar)
                            <img src="{{ \Storage::url($admin->avatar) }}" 
                                 alt="{{ $admin->name }}" 
                                 class="h-24 w-24 rounded-full object-cover">
                        @else
                            <div class="h-24 w-24 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-shield text-3xl text-blue-600"></i>
                            </div>
                        @endif
                        <button onclick="openAvatarModal()" 
                                class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-camera text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- 基本信息 -->
                <div class="flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">姓名</label>
                            <div class="text-lg font-medium text-gray-900">{{ $admin->name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">用户名</label>
                            <div class="text-lg font-medium text-gray-900">{{ $admin->username }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">邮箱</label>
                            <div class="text-lg font-medium text-gray-900">{{ $admin->email }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">角色</label>
                            <div class="flex items-center space-x-2">
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
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">账户状态</label>
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
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">最后登录</label>
                            <div class="text-sm text-gray-500">
                                @if($admin->last_login_at)
                                    {{ $admin->last_login_at->format('Y-m-d H:i:s') }}
                                @else
                                    从未登录
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 统计信息 -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sign-in-alt text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['login_count'] }}</div>
                    <div class="text-sm text-gray-500">登录次数</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['permissions_count'] }}</div>
                    <div class="text-sm text-gray-500">权限数量</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tag text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['roles_count'] }}</div>
                    <div class="text-sm text-gray-500">角色数量</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['account_created']->diffInDays(now()) }}</div>
                    <div class="text-sm text-gray-500">账户天数</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 标签页 -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="switchTab('profile')" 
                        class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    个人资料
                </button>
                <button onclick="switchTab('password')" 
                        class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    修改密码
                </button>
                <button onclick="switchTab('notifications')" 
                        class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    通知设置
                </button>
                <button onclick="switchTab('settings')" 
                        class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    系统设置
                </button>
                <button onclick="switchTab('security')" 
                        class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    安全记录
                </button>
            </nav>
        </div>

        <!-- 个人资料标签页 -->
        <div id="profile-tab" class="tab-content p-6">
            <form method="POST" action="{{ route('admin.profile.update-profile') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">姓名 <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $admin->name) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">用户名 <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="username" 
                               value="{{ old('username', $admin->username) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">邮箱 <span class="text-red-500">*</span></label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $admin->email) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        保存更改
                    </button>
                </div>
            </form>
        </div>

        <!-- 修改密码标签页 -->
        <div id="password-tab" class="tab-content p-6 hidden">
            <form method="POST" action="{{ route('admin.profile.update-password') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">当前密码 <span class="text-red-500">*</span></label>
                        <input type="password" 
                               name="current_password" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">新密码 <span class="text-red-500">*</span></label>
                        <input type="password" 
                               name="password" 
                               required
                               minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">确认新密码 <span class="text-red-500">*</span></label>
                        <input type="password" 
                               name="password_confirmation" 
                               required
                               minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-key mr-2"></i>
                        更新密码
                    </button>
                </div>
            </form>
        </div>

        <!-- 通知设置标签页 -->
        <div id="notifications-tab" class="tab-content p-6 hidden">
            <form method="POST" action="{{ route('admin.profile.update-notifications') }}">
                @csrf
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">通知类型</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">邮件通知</div>
                                    <div class="text-sm text-gray-500">接收邮件通知</div>
                                </div>
                                <input type="checkbox" 
                                       name="email_notifications" 
                                       value="1"
                                       {{ ($admin->notifications['email_notifications'] ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">短信通知</div>
                                    <div class="text-sm text-gray-500">接收短信通知</div>
                                </div>
                                <input type="checkbox" 
                                       name="sms_notifications" 
                                       value="1"
                                       {{ ($admin->notifications['sms_notifications'] ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">推送通知</div>
                                    <div class="text-sm text-gray-500">接收浏览器推送通知</div>
                                </div>
                                <input type="checkbox" 
                                       name="push_notifications" 
                                       value="1"
                                       {{ ($admin->notifications['push_notifications'] ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">订单通知</div>
                                    <div class="text-sm text-gray-500">新订单和订单状态变更通知</div>
                                </div>
                                <input type="checkbox" 
                                       name="order_notifications" 
                                       value="1"
                                       {{ ($admin->notifications['order_notifications'] ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">系统通知</div>
                                    <div class="text-sm text-gray-500">系统维护和重要更新通知</div>
                                </div>
                                <input type="checkbox" 
                                       name="system_notifications" 
                                       value="1"
                                       {{ ($admin->notifications['system_notifications'] ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-bell mr-2"></i>
                        保存设置
                    </button>
                </div>
            </form>
        </div>

        <!-- 系统设置标签页 -->
        <div id="settings-tab" class="tab-content p-6 hidden">
            <form method="POST" action="{{ route('admin.profile.update-settings') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">语言 <span class="text-red-500">*</span></label>
                        <select name="language" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="zh" {{ ($admin->settings['language'] ?? 'zh') === 'zh' ? 'selected' : '' }}>中文</option>
                            <option value="en" {{ ($admin->settings['language'] ?? 'zh') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="ms" {{ ($admin->settings['language'] ?? 'zh') === 'ms' ? 'selected' : '' }}>Bahasa Malaysia</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">时区 <span class="text-red-500">*</span></label>
                        <select name="timezone" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="Asia/Kuala_Lumpur" {{ ($admin->settings['timezone'] ?? 'Asia/Kuala_Lumpur') === 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>Asia/Kuala_Lumpur</option>
                            <option value="UTC" {{ ($admin->settings['timezone'] ?? 'Asia/Kuala_Lumpur') === 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">日期格式 <span class="text-red-500">*</span></label>
                        <select name="date_format" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="Y-m-d" {{ ($admin->settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>2024-01-01</option>
                            <option value="d/m/Y" {{ ($admin->settings['date_format'] ?? 'Y-m-d') === 'd/m/Y' ? 'selected' : '' }}>01/01/2024</option>
                            <option value="m/d/Y" {{ ($admin->settings['date_format'] ?? 'Y-m-d') === 'm/d/Y' ? 'selected' : '' }}>01/01/2024</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">时间格式 <span class="text-red-500">*</span></label>
                        <select name="time_format" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="H:i" {{ ($admin->settings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : '' }}>24小时制 (14:30)</option>
                            <option value="h:i A" {{ ($admin->settings['time_format'] ?? 'H:i') === 'h:i A' ? 'selected' : '' }}>12小时制 (2:30 PM)</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">主题 <span class="text-red-500">*</span></label>
                        <select name="theme" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="light" {{ ($admin->settings['theme'] ?? 'light') === 'light' ? 'selected' : '' }}>浅色主题</option>
                            <option value="dark" {{ ($admin->settings['theme'] ?? 'light') === 'dark' ? 'selected' : '' }}>深色主题</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-cog mr-2"></i>
                        保存设置
                    </button>
                </div>
            </form>
        </div>

        <!-- 安全记录标签页 -->
        <div id="security-tab" class="tab-content p-6 hidden">
            <div class="space-y-6">
                <!-- 登录历史 -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">登录历史</h4>
                    <div id="login-history" class="space-y-3">
                        <!-- 登录历史将通过AJAX加载 -->
                    </div>
                </div>

                <!-- 操作日志 -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">操作日志</h4>
                    <div id="activity-log" class="space-y-3">
                        <!-- 操作日志将通过AJAX加载 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 头像上传模态框 -->
<div id="avatar-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">更新头像</h3>
                <button onclick="closeAvatarModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="avatar-form" method="POST" action="{{ route('admin.profile.update-avatar') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">选择头像</label>
                    <input type="file" 
                           name="avatar" 
                           accept="image/*"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeAvatarModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        取消
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        上传头像
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 加载安全记录数据
    loadLoginHistory();
    loadActivityLog();
});

// 标签页切换
function switchTab(tabName) {
    // 隐藏所有标签页内容
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // 移除所有按钮的激活状态
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // 显示选中的标签页
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // 激活选中的按钮
    event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
    
    // 如果是安全记录标签页，重新加载数据
    if (tabName === 'security') {
        loadLoginHistory();
        loadActivityLog();
    }
}

// 头像模态框
function openAvatarModal() {
    document.getElementById('avatar-modal').classList.remove('hidden');
}

function closeAvatarModal() {
    document.getElementById('avatar-modal').classList.add('hidden');
    document.getElementById('avatar-form').reset();
}

// 加载登录历史
function loadLoginHistory() {
    fetch('/admin/profile/login-history')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('login-history');
            container.innerHTML = '';
            
            data.forEach(record => {
                const statusClass = record.status === 'success' ? 'text-green-600' : 'text-red-600';
                const statusIcon = record.status === 'success' ? 'fa-check-circle' : 'fa-times-circle';
                
                container.innerHTML += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <i class="fas ${statusIcon} ${statusClass}"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">${record.location}</div>
                                <div class="text-xs text-gray-500">${record.device}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-900">${record.ip}</div>
                            <div class="text-xs text-gray-500">${new Date(record.login_at).toLocaleString()}</div>
                        </div>
                    </div>
                `;
            });
        })
        .catch(error => {
            console.error('加载登录历史失败:', error);
        });
}

// 加载操作日志
function loadActivityLog() {
    fetch('/admin/profile/activity-log')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('activity-log');
            container.innerHTML = '';
            
            data.forEach(record => {
                container.innerHTML += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-history text-blue-600"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">${record.action}</div>
                                <div class="text-xs text-gray-500">${record.description}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-900">${record.ip}</div>
                            <div class="text-xs text-gray-500">${new Date(record.created_at).toLocaleString()}</div>
                        </div>
                    </div>
                `;
            });
        })
        .catch(error => {
            console.error('加载操作日志失败:', error);
        });
}

// 导出数据
function exportData() {
    window.open('/admin/profile/export-data', '_blank');
}

// 点击模态框外部关闭
document.getElementById('avatar-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAvatarModal();
    }
});
</script>
@endsection
