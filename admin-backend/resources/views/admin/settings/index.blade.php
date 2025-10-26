@extends('admin.layouts.app')

@section('title', '系统设置')
@section('page-title', '系统设置')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">系统设置</h1>
            <p class="text-gray-600">管理平台系统配置</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="resetSettings()" 
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <i class="fas fa-undo mr-2"></i>
                重置设置
            </button>
        </div>
    </div>

    <!-- 设置分组导航 -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                @foreach($allGroups as $groupName)
                <a href="{{ route('admin.settings.index', ['group' => $groupName]) }}" 
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $group === $groupName ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    @switch($groupName)
                        @case('general')
                            <i class="fas fa-cog mr-2"></i>基本设置
                            @break
                        @case('payment')
                            <i class="fas fa-credit-card mr-2"></i>支付设置
                            @break
                        @case('email')
                            <i class="fas fa-envelope mr-2"></i>邮件设置
                            @break
                        @case('sms')
                            <i class="fas fa-sms mr-2"></i>短信设置
                            @break
                        @case('notification')
                            <i class="fas fa-bell mr-2"></i>通知设置
                            @break
                        @case('security')
                            <i class="fas fa-shield-alt mr-2"></i>安全设置
                            @break
                        @default
                            <i class="fas fa-cog mr-2"></i>{{ ucfirst($groupName) }}
                    @endswitch
                </a>
                @endforeach
            </nav>
        </div>

        <!-- 设置表单 -->
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6">
            @csrf
            <input type="hidden" name="group" value="{{ $group }}">
            
            <div class="space-y-6">
                @if($group === 'general')
                    <!-- 基本设置 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">网站名称</label>
                            <input type="text" 
                                   name="settings[site_name]" 
                                   value="{{ $settings->where('key', 'site_name')->first()->value ?? 'TikShop 管理平台' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">网站描述</label>
                            <input type="text" 
                                   name="settings[site_description]" 
                                   value="{{ $settings->where('key', 'site_description')->first()->value ?? '专业的电商管理平台' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">默认货币</label>
                            <select name="settings[default_currency]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="MYR" {{ ($settings->where('key', 'default_currency')->first()->value ?? 'MYR') === 'MYR' ? 'selected' : '' }}>马来西亚林吉特 (MYR)</option>
                                <option value="USD" {{ ($settings->where('key', 'default_currency')->first()->value ?? 'MYR') === 'USD' ? 'selected' : '' }}>美元 (USD)</option>
                                <option value="CNY" {{ ($settings->where('key', 'default_currency')->first()->value ?? 'MYR') === 'CNY' ? 'selected' : '' }}>人民币 (CNY)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">时区</label>
                            <select name="settings[timezone]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="Asia/Kuala_Lumpur" {{ ($settings->where('key', 'timezone')->first()->value ?? 'Asia/Kuala_Lumpur') === 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>马来西亚时间</option>
                                <option value="UTC" {{ ($settings->where('key', 'timezone')->first()->value ?? 'Asia/Kuala_Lumpur') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="Asia/Shanghai" {{ ($settings->where('key', 'timezone')->first()->value ?? 'Asia/Kuala_Lumpur') === 'Asia/Shanghai' ? 'selected' : '' }}>中国时间</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">默认语言</label>
                            <select name="settings[language]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="zh" {{ ($settings->where('key', 'language')->first()->value ?? 'zh') === 'zh' ? 'selected' : '' }}>中文</option>
                                <option value="en" {{ ($settings->where('key', 'language')->first()->value ?? 'zh') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="ms" {{ ($settings->where('key', 'language')->first()->value ?? 'zh') === 'ms' ? 'selected' : '' }}>Bahasa Malaysia</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="settings[maintenance_mode]" 
                                   value="1"
                                   {{ ($settings->where('key', 'maintenance_mode')->first()->value ?? '0') === '1' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">维护模式</label>
                        </div>
                    </div>

                @elseif($group === 'payment')
                    <!-- 支付设置 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="settings[payment_enabled]" 
                                   value="1"
                                   {{ ($settings->where('key', 'payment_enabled')->first()->value ?? '1') === '1' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">启用支付</label>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">默认支付方式</label>
                            <select name="settings[default_payment_method]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="credit_card" {{ ($settings->where('key', 'default_payment_method')->first()->value ?? 'credit_card') === 'credit_card' ? 'selected' : '' }}>信用卡</option>
                                <option value="bank_transfer" {{ ($settings->where('key', 'default_payment_method')->first()->value ?? 'credit_card') === 'bank_transfer' ? 'selected' : '' }}>银行转账</option>
                                <option value="ewallet" {{ ($settings->where('key', 'default_payment_method')->first()->value ?? 'credit_card') === 'ewallet' ? 'selected' : '' }}>电子钱包</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">支付超时时间(分钟)</label>
                            <input type="number" 
                                   name="settings[payment_timeout]" 
                                   value="{{ $settings->where('key', 'payment_timeout')->first()->value ?? '30' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="settings[refund_enabled]" 
                                   value="1"
                                   {{ ($settings->where('key', 'refund_enabled')->first()->value ?? '1') === '1' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">启用退款</label>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">退款处理时间(天)</label>
                            <input type="number" 
                                   name="settings[refund_timeout]" 
                                   value="{{ $settings->where('key', 'refund_timeout')->first()->value ?? '7' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                @elseif($group === 'email')
                    <!-- 邮件设置 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SMTP服务器</label>
                            <input type="text" 
                                   name="settings[smtp_host]" 
                                   value="{{ $settings->where('key', 'smtp_host')->first()->value ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SMTP端口</label>
                            <input type="number" 
                                   name="settings[smtp_port]" 
                                   value="{{ $settings->where('key', 'smtp_port')->first()->value ?? '587' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SMTP用户名</label>
                            <input type="text" 
                                   name="settings[smtp_username]" 
                                   value="{{ $settings->where('key', 'smtp_username')->first()->value ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SMTP密码</label>
                            <input type="password" 
                                   name="settings[smtp_password]" 
                                   value="{{ $settings->where('key', 'smtp_password')->first()->value ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">加密方式</label>
                            <select name="settings[smtp_encryption]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="tls" {{ ($settings->where('key', 'smtp_encryption')->first()->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($settings->where('key', 'smtp_encryption')->first()->value ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="" {{ ($settings->where('key', 'smtp_encryption')->first()->value ?? 'tls') === '' ? 'selected' : '' }}>无</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">发件人邮箱</label>
                            <input type="email" 
                                   name="settings[from_email]" 
                                   value="{{ $settings->where('key', 'from_email')->first()->value ?? 'noreply@tiktokshop.com' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">发件人名称</label>
                            <input type="text" 
                                   name="settings[from_name]" 
                                   value="{{ $settings->where('key', 'from_name')->first()->value ?? 'TikShop' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                @elseif($group === 'security')
                    <!-- 安全设置 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">登录尝试次数限制</label>
                            <input type="number" 
                                   name="settings[login_attempts]" 
                                   value="{{ $settings->where('key', 'login_attempts')->first()->value ?? '5' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">锁定时间(分钟)</label>
                            <input type="number" 
                                   name="settings[lockout_duration]" 
                                   value="{{ $settings->where('key', 'lockout_duration')->first()->value ?? '15' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">密码最小长度</label>
                            <input type="number" 
                                   name="settings[password_min_length]" 
                                   value="{{ $settings->where('key', 'password_min_length')->first()->value ?? '8' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">会话超时时间(分钟)</label>
                            <input type="number" 
                                   name="settings[session_timeout]" 
                                   value="{{ $settings->where('key', 'session_timeout')->first()->value ?? '120' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="settings[password_require_special]" 
                                   value="1"
                                   {{ ($settings->where('key', 'password_require_special')->first()->value ?? '1') === '1' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">密码需要特殊字符</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="settings[two_factor_auth]" 
                                   value="1"
                                   {{ ($settings->where('key', 'two_factor_auth')->first()->value ?? '0') === '1' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">双因子认证</label>
                        </div>
                    </div>

                @else
                    <!-- 其他设置组 -->
                    <div class="text-center py-12">
                        <i class="fas fa-cog text-4xl text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium text-gray-500">该设置组暂无配置项</p>
                        <p class="text-sm text-gray-400">请联系开发人员添加相关设置</p>
                    </div>
                @endif
            </div>

            <!-- 保存按钮 -->
            <div class="mt-8 flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    保存设置
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 重置设置表单 -->
<form id="reset-form" method="POST" action="{{ route('admin.settings.reset') }}" style="display: none;">
    @csrf
    <input type="hidden" name="group" value="{{ $group }}">
</form>

<script>
function resetSettings() {
    if (confirm('确定要重置当前分组的设置吗？此操作将恢复为默认值，无法撤销！')) {
        document.getElementById('reset-form').submit();
    }
}
</script>
@endsection
