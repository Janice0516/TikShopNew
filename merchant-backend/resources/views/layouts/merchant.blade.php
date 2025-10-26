<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '商家后台') - Tiktok Shop Merchant</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <script>
        // 语言切换功能
        function switchLanguage(lang) {
            fetch('/merchant/language/switch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ lang: lang })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 刷新页面以应用新语言
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Language switch failed:', error);
            });
        }
        
        // 页面加载时设置当前语言显示
        document.addEventListener('DOMContentLoaded', function() {
            const languageNames = {
                'zh': '中文',
                'en': 'English', 
                'ms': 'Bahasa Malaysia'
            };
            const currentLang = '{{ app()->getLocale() }}';
            const currentLanguageElement = document.getElementById('current-language');
            if (currentLanguageElement && languageNames[currentLang]) {
                currentLanguageElement.textContent = languageNames[currentLang];
            }
        });
    </script>
</head>
<body class="bg-gray-100">
    <!-- 顶部导航栏 -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-900">Tiktok Shop Merchant</h1>
                    </div>
                </div>
                
                <!-- 语言切换器 -->
                <div class="flex items-center space-x-4">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" 
                                class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:text-gray-900">
                            <i class="fas fa-globe mr-2"></i>
                            <span id="current-language">{{ __('merchant.common.language') }}</span>
                            <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="#" @click="switchLanguage('zh')" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <span class="mr-2">🇨🇳</span>
                                中文
                            </a>
                            <a href="#" @click="switchLanguage('en')" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <span class="mr-2">🇺🇸</span>
                                English
                            </a>
                            <a href="#" @click="switchLanguage('ms')" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <span class="mr-2">🇲🇾</span>
                                Bahasa Malaysia
                            </a>
                        </div>
                    </div>
                    
                    <!-- 用户信息 -->
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700">{{ session('merchant_info.merchantName', '商家') }}</span>
                        <a href="/merchant/logout" 
                           class="ml-4 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            {{ __('merchant.nav.logout') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- 侧边栏 -->
        <aside class="w-64 bg-white shadow-sm min-h-screen">
            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <!-- 仪表盘 -->
                    <a href="/merchant/dashboard" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/dashboard') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        {{ __('merchant.nav.dashboard') }}
                    </a>
                    
                    <!-- 商品管理 -->
                    <a href="/merchant/products" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/products*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-box mr-3"></i>
                        {{ __('merchant.nav.products') }}
                    </a>
                    
                    <!-- 订单管理 -->
                    <a href="/merchant/orders" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/orders*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-receipt mr-3"></i>
                        {{ __('merchant.nav.orders') }}
                    </a>
                    
                    <!-- 选品 -->
                    <a href="/merchant/select-products" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/select-products*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        {{ __('merchant.nav.select_products') }}
                    </a>
                    
                    <!-- 财务管理 -->
                    <a href="/merchant/finance" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/finance*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-dollar-sign mr-3"></i>
                        {{ __('merchant.nav.finance') }}
                    </a>
                    
                    <!-- 提现 -->
                    <a href="/merchant/withdrawals" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/withdrawals*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-wallet mr-3"></i>
                        {{ __('merchant.nav.withdrawals') }}
                    </a>
                    
                    <!-- 店铺管理 -->
                    <a href="/merchant/shop" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/shop*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-store mr-3"></i>
                        {{ __('merchant.nav.shop') }}
                    </a>
                    
                    <!-- 信用评级 -->
                    <a href="/merchant/credit-rating" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/credit-rating*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-shield-alt mr-3"></i>
                        {{ __('merchant.nav.credit_rating') }}
                    </a>
                    
                    <!-- 设置 -->
                    <a href="/merchant/settings" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->is('merchant/settings*') ? 'bg-indigo-100 text-indigo-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-cog mr-3"></i>
                        {{ __('merchant.nav.settings') }}
                    </a>
                </div>
            </nav>
        </aside>

        <!-- 主内容区域 -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    <!-- 通知消息 -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- JavaScript -->
    <script>
        // CSRF Token 设置
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // 设置 Axios 默认配置
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
        }
    </script>
</body>
</html>
