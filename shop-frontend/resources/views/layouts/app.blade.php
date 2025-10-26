<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'TikTok Shop')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-DlnbNoFL.css') }}?v={{ time() }}">
    <script src="{{ asset('build/assets/app-Bj43h_rG.js') }}"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* TikTok Shop CSS Variables */
        :root {
            --tux-v2-radius-control-tiny: 4px;
            --tux-v2-radius-control-small: 6px;
            --tux-v2-radius-control-medium: 8px;
            --tux-v2-radius-control-large: 12px;
            --tux-v2-radius-control-xlarge: 16px;
            --tux-v2-radius-control-xxlarge: 20px;
            --tux-v2-radius-control-xxxlarge: 24px;
            --tux-v2-radius-control-huge: 28px;
            --tux-v2-radius-control-massive: 32px;
            --tux-v2-radius-control-giant: 36px;
            --tux-v2-radius-control-colossal: 40px;
            --tux-v2-radius-control-titanic: 44px;
            --tux-v2-radius-control-mega: 48px;
            --tux-v2-radius-control-ultra: 52px;
            --tux-v2-radius-control-super: 56px;
            --tux-v2-radius-control-mega-ultra: 60px;
            --tux-v2-radius-control-super-mega: 64px;
            --tux-v2-radius-control-ultra-super: 68px;
            --tux-v2-radius-control-mega-super: 72px;
            --tux-v2-radius-control-super-ultra: 76px;
            --tux-v2-radius-control-ultra-mega: 80px;
            --tux-v2-radius-control-super-mega-ultra: 84px;
            --tux-v2-radius-control-mega-ultra-super: 88px;
            --tux-v2-radius-control-ultra-super-mega: 92px;
            --tux-v2-radius-control-super-mega-ultra-super: 96px;
            --tux-v2-radius-control-mega-ultra-super-mega: 100px;
            
            --tux-v2-color-ui-text-1: #161823;
            --tux-v2-color-ui-text-2: #8A8B93;
            --tux-v2-color-ui-text-3: #B3B4BA;
            --tux-v2-color-ui-text-4: #D7D8DB;
            --tux-v2-color-ui-text-5: #F1F1F2;
            --tux-v2-color-ui-text-6: #FFFFFF;
            
            --tux-v2-color-ui-shape-primary: #FE4166;
            --tux-v2-color-ui-shape-neutral-1: #F1F1F2;
            --tux-v2-color-ui-shape-neutral-2: #E7E8EA;
            --tux-v2-color-ui-shape-neutral-3: #D7D8DB;
            --tux-v2-color-ui-shape-neutral-4: #B3B4BA;
            --tux-v2-color-ui-shape-text-1-on-primary: #FFFFFF;
            
            --tux-v2-font-family-primary: TikTokFont, Arial, Tahoma, PingFangSC, sans-serif;
            --tux-v2-font-size-p1-regular: 14px;
            --tux-v2-font-size-h4-semibold: 16px;
        }
        
        .P1-Regular {
            font-family: var(--tux-v2-font-family-primary);
            font-size: var(--tux-v2-font-size-p1-regular);
            font-weight: 400;
        }
        
        .H4-Semibold {
            font-family: var(--tux-v2-font-family-primary);
            font-size: var(--tux-v2-font-size-h4-semibold);
            font-weight: 600;
        }
        
        .text-color-UIText1 {
            color: var(--tux-v2-color-ui-text-1);
        }
        
        .text-color-UIShapeText1OnPrimary {
            color: var(--tux-v2-color-ui-shape-text-1-on-primary);
        }
        
        .background-color-UIShapePrimary {
            background-color: var(--tux-v2-color-ui-shape-primary);
        }
        
        .hover\:background-color-UIShapeNeutral4:hover {
            background-color: var(--tux-v2-color-ui-shape-neutral-4);
        }
        
        /* Hide scrollbar for webkit browsers */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;  /* Chrome, Safari and Opera */
        }
        
        .product-card {
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }
        
        /* Category text wrapping */
        .category-text {
            word-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
            line-height: 1.2;
        }
        
        /* Fixed sidebar */
        .sidebar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 30;
        }
        
        /* TikTok Shop Style */
        .tiktok-black {
            background-color: #000000;
        }
        
        .tiktok-white {
            background-color: #ffffff;
        }
        
        .tiktok-red {
            background-color: #ff0050;
        }
        
        .tiktok-gradient {
            background: linear-gradient(135deg, #ff0050, #00f2ea);
        }
        
        .tiktok-gradient-hero {
            background: linear-gradient(135deg, #ff0050, #ff1744, #ff5722);
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        @media (max-width: 768px) {
            .desktop-only {
                display: none !important;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-only {
                display: none !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- TikTok Shop Official Header -->
    <header class="desktop-only fixed top-0 left-0 right-0 bg-white z-50" style="border-bottom: 1px solid #e5e5e5;">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo Section -->
                <div class="flex items-center">
                    <a href="{{ app('device_type') === 'mobile' ? route('mobile.home') : route('desktop.home') }}" class="flex items-center">
                        <div class="w-8 h-8 bg-black rounded flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-black">TikTok Shop</span>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-lg mx-8">
                    <div class="relative">
                        <input type="text" placeholder="搜索商品、品牌、店铺" 
                               class="w-full h-10 px-4 pl-10 pr-4 text-sm bg-gray-50 border border-gray-200 rounded-full focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Language -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-600 hover:text-black text-sm">
                            <span>{{ app()->getLocale() === 'zh' ? '中文' : (app()->getLocale() === 'ms' ? 'Bahasa' : 'English') }}</span>
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg py-1 z-50 border">
                            <a href="{{ route('locale', 'zh') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">中文</a>
                            <a href="{{ route('locale', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">English</a>
                            <a href="{{ route('locale', 'ms') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Bahasa</a>
                        </div>
                    </div>

                    @auth
                        <!-- Cart -->
                        <a href="{{ app('device_type') === 'mobile' ? route('mobile.cart') : route('desktop.cart') }}" class="relative text-gray-600 hover:text-black">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center cart-count">
                                {{ Auth::user()->cartItems->count() ?? 0 }}
                            </span>
                        </a>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-600 hover:text-black">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm">{{ Auth::user()->name }}</span>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border">
                                <a href="{{ app('device_type') === 'mobile' ? route('mobile.profile') : route('desktop.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    个人中心
                                </a>
                                <a href="{{ app('device_type') === 'mobile' ? route('mobile.orders.index') : route('desktop.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    我的订单
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ app('device_type') === 'mobile' ? route('mobile.logout') : route('desktop.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        退出登录
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                                            <div class="flex items-center flex-none rounded-full" style="box-shadow:0px 2px 10px 0px rgba(0, 0, 0, 0.07); padding: 8px 16px !important;" data-testid="header-container" data-timestamp="{{ time() }}">
                            <div class="tux-web-canary tux-interaction-container-Lhgppe tux-interaction-container--opacity-zoYF7b tux-web-canary tux-button-w_xpIs tux-button--medium-eR6YNS" style="height: 32px; background-color: transparent; color: var(--tux-v2-color-ui-text-1);" data-testid="tux-web-button-container">
                                <button type="button" class="tux-button__element-Iieopq" style="padding-left:0px;padding-right:0px" data-testid="tux-web-button">
                                    <div class="rounded-full hover:background-color-UIShapeNeutral4 flex items-center P1-Regular text-color-UIText1" style="padding: 0 7.255px !important; height: 32px !important;">
                                        <svg fill="currentColor" font-size="16px" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em">
                                            <path d="M20 9c0-1.1.9-2 2-2h4a2 2 0 1 1 0 4h-4a2 2 0 0 1-2-2ZM18 38.5c0-.83.67-1.5 1.5-1.5h9a1.5 1.5 0 0 1 0 3h-9a1.5 1.5 0 0 1-1.5-1.5Z"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8 10.6c0-3.36 0-5.04.65-6.32a6 6 0 0 1 2.63-2.63C12.56 1 14.23 1 17.6 1h12.8c3.36 0 5.04 0 6.32.65a6 6 0 0 1 2.63 2.63C40 5.56 40 7.24 40 10.6v26.8c0 3.36 0 5.04-.65 6.32a6 6 0 0 1-2.63 2.63c-1.28.65-2.96.65-6.32.65H17.6c-3.36 0-5.04 0-6.32-.65a6 6 0 0 1-2.63-2.63C8 42.44 8 40.75 8 37.4V10.6ZM17.6 5h12.8c1.75 0 2.82 0 3.62.07.37.03.6.07.73.1.48.11.96.58 1.08 1.08.03.14.07.36.1.73.07.8.07 1.87.07 3.62v26.8c0 1.75 0 2.82-.07 3.62-.03.37-.07.6-.1.73-.11.48-.58.96-1.08 1.08-.14.03-.36.07-.73.1-.8.07-1.87.07-3.62.07H17.6c-1.75 0-2.82 0-3.62-.07-.37-.03-.6-.07-.73-.1-.5-.12-.97-.6-1.08-1.08a5.11 5.11 0 0 1-.1-.73c-.07-.8-.07-1.87-.07-3.62V10.6c0-1.75 0-2.82.07-3.62.03-.37.07-.6.1-.73.12-.5.6-.97 1.08-1.08.14-.03.36-.07.73-.1C14.78 5 15.85 5 17.6 5Z"></path>
                                        </svg>
                                        <div class="ml-4 font-medium whitespace-nowrap">Get app</div>
                                    </div>
                                </button>
                            </div>
                            <div class="w-4 h-20 border-none" style="margin-right: 16px !important;"></div>
                            <div class="tux-web-canary tux-interaction-container-Lhgppe tux-interaction-container--opacity-zoYF7b tux-web-canary tux-button-w_xpIs tux-button--small-dy6AZX" style="height: 32px; background-color: transparent; color: var(--tux-v2-color-ui-text-1);" data-testid="tux-web-button-container">
                                <a href="{{ app('device_type') === 'mobile' ? route('mobile.login') : route('desktop.login') }}" class="tux-button__element-Iieopq" style="padding-left:0px;padding-right:0px" data-testid="tux-web-button" aria-expanded="false" aria-haspopup="dialog">
                                    <div class="flex items-center rounded-full hover:bg-[#FE4166] H4-Semibold text-color-UIShapeText1OnPrimary background-color-UIShapePrimary" style="padding: 0 12px !important; height: 32px !important;">Log in</div>
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Header - TikTok Black Theme -->
    <header class="mobile-only fixed top-0 left-0 right-0 bg-black z-50">
        <div class="flex items-center justify-between h-14 px-4">
            <a href="{{ app('device_type') === 'mobile' ? route('mobile.home') : route('desktop.home') }}" class="flex items-center">
                <div class="w-6 h-6 bg-white rounded flex items-center justify-center mr-2">
                    <svg class="w-4 h-4 text-black" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">TikTok Shop</span>
            </a>
            
            <div class="flex items-center space-x-3">
                <!-- Search Icon -->
                <button class="text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                
                <!-- Cart -->
                <a href="{{ app('device_type') === 'mobile' ? route('mobile.cart') : route('desktop.cart') }}" class="relative text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    @auth
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center cart-count">
                            {{ Auth::user()->cartItems->count() ?? 0 }}
                        </span>
                    @endauth
                </a>
                
                @auth
                    <a href="{{ app('device_type') === 'mobile' ? route('mobile.profile') : route('desktop.profile') }}">
                        <div class="w-6 h-6 bg-gray-600 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </a>
                @else
                    <a href="{{ app('device_type') === 'mobile' ? route('mobile.login') : route('desktop.login') }}">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16 mobile-only:pt-14 mobile-only:bg-black mobile-only:min-h-screen">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-only mobile-nav bg-black border-t border-gray-800">
        <div class="flex justify-around py-2">
            <a href="{{ app('device_type') === 'mobile' ? route('mobile.home') : route('desktop.home') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs">首页</span>
            </a>
            <a href="{{ app('device_type') === 'mobile' ? route('mobile.categories') : route('desktop.categories') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="text-xs">分类</span>
            </a>
            <a href="{{ app('device_type') === 'mobile' ? route('mobile.cart') : route('desktop.cart') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white relative">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
                <span class="text-xs">购物车</span>
                @auth
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center cart-count">
                        {{ Auth::user()->cartItems->count() ?? 0 }}
                    </span>
                @endauth
            </a>
            @auth
                <a href="{{ app('device_type') === 'mobile' ? route('mobile.orders.index') : route('desktop.orders.index') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span class="text-xs">订单</span>
                </a>
                <a href="{{ app('device_type') === 'mobile' ? route('mobile.profile') : route('desktop.profile') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-xs">我的</span>
                </a>
            @else
                <a href="{{ app('device_type') === 'mobile' ? route('mobile.login') : route('desktop.login') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="text-xs">登录</span>
                </a>
            @endauth
        </div>
    </nav>

    <!-- Scripts -->
    <script>
        // 简单的购物车功能
        function addToCart(productId, quantity = 1) {
            fetch('{{ app("device_type") === "mobile" ? route("mobile.cart.add") : route("desktop.cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 显示成功消息
                    showNotification('商品已添加到购物车', 'success');
                    // 更新购物车数量
                    updateCartCount();
                } else {
                    showNotification(data.message || '添加失败', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('网络错误', 'error');
            });
        }

        function showNotification(message, type = 'info') {
            // 简单的通知实现
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-50 px-4 py-2 rounded-md text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function updateCartCount() {
            // 更新购物车数量显示
            const currentPath = window.location.pathname;
            const isDesktop = currentPath.startsWith('/desktop/');
            const isMobile = currentPath.startsWith('/mobile/');
            
            let cartCountRoute = '{{ app("device_type") === "mobile" ? route("mobile.cart.count") : route("desktop.cart.count") }}';
            if (isDesktop) {
                cartCountRoute = '{{ route("desktop.cart.count") }}';
            } else if (isMobile) {
                cartCountRoute = '{{ route("mobile.cart.count") }}';
            }
            
            fetch(cartCountRoute)
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count || 0;
                    }
                });
        }
        
        // 设备切换功能
        function switchDevice(device) {
            window.location.href = `/switch-device/${device}`;
        }
        
        // 检测设备类型并显示切换按钮
        function showDeviceSwitch() {
            const currentPath = window.location.pathname;
            const isDesktop = currentPath.startsWith('/desktop/');
            const isMobile = currentPath.startsWith('/mobile/');
            
            if (isDesktop || isMobile) {
                // 显示设备切换按钮
                const switchButton = document.createElement('div');
                switchButton.className = 'fixed top-4 right-4 z-50 bg-gray-800 text-white px-3 py-2 rounded-lg text-sm';
                switchButton.innerHTML = `
                    <button onclick="switchDevice('${isDesktop ? 'mobile' : 'desktop'}')" class="hover:text-red-500">
                        Switch to ${isDesktop ? 'Mobile' : 'Desktop'}
                    </button>
                `;
                document.body.appendChild(switchButton);
            }
        }
        
        // 页面加载完成后显示切换按钮
        window.addEventListener('load', showDeviceSwitch);
    </script>
</body>
</html>
