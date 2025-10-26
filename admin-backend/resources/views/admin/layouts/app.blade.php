<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', '管理后台') - TikTok Shop Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- 侧边栏 -->
        <aside class="w-64 bg-white shadow-lg">
            <div class="p-6">
                <h1 class="text-xl font-bold text-gray-800">TikTok Shop Admin</h1>
            </div>
            
            <nav class="mt-6">
                <div class="px-6 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">主要功能</h3>
                </div>
                
                <div class="mt-2">
                    <a href="/admin/dashboard" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/dashboard') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        仪表盘
                    </a>
                    
                    <a href="/admin/merchants" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/merchants*') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-store mr-3"></i>
                        商家管理
                    </a>
                    
                    <a href="/admin/orders" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/orders*') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        订单管理
                    </a>
                    
                    <a href="/admin/products" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/products*') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-box mr-3"></i>
                        商品管理
                    </a>
                    
                    <a href="/admin/users" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/users*') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-users mr-3"></i>
                        用户管理
                    </a>
                    
                    <a href="/admin/categories" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/categories*') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-tags mr-3"></i>
                        分类管理
                    </a>
                </div>
                
                <div class="px-6 py-2 mt-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">系统管理</h3>
                </div>
                
                <div class="mt-2">
                    <a href="/admin/finance" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/finance*') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-chart-line mr-3"></i>
                        财务管理
                    </a>
                    
                    <a href="/admin/withdrawals" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/withdrawals*') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-money-bill-wave mr-3"></i>
                        提现管理
                    </a>
                    
                    <a href="/admin/settings" 
                       class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->is('admin/settings*') ? 'bg-gray-50 text-gray-900 border-r-2 border-blue-500' : '' }}">
                        <i class="fas fa-cog mr-3"></i>
                        系统设置
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- 主内容区域 -->
        <div class="flex-1 flex flex-col">
            <!-- 顶部导航栏 -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">@yield('page-title', '管理后台')</h2>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- 通知 -->
                            <button class="p-2 text-gray-400 hover:text-gray-500">
                                <i class="fas fa-bell"></i>
                            </button>
                            
                            <!-- 用户菜单 -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" 
                                        class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr(Auth::guard('admin')->user()->name, 0, 1) }}
                                    </div>
                                    <span class="ml-2">{{ Auth::guard('admin')->user()->name }}</span>
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
                                    <a href="/admin/profile" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>
                                        个人资料
                                    </a>
                                    <a href="/admin/settings" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i>
                                        设置
                                    </a>
                                    <hr class="my-1">
                                    <a href="/admin/logout" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        退出登录
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- 页面内容 -->
            <main class="flex-1 p-6">
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

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
