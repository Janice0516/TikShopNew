@extends('layouts.app')

@section('title', $category->name . ' - TikTok Shop')

@section('content')
<div class="min-h-screen bg-white mobile-only:bg-black">
    <!-- Main Content Layout -->
    <main class="flex-1 pt-16 ml-64 desktop-only:ml-64">
        <div class="max-w-screen-xl mx-auto px-6 py-8">
            <!-- Breadcrumb Navigation -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-600 mobile-only:text-gray-400">
                    @foreach($breadcrumbs as $index => $breadcrumb)
                        @if($index > 0)
                            <li><i class="fas fa-chevron-right text-xs mx-2"></i></li>
                        @endif
                        <li>
                            @if($breadcrumb['url'])
                                <a href="{{ $breadcrumb['url'] }}" class="hover:text-gray-900 mobile-only:hover:text-white">{{ $breadcrumb['name'] }}</a>
                            @else
                                <span class="text-gray-900 mobile-only:text-white font-medium">{{ $breadcrumb['name'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            <!-- Category Title -->
            <h1 class="text-4xl font-bold text-gray-900 mobile-only:text-white mb-8">{{ $category->name }}</h1>

            <!-- Subcategories Section -->
            @if($subcategories->count() > 0)
                <section class="mb-12">
                    <div class="flex space-x-6 overflow-x-auto pb-4">
                        @foreach($subcategories as $subcategory)
                            <div class="flex-shrink-0 text-center">
                                <a href="{{ route('categories.show', $subcategory) }}" class="block">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-3 hover:bg-gray-200 transition-colors">
                                        <div class="w-20 h-20 bg-gradient-to-br from-pink-200 to-pink-300 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-700 mobile-only:text-gray-300 font-medium text-center block max-w-24 leading-tight">{{ $subcategory->name }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Products Grid -->
            <section>
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
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
                                        <h3 class="text-sm font-medium text-gray-900 mobile-only:text-white mb-2 line-clamp-3">{{ $product->name }}</h3>
                                        
                                        <!-- Rating -->
                                        <div class="flex items-center mb-2">
                                            <div class="flex text-yellow-400">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i < floor($product->rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-600 mobile-only:text-gray-400 ml-1">{{ number_format($product->rating, 1) }}</span>
                                        </div>
                                        
                                        <!-- Sold Count -->
                                        <div class="text-xs text-gray-600 mobile-only:text-gray-400 mb-2">
                                            {{ number_format($product->sales_count / 1000, 1) }}K sold
                                        </div>
                                        
                                        <!-- Price -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg font-bold text-red-500">RM {{ number_format($product->display_price, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- No Products -->
                    <div class="text-center py-20">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mobile-only:text-white mb-2">No Products in This Category</h3>
                        <p class="text-gray-600 mobile-only:text-gray-400 mb-6">Products will be available soon</p>
                        <a href="{{ route('products.index') }}" class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition-colors">
                            Browse All Products
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </main>
</div>
@endsection
