@extends('layouts.app')

@section('title', $product->name . ' - TikTok Shop')

@section('content')
<div class="min-h-screen bg-white mobile-only:bg-black">
    <!-- Main Content Layout -->
    <main class="flex-1 pt-16 ml-64 desktop-only:ml-64">
        <div class="max-w-screen-xl mx-auto px-6 py-8">
            <!-- Breadcrumb Navigation -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-600 mobile-only:text-gray-400">
                    <li><a href="{{ route('home') }}" class="hover:text-gray-900 mobile-only:hover:text-white">TikTok Shop</a></li>
                    @if($product->category)
                        <li><i class="fas fa-chevron-right text-xs mx-2"></i></li>
                        <li><a href="{{ route('categories.show', $product->category) }}" class="hover:text-gray-900 mobile-only:hover:text-white">{{ $product->category->name }}</a></li>
                    @endif
                    <li><i class="fas fa-chevron-right text-xs mx-2"></i></li>
                    <li class="text-gray-900 mobile-only:text-white font-medium">{{ Str::limit($product->name, 50) }}</li>
                </ol>
            </nav>

            <!-- Product Detail Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Product Images (Left Side) -->
                <div class="relative">
                    <!-- Main Product Image -->
                    <div class="aspect-w-1 aspect-h-1 bg-gray-100 rounded-lg overflow-hidden mb-4">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
                        @else
                            <div class="w-full h-96 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="w-24 h-24 bg-gray-400 rounded-full mx-auto mb-4 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ Str::limit($product->name, 20) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnail Images -->
                    @if($product->images && count($product->images) > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($product->images as $index => $image)
                                <div class="aspect-w-1 aspect-h-1 bg-gray-100 rounded cursor-pointer border-2 border-transparent hover:border-gray-300">
                                    <img src="{{ $image }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Brand Logo Overlay -->
                    @if($product->brand)
                        <div class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-3">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <span class="text-white font-bold text-lg">{{ substr($product->brand, 0, 1) }}</span>
                                </div>
                                <span class="text-xs font-medium text-gray-700">{{ $product->brand }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Product Information (Right Side) -->
                <div class="space-y-6">
                    <!-- Discount Badge -->
                    @if($product->cost_price && $product->cost_price > $product->display_price)
                        <div class="inline-block">
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                -{{ round((($product->cost_price - $product->display_price) / $product->cost_price) * 100) }}%
                            </span>
                        </div>
                    @endif

                    <!-- Price -->
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <span class="text-4xl font-bold text-gray-900 mobile-only:text-white">RM {{ number_format($product->display_price, 2) }}</span>
                            @if($product->cost_price && $product->cost_price > $product->display_price)
                                <span class="text-xl text-gray-500 line-through">RM {{ number_format($product->cost_price, 2) }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mobile-only:text-gray-400">From RM4.90 shipping on this order</p>
                    </div>

                    <!-- Product Title -->
                    <h1 class="text-2xl font-bold text-gray-900 mobile-only:text-white leading-tight">{{ $product->name }}</h1>

                    <!-- Seller Info -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 mobile-only:text-gray-400">Sold by</span>
                        <span class="text-sm font-medium text-gray-900 mobile-only:text-white">{{ $product->merchant->name ?? 'Unknown Seller' }}</span>
                    </div>

                    <!-- Rating & Sales -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-1">
                            <div class="flex text-yellow-400">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 {{ $i < floor($product->rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600 mobile-only:text-gray-400">{{ number_format($product->rating, 1) }}</span>
                        </div>
                        <span class="text-sm text-gray-600 mobile-only:text-gray-400">({{ rand(50, 500) }} reviews)</span>
                        <span class="text-sm text-gray-600 mobile-only:text-gray-400">{{ number_format($product->sales_count / 1000, 1) }}K sold</span>
                    </div>

                    <!-- Color Selection -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700 mobile-only:text-gray-300">Warna: Fossil</label>
                        <div class="flex space-x-2">
                            @php
                                $colors = ['Black', 'Navy', 'Hazelnut', 'Fossil', 'Ivy'];
                                $colorClasses = [
                                    'Black' => 'bg-black',
                                    'Navy' => 'bg-blue-900',
                                    'Hazelnut' => 'bg-yellow-600',
                                    'Fossil' => 'bg-gray-400',
                                    'Ivy' => 'bg-green-600'
                                ];
                            @endphp
                            @foreach($colors as $color)
                                <div class="relative">
                                    <div class="w-12 h-12 {{ $colorClasses[$color] }} rounded border-2 {{ $color === 'Fossil' ? 'border-black' : 'border-gray-200' }} cursor-pointer hover:border-gray-400"></div>
                                    <span class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-xs text-gray-600 mobile-only:text-gray-400 whitespace-nowrap">{{ $color }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Size Selection -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700 mobile-only:text-gray-300">Saiz: S</label>
                        <div class="flex space-x-2">
                            @php
                                $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                            @endphp
                            @foreach($sizes as $size)
                                <button class="w-12 h-10 border-2 {{ $size === 'S' ? 'border-black bg-gray-50' : 'border-gray-200 hover:border-gray-400' }} rounded text-sm font-medium text-gray-700 mobile-only:text-gray-300 hover:bg-gray-50">
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <div class="space-y-4">
                        <button onclick="addToCart({{ $product->id }})" class="w-full bg-pink-500 text-white py-4 px-6 rounded-lg font-bold text-lg hover:bg-pink-600 transition-colors">
                            Add to Cart
                        </button>
                        
                        <!-- Seller Performance -->
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900 mobile-only:text-white">{{ $product->merchant->name ?? 'Unknown Seller' }}</span>
                                <div class="flex items-center space-x-1">
                                    <div class="flex text-yellow-400">
                                        @for($i = 0; $i < 5; $i++)
                                            <svg class="w-3 h-3 {{ $i < 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 mobile-only:text-gray-400">4.3</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mobile-only:text-gray-400">
                                <span>{{ number_format(rand(10000, 50000) / 1000, 1) }}M Sold</span>
                                <span>{{ number_format(rand(500000, 1000000) / 1000, 1) }}K+ Followers</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Description -->
            @if($product->description)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mobile-only:text-white mb-6">Product Description</h2>
                    <div class="bg-white rounded-lg p-6 border border-gray-200">
                        <p class="text-gray-700 mobile-only:text-gray-300 leading-relaxed">{{ $product->description }}</p>
                    </div>
                </div>
            @endif

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mobile-only:text-white mb-6">Related Products</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                                <a href="{{ route('products.show', $relatedProduct) }}">
                                    <div class="aspect-w-1 aspect-h-1 bg-gray-100">
                                        @if($relatedProduct->images && count($relatedProduct->images) > 0)
                                            <img src="{{ $relatedProduct->images[0] }}" alt="{{ $relatedProduct->name }}" class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <div class="text-center">
                                                    <div class="w-16 h-16 bg-gray-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-600">{{ Str::limit($relatedProduct->name, 15) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="p-3">
                                        <h3 class="text-sm font-medium text-gray-900 mobile-only:text-white mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                                        
                                        <div class="flex items-center mb-2">
                                            <div class="flex text-yellow-400">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i < floor($relatedProduct->rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-600 mobile-only:text-gray-400 ml-1">{{ number_format($relatedProduct->rating, 1) }}</span>
                                        </div>
                                        
                                        <div class="text-xs text-gray-600 mobile-only:text-gray-400 mb-2">
                                            {{ number_format($relatedProduct->sales_count / 1000, 1) }}K sold
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg font-bold text-red-500">RM {{ number_format($relatedProduct->display_price, 2) }}</span>
                                                @if($relatedProduct->cost_price && $relatedProduct->cost_price > $relatedProduct->display_price)
                                                    <span class="text-sm text-gray-500 line-through">RM {{ number_format($relatedProduct->cost_price, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>

<script>
function addToCart(productId) {
    // Add to cart functionality
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Product added to cart successfully!');
        } else {
            alert('Failed to add product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endsection