@extends('layouts.app')

@section('title', 'TikTok Shop - 发现好物')

@section('content')
<div class="min-h-screen bg-white mobile-only:bg-black">
    <!-- Main Content Layout -->
        <!-- Left Sidebar -->
        <aside class="desktop-only w-64 bg-gray-50 min-h-screen pt-16 sidebar-fixed">
            <div class="p-6">
                <!-- Category Button -->
                <div class="mb-8">
                    <a href="{{ route('categories') }}" class="flex items-center text-gray-700 mobile-only:text-gray-300 hover:text-black mb-4">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Category
                    </a>
                    <button class="flex items-center text-gray-700 mobile-only:text-gray-300 hover:text-black">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        客服
                    </button>
                </div>

                <!-- Login Button -->
                <div class="mb-8">
                    <a href="{{ app('device_type') === 'mobile' ? route('mobile.login') : route('desktop.login') }}" class="block w-full bg-red-500 text-white text-center py-3 rounded-lg font-medium hover:bg-red-600 transition-colors">
                        Log in
                    </a>
                </div>

            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 pt-16 ml-64 desktop-only:ml-64">
            <div class="max-w-screen-xl mx-auto px-6 py-8">
                <!-- Mobile Category Navigation -->
                <div class="mobile-only mb-6">
                    <div class="flex space-x-4 overflow-x-auto pb-2">
                        <button class="flex-shrink-0 px-4 py-2 bg-gray-800 text-white rounded-full text-sm font-medium">
                            All
                        </button>
                        <button class="flex-shrink-0 px-4 py-2 bg-gray-700 text-gray-300 rounded-full text-sm font-medium hover:bg-gray-600">
                            Womenswear & Underwear
                        </button>
                        <button class="flex-shrink-0 px-4 py-2 bg-gray-700 text-gray-300 rounded-full text-sm font-medium hover:bg-gray-600">
                            Phones & Electronics
                        </button>
                        <button class="flex-shrink-0 px-4 py-2 bg-gray-700 text-gray-300 rounded-full text-sm font-medium hover:bg-gray-600">
                            Fashion Accessories
                        </button>
                        <button class="flex-shrink-0 px-4 py-2 bg-gray-700 text-gray-300 rounded-full text-sm font-medium hover:bg-gray-600">
                            Menswear & Underwear
                        </button>
                        <button class="flex-shrink-0 px-4 py-2 bg-gray-700 text-gray-300 rounded-full text-sm font-medium hover:bg-gray-600">
                            Home Supplies
                        </button>
                        <button class="flex-shrink-0 px-4 py-2 bg-gray-700 text-gray-300 rounded-full text-sm font-medium hover:bg-gray-600">
                            Beauty & Skincare
                        </button>
                        <button class="flex-shrink-0 px-4 py-2 bg-gray-700 text-gray-300 rounded-full text-sm font-medium hover:bg-gray-600">
                            Sports & Outdoor
                        </button>
                    </div>
                </div>

                <!-- Categories Section -->
                <section class="mb-12">
                    <h2 class="text-3xl font-bold text-black mobile-only:text-white mb-6">Categories</h2>
                    <!-- Desktop Categories -->
                    <div class="desktop-only flex space-x-4 overflow-x-auto pb-4">
                        <!-- Category Items -->
                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-pink-200 to-pink-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Womenswear & Underwear</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-blue-200 to-blue-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Phones & Electronics</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-yellow-200 to-yellow-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Fashion Accessories</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Menswear & Underwear</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-green-200 to-green-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Home Supplies</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-purple-200 to-purple-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Beauty & Personal Care</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-indigo-200 to-indigo-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Shoes</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-red-200 to-red-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Sports & Outdoor</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-[117.59px] h-[117.59px] bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <div class="w-[100px] h-[100px] bg-gradient-to-br from-teal-200 to-teal-300 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-teal-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium leading-tight text-center block max-w-[117.59px] category-text">Luggage & Bags</span>
                        </div>

                        <!-- Arrow Button -->
                        <div class="flex-shrink-0 flex items-center">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Mobile Categories Grid -->
                <div class="mobile-only mb-12">
                    <div class="flex space-x-4 overflow-x-auto pb-4">
                        <!-- Mobile Category Items -->
                        <div class="flex-shrink-0 text-center">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-300 font-medium leading-tight text-center block max-w-20">Womenswear & Underwear</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-300 font-medium leading-tight text-center block max-w-20">Phones & Electronics</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-300 font-medium leading-tight text-center block max-w-20">Fashion Accessories</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-300 font-medium leading-tight text-center block max-w-20">Menswear & Underwear</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-300 font-medium leading-tight text-center block max-w-20">Home Supplies</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-300 font-medium leading-tight text-center block max-w-20">Beauty & Skincare</span>
                        </div>

                        <div class="flex-shrink-0 text-center">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-300 font-medium leading-tight text-center block max-w-20">Sports & Outdoor</span>
                        </div>
                    </div>
                </div>

                <!-- Savings for you Section -->
                <section class="mb-12">
                    <h2 class="text-3xl font-bold text-black mobile-only:text-white mb-6">Savings for you</h2>
                    <div class="flex space-x-4 overflow-x-auto pb-4">
                        @forelse($savingsProducts as $item)
                            @php
                                $product = $item->product ?? $item;
                                $displayTitle = method_exists($item, 'display_title') ? $item->display_title : $product->name;
                                $displayPrice = method_exists($item, 'display_price') ? $item->display_price : $product->price;
                                $displayOriginalPrice = method_exists($item, 'display_original_price') ? $item->display_original_price : $product->original_price;
                                $displayRating = method_exists($item, 'display_rating') ? $item->display_rating : $product->rating;
                                $displaySalesCount = method_exists($item, 'display_sales_count') ? $item->display_sales_count : $product->sales_count;
                            @endphp
                            <div class="flex-shrink-0 w-64 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                <a href="{{ route('products.show', $product) }}">
                                    <!-- Product Image -->
                                    <div class="aspect-w-1 aspect-h-1 bg-gray-100">
                                        @if($product->images && count($product->images) > 0)
                                            <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <div class="text-center">
                                                    <div class="w-16 h-16 bg-gray-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-600">{{ Str::limit($product->name, 15) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="p-3">
                                        <h3 class="text-sm font-medium text-gray-900 mobile-only:text-white mb-2 line-clamp-3">{{ $displayTitle }}</h3>
                                        
                                        <!-- Rating -->
                                        <div class="flex items-center mb-2">
                                            <div class="flex text-yellow-400">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i < floor($displayRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-600 mobile-only:text-gray-400 ml-1">{{ number_format($displayRating, 1) }}</span>
                                        </div>
                                        
                                        <!-- Sold Count -->
                                        <div class="text-xs text-gray-600 mobile-only:text-gray-400 mb-2">
                                            {{ number_format($displaySalesCount / 1000, 1) }}K sold
                                        </div>
                                        
                                        <!-- Price -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                       <span class="text-lg font-bold text-red-500">RM {{ number_format($displayPrice, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <!-- Fallback static products -->
                            <div class="flex-shrink-0 w-64 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                <a href="#">
                                    <div class="aspect-w-1 aspect-h-1 bg-gray-100">
                                        <div class="w-full h-48 bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center">
                                            <div class="text-center">
                                                <div class="w-16 h-16 bg-yellow-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                    </svg>
                                                </div>
                                                <span class="text-xs text-gray-600">Sample Product</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h3 class="text-sm font-medium text-gray-900 mobile-only:text-white mb-2 line-clamp-3">Sample Product Title</h3>
                                        <div class="flex items-center mb-2">
                                            <div class="flex text-yellow-400">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i < 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-600 mobile-only:text-gray-400 ml-1">4.5</span>
                                        </div>
                                        <div class="text-xs text-gray-600 mobile-only:text-gray-400 mb-2">1.0K sold</div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg font-bold text-red-500">RM 10.00</span>
                                                <span class="text-sm text-gray-500 line-through">RM 15.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforelse

                        <!-- Arrow Button -->
                        <div class="flex-shrink-0 flex items-center">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Top deals for you Section -->
                <section>
                    <h2 class="text-3xl font-bold text-black mobile-only:text-white mb-6">Top deals for you</h2>
                    <div class="flex space-x-4 overflow-x-auto pb-4">
                        @forelse($topDealsProducts as $item)
                            @php
                                $product = $item->product ?? $item;
                                $displayTitle = method_exists($item, 'display_title') ? $item->display_title : $product->name;
                                $displayPrice = method_exists($item, 'display_price') ? $item->display_price : $product->price;
                                $displayOriginalPrice = method_exists($item, 'display_original_price') ? $item->display_original_price : $product->original_price;
                                $displayRating = method_exists($item, 'display_rating') ? $item->display_rating : $product->rating;
                                $displaySalesCount = method_exists($item, 'display_sales_count') ? $item->display_sales_count : $product->sales_count;
                            @endphp
                            <div class="flex-shrink-0 w-64 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                <a href="{{ route('products.show', $product) }}">
                                    <!-- Product Image -->
                                    <div class="aspect-w-1 aspect-h-1 bg-gray-100">
                                        @if($product->images && count($product->images) > 0)
                                            <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <div class="text-center">
                                                    <div class="w-16 h-16 bg-gray-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-600">{{ Str::limit($product->name, 15) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="p-3">
                                        <h3 class="text-sm font-medium text-gray-900 mobile-only:text-white mb-2 line-clamp-3">{{ $displayTitle }}</h3>
                                        
                                        <!-- Rating -->
                                        <div class="flex items-center mb-2">
                                            <div class="flex text-yellow-400">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i < floor($displayRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-600 mobile-only:text-gray-400 ml-1">{{ number_format($displayRating, 1) }}</span>
                                        </div>
                                        
                                        <!-- Sold Count -->
                                        <div class="text-xs text-gray-600 mobile-only:text-gray-400 mb-2">
                                            {{ number_format($displaySalesCount / 1000, 1) }}K sold
                                        </div>
                                        
                                        <!-- Price -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                       <span class="text-lg font-bold text-red-500">RM {{ number_format($displayPrice, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <!-- Fallback static products -->
                            <div class="flex-shrink-0 w-64 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                <a href="#">
                                    <div class="aspect-w-1 aspect-h-1 bg-gray-100">
                                        <div class="w-full h-48 bg-gradient-to-br from-pink-100 to-pink-200 flex items-center justify-center">
                                            <div class="text-center">
                                                <div class="w-16 h-16 bg-pink-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                    </svg>
                                                </div>
                                                <span class="text-xs text-gray-600">Sample Product</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h3 class="text-sm font-medium text-gray-900 mobile-only:text-white mb-2 line-clamp-3">Sample Product Title</h3>
                                        <div class="flex items-center mb-2">
                                            <div class="flex text-yellow-400">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i < 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-600 mobile-only:text-gray-400 ml-1">4.5</span>
                                        </div>
                                        <div class="text-xs text-gray-600 mobile-only:text-gray-400 mb-2">1.0K sold</div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg font-bold text-red-500">RM 10.00</span>
                                                <span class="text-sm text-gray-500 line-through">RM 15.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforelse

                        <!-- Arrow Button -->
                        <div class="flex-shrink-0 flex items-center">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </main>
</div>
@endsection
