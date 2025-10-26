@extends('layouts.app')

@section('title', '登录 - TikTok Shop')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 mobile-only:bg-black py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-16 w-16 bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-shopping-bag text-white text-2xl"></i>
            </div>
            <h2 class="mt-8 text-center text-3xl font-bold text-gray-900 mobile-only:text-white">
                {{ __('Sign in to your account') }}
            </h2>
            <p class="mt-3 text-center text-sm text-gray-600 mobile-only:text-gray-400">
                {{ __('Or') }}
                <a href="{{ route('register') }}" class="font-medium text-pink-600 hover:text-pink-500">
                    {{ __('create a new account') }}
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ app('device_type') === 'mobile' ? route('mobile.login') : route('desktop.login') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mobile-only:text-gray-300 mb-2">
                        {{ __('Phone Number') }}
                    </label>
                    <input id="phone" name="phone" type="text" required 
                           class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 sm:text-sm @error('phone') border-red-500 @enderror" 
                           placeholder="{{ __('Phone Number') }}"
                           value="{{ old('phone') }}">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mobile-only:text-gray-300 mb-2">
                        {{ __('Password') }}
                    </label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 sm:text-sm @error('password') border-red-500 @enderror" 
                           placeholder="{{ __('Password') }}">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember" type="checkbox" 
                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900 mobile-only:text-gray-300">
                        {{ __('Remember me') }}
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-pink-600 hover:text-pink-500">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors duration-200">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock text-pink-200 group-hover:text-pink-100"></i>
                    </span>
                    {{ __('Sign in') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
