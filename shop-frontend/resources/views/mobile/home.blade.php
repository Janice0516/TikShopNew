@extends('layouts.mobile.app')

@section('title', 'TikTok Shop - 发现好物')

@section('content')
<div class="min-h-screen bg-black text-white">
    <!-- Categories Section -->
    <div class="px-4 py-6">
        <h2 class="text-lg font-semibold mb-4 text-white">分类</h2>
        <div class="flex space-x-4 overflow-x-auto scrollbar-hide pb-2">
            @foreach($categories as $category)
                <a href="{{ route('mobile.categories.show', $category) }}" class="flex-shrink-0 text-center">
                    <div class="w-16 h-16 bg-gray-800 rounded-lg flex items-center justify-center mb-2">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-300">{{ $category->name }}</span>
                </a>
            @endforeach
                                </div>
                        </div>

    <!-- Popular Products Section -->
    <div class="px-4 py-6">
        <h2 class="text-lg font-semibold mb-4 text-white">热门商品</h2>
        <div class="grid grid-cols-2 gap-4">
            @foreach($popularProducts as $product)
                <div class="bg-gray-900 rounded-lg overflow-hidden">
                    <div class="aspect-square bg-gray-800 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                    </div>
                                    <div class="p-3">
                        <h3 class="text-sm font-medium text-white mb-1 line-clamp-2">{{ $product->name }}</h3>
                                        <div class="flex items-center justify-between">
                            <span class="text-pink-500 font-semibold">RM {{ number_format($product->display_price, 2) }}</span>
                            <button onclick="addToCart({{ $product->id }})" class="bg-pink-500 text-white px-3 py-1 rounded-full text-xs hover:bg-pink-600">
                                加入购物车
                            </button>
                        </div>
                    </div>
                                                    </div>
            @endforeach
                                                </div>
                                    </div>
                                    
    <!-- Login Section (if not authenticated) -->
    @guest
    <div class="px-4 py-6">
        <div class="bg-gray-900 rounded-lg p-6 text-center">
            <h3 class="text-lg font-semibold text-white mb-2">登录享受更多优惠</h3>
            <p class="text-gray-400 text-sm mb-4">登录后可以查看个人订单、收藏商品等</p>
            <a href="{{ route('mobile.login') }}" class="block w-full bg-pink-500 text-white text-center py-3 rounded-lg font-medium hover:bg-pink-600 transition-colors">
                立即登录
                                </a>
                            </div>
                                                </div>
    @endguest
</div>
@endsection