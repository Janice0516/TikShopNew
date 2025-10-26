@extends('layouts.desktop.app')

@section('title', 'TikTok Shop - 发现好物')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Main Content Layout -->
        <!-- Left Sidebar -->
        <aside class="w-64 bg-gray-50 min-h-screen pt-16 sidebar-fixed">
            <div class="p-6">
                <!-- Category Button -->
                <div class="mb-8">
                    <a href="{{ route('desktop.categories') }}" class="flex items-center text-gray-700 hover:text-black mb-4">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Category
                    </a>
                    <button class="flex items-center text-gray-700 hover:text-black">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        客服
                    </button>
                </div>

                <!-- Login Button -->
                <div class="mb-8">
                    <a href="{{ route('desktop.login') }}" class="block w-full bg-red-500 text-white text-center py-3 rounded-lg font-medium hover:bg-red-600 transition-colors">
                        Log in
                    </a>
                </div>

            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 pt-16 ml-64">
            <div class="max-w-screen-xl mx-auto px-6 py-8">

                <!-- Categories Section -->
                <div class="mb-12">
                    <div class="scroll-smooth snap-mandatory snap-x scroll-p-1 px-1 py-2 flex overflow-x-auto gap-6 w-full gallery-no-scrollbar">
                        @foreach($categories->take(28) as $category)
                        <div class="snap-start relative flex-[0_0_calc((100%-24px*3)/4)] w-[calc((100%-24px*3)/4)] flex-grow-0 h-auto">
                            <div class="group flex flex-col items-center w-full h-full cursor-pointer" title="{{ $category->name }}">
                                <div class="flex-1 w-full group relative">
                                    <div class="px-6 mb-8 flex items-center justify-center overflow-hidden flex-grow">
                                        <div class="object-contain rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition-colors" style="width: 117.59px !important; height: 117.59px !important;">
                                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            </div>
                                    <div class="pointer-events-none absolute top-0 bottom-8 left-6 right-6 rounded-full inset-0 bg-black opacity-[.03]"></div>
                                    <div class="pointer-events-none absolute top-0 bottom-8 left-6 right-6 rounded-full inset-0 opacity-0 bg-white group-hover:opacity-20 transition duration-300"></div>
                                </div>
                                <div class="h-10 m-0 text-center overflow-hidden text-ellipsis line-clamp-2 w-full break-words" style="width: 129.59px !important; height: 42px !important;">
                                    <a class="decoration-none no-underline font-semibold text-gray-900" href="{{ route('desktop.categories.show', $category->slug) }}">
                                        {{ $category->name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Savings for you Section -->
                <div class="mb-12">
                    <div class="scroll-smooth snap-mandatory snap-x scroll-p-1 px-1 py-2 flex overflow-x-auto gap-4 w-full gallery-no-scrollbar">
                        @foreach($savingsProducts->take(8) as $product)
                        <div class="snap-start xl:flex-[0_0_calc((100%-16px*4)/5)] lg:flex-[0_0_calc((100%-16px*3)/4)] md:flex-[0_0_calc((100%-16px*2)/3)] flex-[0_0_calc((100%-16px)/2)] flex-grow-0 h-auto">
                            <div class="w-full cursor-pointer">
                                <div class="flex relative group w-full aspect-square overflow-hidden rounded-xl bg-gray-100" style="width: 237.33px !important; height: 237.33px !important;">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                    <div class="pointer-events-none absolute inset-0 w-full h-full bg-black opacity-[.03]"></div>
                                    <div class="pointer-events-none absolute inset-0 w-full h-full opacity-0 bg-white group-hover:opacity-20 transition duration-300"></div>
                                                </div>
                                <div class="my-8 px-8 text-gray-900 overflow-hidden">
                                    <div class="mb-4 max-h-51">
                                        <a href="{{ route('desktop.products.show', $product) }}" class="group no-underline text-sm text-gray-900 break-all">
                                            <h3 class="text-sm group-hover:underline transition duration-200 overflow-hidden text-ellipsis line-clamp-3">{{ $product->name }}</h3>
                                        </a>
                                    </div>
                                    <div class="mb-4 flex items-center text-gray-900">
                                        <span class="text-sm font-semibold mr-2">{{ number_format($product->rating ?? 4.5, 1) }}</span>
                                        <div role="img" aria-label="Rating: {{ number_format($product->rating ?? 4.5, 1) }} out of 5 stars" class="flex gap-2 flex-shrink-0">
                                                @for($i = 0; $i < 5; $i++)
                                            <svg fill="currentColor" font-size="12px" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" class="text-yellow-400">
                                                <path d="m25.22 2.72.12.06c1 .6 1.4 1.9 1.82 2.9l4.12 9.92.1.24.26.02 10.7.86c1.1.09 2.45.06 3.33.83.66.63.97 1.5.85 2.42-.19 1.2-1.36 2.01-2.22 2.76l-8.16 6.98-.2.17.06.25 2.5 10.45c.24 1.07.7 2.35.23 3.41a2.79 2.79 0 0 1-2.04 1.56c-1.2.2-2.33-.67-3.3-1.26l-9.17-5.6-.22-.13-.22.13-9.17 5.6c-.93.57-2.01 1.4-3.17 1.28a2.78 2.78 0 0 1-2.11-1.45c-.56-1.09-.09-2.44.18-3.54L12 30.13l.06-.25-.2-.17-8.15-6.98c-.84-.72-1.95-1.5-2.2-2.63-.19-.95.12-1.88.82-2.55.88-.77 2.24-.74 3.33-.83l10.7-.86.26-.02.1-.24 4.12-9.91c.44-1.05.85-2.42 1.94-2.97a2.8 2.8 0 0 1 2.44 0Z"></path>
                                                    </svg>
                                                @endfor
                                        </div>
                                        <span class="w-1 h-8 mx-4 bg-gray-300"></span>
                                        <span class="text-sm text-ellipsis whitespace-nowrap overflow-hidden">{{ number_format($product->sales_count ?? rand(100, 9999)) }} sold</span>
                                    </div>
                                    <div class="mt-4">
                                        <span class="text-sm font-semibold">
                                            <span class="">RM</span>
                                            <span class="text-lg font-bold text-gray-900">{{ number_format($product->display_price, 0) }}</span>
                                            <span class="text-gray-900">.{{ number_format(($product->display_price - floor($product->display_price)) * 100, 0) }}</span>
                                        </span>
                                        @if($product->discount_price > 0)
                                        <span class="ml-4 text-sm line-through text-gray-500">RM{{ number_format($product->discount_price, 2) }}</span>
                                        @endif
                                        </div>
                                    </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Top deals for you Section -->
                <div class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Top deals for you</h2>
                        <a href="#" class="text-red-500 font-medium hover:text-red-600">View all</a>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        @foreach($topDealsProducts->take(2) as $product)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="aspect-video bg-gray-100 flex items-center justify-center">
                                <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xl font-bold text-red-500">RM {{ number_format($product->display_price, 2) }}</span>
                                        @if($product->discount_price > 0)
                                        <span class="text-sm text-gray-500 line-through ml-2">RM {{ number_format($product->discount_price, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                        {{ number_format($product->rating ?? 4.5, 1) }}
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-500">
                                    {{ number_format($product->sales_count ?? rand(100, 9999)) }} sold
                                </div>
                            </div>
                                                </div>
                        @endforeach
                                            </div>
                                        </div>

                <!-- Latest Products Section -->
                <div class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Latest Products</h2>
                        <a href="/desktop/products" class="text-red-500 font-medium hover:text-red-600">View all</a>
                    </div>
                    <div class="grid grid-cols-6 gap-4">
                        @foreach($latestProducts->take(12) as $product)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="aspect-square bg-gray-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                    </div>
                                    <div class="p-3">
                                <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-red-500">RM {{ number_format($product->display_price, 2) }}</span>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                        {{ number_format($product->rating ?? 4.5, 1) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </main>
</div>

<style>
.sidebar-fixed {
    position: fixed;
    left: 0;
    top: 0;
    z-index: 40;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.gallery-no-scrollbar {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.gallery-no-scrollbar::-webkit-scrollbar {
    display: none;
}

.max-h-51 {
    max-height: 51px;
}
</style>
@endsection