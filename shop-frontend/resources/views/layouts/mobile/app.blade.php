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
        
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
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
<body class="font-sans antialiased bg-black">
    <!-- TikTok Shop Mobile Header -->
    <header class="fixed top-0 left-0 right-0 bg-black z-50">
        <div class="flex items-center justify-between h-14 px-4">
            <a href="{{ route('mobile.home') }}" class="flex items-center">
                <div class="w-6 h-6 bg-white rounded flex items-center justify-center mr-2">
                    <svg class="w-4 h-4 text-black" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">TikTok Shop</span>
            </a>

            <div class="flex items-center space-x-4">
                <!-- Search -->
                <button class="text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                
                <!-- Cart -->
                <a href="{{ route('mobile.cart') }}" class="relative text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center" id="cart-count">0</span>
                </a>
                
                @auth
                    <a href="{{ route('mobile.profile') }}">
                        <div class="w-6 h-6 bg-gray-600 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </a>
                @else
                    <a href="{{ route('mobile.login') }}">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-14 bg-black min-h-screen">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav bg-black border-t border-gray-800">
        <div class="flex justify-around py-2">
            <a href="{{ route('mobile.home') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs">首页</span>
            </a>
            <a href="{{ route('mobile.categories') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="text-xs">分类</span>
            </a>
            <a href="{{ route('mobile.cart') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white relative">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
                <span class="text-xs">购物车</span>
                <span class="absolute -top-1 -right-1 bg-pink-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center" id="cart-count-bottom">0</span>
            </a>
            @auth
                <a href="{{ route('mobile.profile') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-xs">我的</span>
                </a>
            @else
                <a href="{{ route('mobile.login') }}" class="flex flex-col items-center py-2 px-3 text-gray-400 hover:text-white">
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
            fetch('{{ route("mobile.cart.add") }}', {
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
            fetch('{{ route("mobile.cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.getElementById('cart-count');
                    const cartCountBottom = document.getElementById('cart-count-bottom');
                    if (cartCount) {
                        cartCount.textContent = data.count || 0;
                    }
                    if (cartCountBottom) {
                        cartCountBottom.textContent = data.count || 0;
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
