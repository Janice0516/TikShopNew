@extends('layouts.app')

@section('title', '购物车 - TikTok Shop')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Shopping Cart') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Review your items before checkout') }}</p>
        </div>

        @if($cartItems->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('Cart Items') }}</h2>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            @foreach($cartItems as $item)
                                <div class="p-6">
                                    <div class="flex items-center space-x-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            @if($item->product->images && count($item->product->images) > 0)
                                                <img src="{{ $item->product->images[0] }}" alt="{{ $item->product->name }}" 
                                                     class="w-20 h-20 object-cover rounded-lg">
                                            @else
                                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('products.show', $item->product) }}" class="hover:text-pink-600">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500">{{ $item->product->category->name ?? '' }}</p>
                                            
                                            <!-- Price -->
                                            <div class="mt-2">
                                                <span class="text-lg font-bold text-gray-900">RM {{ number_format($item->product->display_price, 2) }}</span>
                                                @if($item->product->discount_price && $item->price != $item->product->price)
                                                    <span class="text-sm text-gray-500 line-through ml-2">RM {{ number_format($item->product->price, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="flex items-center space-x-2">
                                            <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" onclick="decreaseQuantity({{ $item->id }})" 
                                                        class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50">
                                                    <i class="fas fa-minus text-gray-600"></i>
                                                </button>
                                                <input type="number" id="quantity-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="99"
                                                       class="w-16 px-2 py-1 text-center border border-gray-300 rounded-md mx-2"
                                                       onchange="updateQuantity({{ $item->id }}, this.value)">
                                                <button type="button" onclick="increaseQuantity({{ $item->id }})" 
                                                        class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50">
                                                    <i class="fas fa-plus text-gray-600"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Remove Button -->
                                        <div class="flex-shrink-0">
                                            <form method="POST" action="{{ route('cart.remove', $item) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 p-2"
                                                        onclick="return confirm('确定要移除这个商品吗？')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Order Summary') }}</h2>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Subtotal') }}</span>
                                <span class="font-semibold">RM {{ number_format($total, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Shipping') }}</span>
                                <span class="font-semibold text-green-600">{{ __('Free') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Tax') }}</span>
                                <span class="font-semibold">RM 0.00</span>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="flex justify-between text-lg font-bold">
                                <span>{{ __('Total') }}</span>
                                <span>RM {{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <a href="{{ route('orders.create') }}" 
                               class="w-full bg-pink-500 text-white py-3 px-4 rounded-md font-semibold text-center hover:bg-pink-600 transition-colors block">
                                {{ __('Proceed to Checkout') }}
                            </a>
                            
                            <a href="{{ route('products.index') }}" 
                               class="w-full bg-gray-200 text-gray-800 py-3 px-4 rounded-md font-semibold text-center hover:bg-gray-300 transition-colors block">
                                {{ __('Continue Shopping') }}
                            </a>
                        </div>

                        <!-- Security Features -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                <span>{{ __('Secure checkout') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mt-2">
                                <i class="fas fa-truck text-blue-500 mr-2"></i>
                                <span>{{ __('Free shipping on orders over RM50') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-shopping-cart text-gray-400 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('Your cart is empty') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('Add some products to get started') }}</p>
                <a href="{{ route('products.index') }}" 
                   class="bg-pink-500 text-white px-6 py-3 rounded-md font-semibold hover:bg-pink-600 transition-colors">
                    {{ __('Start Shopping') }}
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function increaseQuantity(itemId) {
    const input = document.getElementById('quantity-' + itemId);
    const currentValue = parseInt(input.value);
    if (currentValue < 99) {
        input.value = currentValue + 1;
        updateQuantity(itemId, input.value);
    }
}

function decreaseQuantity(itemId) {
    const input = document.getElementById('quantity-' + itemId);
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
        updateQuantity(itemId, input.value);
    }
}

function updateQuantity(itemId, quantity) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/cart/${itemId}`;
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const quantityInput = document.createElement('input');
    quantityInput.type = 'hidden';
    quantityInput.name = 'quantity';
    quantityInput.value = quantity;
    
    form.appendChild(methodInput);
    form.appendChild(csrfInput);
    form.appendChild(quantityInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
