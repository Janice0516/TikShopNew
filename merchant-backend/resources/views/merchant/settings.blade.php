@extends('layouts.merchant')

@section('title', '系统设置')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">系统设置</h1>
            <p class="text-gray-600">管理您的账户信息和系统偏好</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-sync-alt mr-2"></i>
                刷新数据
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">保存成功</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">保存失败</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 账户统计 -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-sign-in-alt text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">登录次数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['login_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-calendar text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">注册时间</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $stats['account_created']->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">最后登录</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $stats['last_login']->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-shopping-cart text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总订单</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <i class="fas fa-box text-indigo-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总商品</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_products'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-pink-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-pink-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总收入</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 设置表单 -->
    <form method="POST" action="/merchant/settings" class="space-y-6">
        @csrf
        
        <!-- 基本信息 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">基本信息</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">姓名 *</label>
                    <input type="text" 
                           name="name"
                           value="{{ $user->name }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">邮箱地址 *</label>
                    <input type="email" 
                           name="email"
                           value="{{ $user->email }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">联系电话</label>
                    <input type="text" 
                           name="phone"
                           value="{{ $merchantProfile->contact_phone ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">用户名</label>
                    <input type="text" 
                           value="{{ $merchantProfile->username ?? '' }}"
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                    <p class="text-xs text-gray-500 mt-1">用户名不可修改</p>
                </div>
            </div>
        </div>

        <!-- 密码设置 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">密码设置</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">当前密码</label>
                    <input type="password" 
                           name="current_password"
                           placeholder="输入当前密码"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">新密码</label>
                    <input type="password" 
                           name="new_password"
                           placeholder="输入新密码"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">确认新密码</label>
                    <input type="password" 
                           name="new_password_confirmation"
                           placeholder="再次输入新密码"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">留空表示不修改密码</p>
        </div>

        <!-- 通知设置 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">通知设置</h3>
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="notification_email"
                           value="1"
                           {{ ($userSettings['notification_email'] ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">
                        邮件通知
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="notification_sms"
                           value="1"
                           {{ ($userSettings['notification_sms'] ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">
                        短信通知
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="notification_push"
                           value="1"
                           {{ ($userSettings['notification_push'] ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">
                        推送通知
                    </label>
                </div>
            </div>
        </div>

        <!-- 语言和地区设置 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">语言和地区设置</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('merchant.settings.language') }}</label>
                    <select name="language" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="zh" {{ ($userSettings['language'] ?? 'zh') === 'zh' ? 'selected' : '' }}>中文</option>
                        <option value="en" {{ ($userSettings['language'] ?? 'zh') === 'en' ? 'selected' : '' }}>English</option>
                        <option value="ms" {{ ($userSettings['language'] ?? 'zh') === 'ms' ? 'selected' : '' }}>Bahasa Malaysia</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">时区</label>
                    <select name="timezone" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Asia/Kuala_Lumpur" {{ ($userSettings['timezone'] ?? 'Asia/Kuala_Lumpur') === 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>马来西亚时间 (UTC+8)</option>
                        <option value="Asia/Singapore" {{ ($userSettings['timezone'] ?? 'Asia/Kuala_Lumpur') === 'Asia/Singapore' ? 'selected' : '' }}>新加坡时间 (UTC+8)</option>
                        <option value="Asia/Shanghai" {{ ($userSettings['timezone'] ?? 'Asia/Kuala_Lumpur') === 'Asia/Shanghai' ? 'selected' : '' }}>中国时间 (UTC+8)</option>
                        <option value="UTC" {{ ($userSettings['timezone'] ?? 'Asia/Kuala_Lumpur') === 'UTC' ? 'selected' : '' }}>协调世界时 (UTC+0)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">货币</label>
                    <select name="currency" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="MYR" {{ ($userSettings['currency'] ?? 'MYR') === 'MYR' ? 'selected' : '' }}>马来西亚林吉特 (RM)</option>
                        <option value="USD" {{ ($userSettings['currency'] ?? 'MYR') === 'USD' ? 'selected' : '' }}>美元 ($)</option>
                        <option value="SGD" {{ ($userSettings['currency'] ?? 'MYR') === 'SGD' ? 'selected' : '' }}>新加坡元 (S$)</option>
                        <option value="CNY" {{ ($userSettings['currency'] ?? 'MYR') === 'CNY' ? 'selected' : '' }}>人民币 (¥)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">日期格式</label>
                    <select name="date_format" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Y-m-d" {{ ($userSettings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>2024-01-01</option>
                        <option value="d/m/Y" {{ ($userSettings['date_format'] ?? 'Y-m-d') === 'd/m/Y' ? 'selected' : '' }}>01/01/2024</option>
                        <option value="m/d/Y" {{ ($userSettings['date_format'] ?? 'Y-m-d') === 'm/d/Y' ? 'selected' : '' }}>01/01/2024</option>
                        <option value="d-m-Y" {{ ($userSettings['date_format'] ?? 'Y-m-d') === 'd-m-Y' ? 'selected' : '' }}>01-01-2024</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">时间格式</label>
                    <select name="time_format" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="H:i" {{ ($userSettings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : '' }}>24小时制 (14:30)</option>
                        <option value="h:i A" {{ ($userSettings['time_format'] ?? 'H:i') === 'h:i A' ? 'selected' : '' }}>12小时制 (2:30 PM)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 账户安全 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">账户安全</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-900">两步验证</h4>
                        <p class="text-sm text-gray-500">为您的账户添加额外的安全保护</p>
                    </div>
                    <button type="button" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        启用
                    </button>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-900">登录日志</h4>
                        <p class="text-sm text-gray-500">查看最近的登录活动</p>
                    </div>
                    <button type="button" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        查看
                    </button>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-900">API密钥</h4>
                        <p class="text-sm text-gray-500">管理您的API访问密钥</p>
                    </div>
                    <button type="button" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        管理
                    </button>
                </div>
            </div>
        </div>

        <!-- 数据管理 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">数据管理</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-900">导出数据</h4>
                        <p class="text-sm text-gray-500">下载您的账户数据</p>
                    </div>
                    <button type="button" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        导出
                    </button>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-900">清除缓存</h4>
                        <p class="text-sm text-gray-500">清除系统缓存数据</p>
                    </div>
                    <button type="button" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        清除
                    </button>
                </div>
            </div>
        </div>

        <!-- 危险操作 -->
        <div class="bg-white rounded-lg shadow border border-red-200 p-6">
            <h3 class="text-lg font-medium text-red-900 mb-4">危险操作</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-red-900">停用账户</h4>
                        <p class="text-sm text-red-600">暂时停用您的商家账户</p>
                    </div>
                    <button type="button" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        停用
                    </button>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-red-900">删除账户</h4>
                        <p class="text-sm text-red-600">永久删除您的商家账户（不可恢复）</p>
                    </div>
                    <button type="button" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        删除
                    </button>
                </div>
            </div>
        </div>

        <!-- 提交按钮 -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-save mr-2"></i>
                保存设置
            </button>
        </div>
    </form>
</div>
@endsection