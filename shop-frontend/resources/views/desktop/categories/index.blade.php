@extends('layouts.app')

@section('title', '商品分类 - TikTok Shop')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ __('Shop by Category') }}</h1>
            <p class="text-gray-600">{{ __('Find what you need in our wide range of categories') }}</p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="group">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-pink-400 to-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tag text-white text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-pink-600 mb-2">{{ $category->name }}</h3>
                        @if($category->description)
                            <p class="text-sm text-gray-500 line-clamp-2">{{ $category->description }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        @if($categories->count() == 0)
            <div class="text-center py-12">
                <i class="fas fa-folder-open text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('No Categories Available') }}</h3>
                <p class="text-gray-600">{{ __('Categories will be available soon') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
