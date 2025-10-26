@extends('layouts.app')

@section('title', '商品列表 - TikTok Shop')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ __('All Products') }}</h1>
            
            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Search') }}</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="{{ __('Search products...') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Category') }}</label>
                            <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Min Price') }}</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                   placeholder="0" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Max Price') }}</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                   placeholder="1000" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="flex flex-col sm:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Sort By') }}</label>
                            <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>{{ __('Price') }}</option>
                                <option value="sales" {{ request('sort_by') == 'sales' ? 'selected' : '' }}>{{ __('Best Selling') }}</option>
                                <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>{{ __('Highest Rated') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <select name="sort_order" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>{{ __('Descending') }}</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>{{ __('Ascending') }}</option>
                            </select>
                        </div>

                        <button type="submit" class="bg-pink-500 text-white px-6 py-2 rounded-md hover:bg-pink-600 transition-colors">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Count -->
        <div class="mb-6">
            <p class="text-gray-600">
                {{ __('Showing :count products', ['count' => $products->total()]) }}
            </p>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden">
                        <a href="{{ route('products.show', $product) }}">
                            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                                
                                <!-- Category -->
                                <p class="text-sm text-gray-500 mb-2">{{ $product->category->name ?? '' }}</p>
                                
                                <!-- Rating -->
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        @for($i = 0; $i < 5; $i++)
                                            <i class="fas fa-star {{ $i < floor($product->rating) ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 ml-2">({{ $product->rating }})</span>
                                </div>
                                
                                <!-- Price -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        @if($product->discount_price)
                                            <span class="text-lg font-bold text-red-600">RM {{ number_format($product->discount_price, 2) }}</span>
                                            <span class="text-sm text-gray-500 line-through">RM {{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">RM {{ number_format($product->display_price, 2) }}</span>
                                        @endif
                                    </div>
                                    <button onclick="addToCart({{ $product->id }})" class="bg-pink-500 text-white px-3 py-1 rounded-full text-sm hover:bg-pink-600 transition-colors">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <!-- No Products -->
            <div class="text-center py-12">
                <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('No Products Found') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('Try adjusting your search criteria') }}</p>
                <a href="{{ route('products.index') }}" class="bg-pink-500 text-white px-6 py-3 rounded-md hover:bg-pink-600 transition-colors">
                    {{ __('Clear Filters') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
