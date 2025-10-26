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
            --tux-v2-color-ui-text-1: #161823;
            --tux-v2-color-ui-text-2: #8A8B93;
            --tux-v2-color-ui-text-3: #B3B4BA;
            --tux-v2-color-ui-text-4: #D0D1D6;
            --tux-v2-color-ui-shape-primary: #FE4166;
            --tux-v2-color-ui-shape-neutral-1: #FFFFFF;
            --tux-v2-color-ui-shape-neutral-2: #F8F8F9;
            --tux-v2-color-ui-shape-neutral-3: #F1F1F2;
            --tux-v2-color-ui-shape-neutral-4: #B3B4BA;
            --tux-v2-color-ui-shape-text-1-on-primary: #FFFFFF;
            --tux-v2-font-family-primary: TikTokFont, Arial, Tahoma, PingFangSC, sans-serif;
            --tux-v2-font-size-p1-regular: 14px;
            --tux-v2-font-size-h4-semibold: 16px;
        }
        
        .P1-Regular { font-size: var(--tux-v2-font-size-p1-regular); font-family: var(--tux-v2-font-family-primary); }
        .H4-Semibold { font-size: var(--tux-v2-font-size-h4-semibold); font-family: var(--tux-v2-font-family-primary); font-weight: 600; }
        .text-color-UIText1 { color: var(--tux-v2-color-ui-text-1); }
        .text-color-UIShapeText1OnPrimary { color: var(--tux-v2-color-ui-shape-text-1-on-primary); }
        .background-color-UIShapePrimary { background-color: var(--tux-v2-color-ui-shape-primary); }
        .hover\:background-color-UIShapeNeutral4:hover { background-color: var(--tux-v2-color-ui-shape-neutral-4); }
        
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        .notification.show {
            transform: translateX(0);
        }
        .notification.success {
            background-color: #10B981;
        }
        .notification.error {
            background-color: #EF4444;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- TikTok Shop Desktop Header -->
    <header class="fixed top-0 left-0 right-0 bg-white z-50" style="border-bottom: 1px solid #e5e5e5;">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo Section -->
                <div class="flex items-center">
                    <a href="{{ route('desktop.home') }}" class="flex items-center">
                        <div class="w-8 h-8 bg-black rounded flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-black">TikTok Shop</span>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-8">
                    <div class="relative">
                        <input type="text" placeholder="搜索商品..." 
                               class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-gray-100 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Cart -->
                        <a href="{{ route('desktop.cart') }}" class="relative text-gray-600 hover:text-black">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" id="cart-count">0</span>
                        </a>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-600 hover:text-black">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border">
                                <a href="{{ route('desktop.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    个人中心
                                </a>
                                <a href="{{ route('desktop.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    我的订单
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('desktop.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        退出登录
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Login/Register Buttons -->
                        <div class="flex items-center flex-none rounded-full" style="box-shadow:0px 2px 10px 0px rgba(0, 0, 0, 0.07); width: 179.49px !important; height: 44px !important;" data-testid="header-container" data-timestamp="{{ time() }}">
                            <div class="tux-web-canary tux-interaction-container-Lhgppe tux-interaction-container--opacity-zoYF7b tux-web-canary tux-button-w_xpIs tux-button--medium-eR6YNS" style="height: 32px; background-color: transparent; color: var(--tux-v2-color-ui-text-1);" data-testid="tux-web-button-container">
                                <button type="button" class="tux-button__element-Iieopq" style="padding-left:0px;padding-right:0px" data-testid="tux-web-button">
                                    <div class="rounded-full hover:background-color-UIShapeNeutral4 flex items-center P1-Regular text-color-UIText1" style="width: 88.52px !important; height: 32.20px !important; font-size: 14px !important;">
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
                                <a href="{{ route('desktop.login') }}" class="tux-button__element-Iieopq" style="padding-left:0px;padding-right:0px" data-testid="tux-web-button" aria-expanded="false" aria-haspopup="dialog">
                                    <div class="flex items-center justify-center rounded-full hover:bg-[#FE4166] H4-Semibold text-color-UIShapeText1OnPrimary background-color-UIShapePrimary" style="width: 66.98px !important; height: 32px !important; font-size: 15px !important;">Log in</div>
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script>
        // 简单的购物车功能
        function addToCart(productId, quantity = 1) {
            fetch('{{ route("desktop.cart.add") }}', {
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
                showNotification('网络错误，请重试', 'error');
            });
        }

        function showNotification(message, type = 'success') {
            // 创建通知元素
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            // 添加到页面
            document.body.appendChild(notification);
            
            // 显示动画
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // 3秒后移除
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        function updateCartCount() {
            // 更新购物车数量显示
            fetch('{{ route("desktop.cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count || 0;
                    }
                })
                .catch(error => {
                    console.error('Error updating cart count:', error);
                });
        }

        // 页面加载时更新购物车数量
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>
</body>
</html>
