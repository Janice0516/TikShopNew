@extends('layouts.merchant')

@section('title', '店铺设置')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">店铺设置</h1>
            <p class="text-gray-600">管理您的店铺信息和设置</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-sync-alt mr-2"></i>
                刷新数据
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">保存成功</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">保存失败</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 店铺统计卡片 -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-box text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总商品</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_products'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">在售商品</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_products'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-shopping-cart text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总订单</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总收入</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <i class="fas fa-eye text-indigo-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">店铺访问</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['shop_views'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-pink-100 rounded-lg">
                    <i class="fas fa-users text-pink-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">客户数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['customer_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 店铺设置表单 -->
    <form method="POST" action="/merchant/shop" class="space-y-6">
        @csrf
        
        <!-- 基本信息 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">基本信息</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">店铺名称 *</label>
                    <input type="text" 
                           name="shop_name"
                           value="{{ $shopSetting->shop_name }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">店铺标识</label>
                    <input type="text" 
                           value="{{ $shopSetting->shop_slug }}"
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                    <p class="text-xs text-gray-500 mt-1">店铺URL: /shop/{{ $shopSetting->shop_slug }}</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">店铺描述</label>
                    <textarea name="shop_description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ $shopSetting->shop_description }}</textarea>
                </div>
            </div>
        </div>

        <!-- 联系信息 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">联系信息</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">联系邮箱</label>
                    <input type="email" 
                           name="contact_email"
                           value="{{ $shopSetting->contact_email }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">联系电话</label>
                    <input type="text" 
                           name="contact_phone"
                           value="{{ $shopSetting->contact_phone }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">联系地址</label>
                    <input type="text" 
                           name="contact_address"
                           value="{{ $shopSetting->contact_address }}"
                           placeholder="详细地址"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">城市</label>
                    <input type="text" 
                           name="contact_city"
                           value="{{ $shopSetting->contact_city }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">州/省</label>
                    <input type="text" 
                           name="contact_state"
                           value="{{ $shopSetting->contact_state }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">国家</label>
                    <input type="text" 
                           name="contact_country"
                           value="{{ $shopSetting->contact_country }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">邮编</label>
                    <input type="text" 
                           name="contact_zip"
                           value="{{ $shopSetting->contact_zip }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- 社交媒体 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">社交媒体</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">官方网站</label>
                    <input type="url" 
                           name="website_url"
                           value="{{ $shopSetting->website_url }}"
                           placeholder="https://example.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                    <input type="url" 
                           name="facebook_url"
                           value="{{ $shopSetting->facebook_url }}"
                           placeholder="https://facebook.com/yourpage"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                    <input type="url" 
                           name="instagram_url"
                           value="{{ $shopSetting->instagram_url }}"
                           placeholder="https://instagram.com/yourpage"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter</label>
                    <input type="url" 
                           name="twitter_url"
                           value="{{ $shopSetting->twitter_url }}"
                           placeholder="https://twitter.com/yourpage"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">YouTube</label>
                    <input type="url" 
                           name="youtube_url"
                           value="{{ $shopSetting->youtube_url }}"
                           placeholder="https://youtube.com/channel/yourchannel"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- 配送设置 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">配送设置</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">免运费门槛 (RM)</label>
                    <input type="number" 
                           name="free_shipping_threshold"
                           value="{{ $shopSetting->free_shipping_threshold }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">订单金额达到此金额时免运费</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">默认运费 (RM)</label>
                    <input type="number" 
                           name="default_shipping_fee"
                           value="{{ $shopSetting->default_shipping_fee }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">未达到免运费门槛时的运费</p>
                </div>
            </div>
        </div>

        <!-- 店铺政策 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">店铺政策</h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">退货政策</label>
                    <textarea name="return_policy"
                              rows="4"
                              placeholder="请详细说明退货条件和流程..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ $shopSetting->return_policy }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">隐私政策</label>
                    <textarea name="privacy_policy"
                              rows="4"
                              placeholder="请说明如何保护客户隐私..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ $shopSetting->privacy_policy }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">服务条款</label>
                    <textarea name="terms_of_service"
                              rows="4"
                              placeholder="请说明服务条款和使用条件..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ $shopSetting->terms_of_service }}</textarea>
                </div>
            </div>
        </div>

        <!-- 店铺设置 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">店铺设置</h3>
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active"
                           value="1"
                           {{ $shopSetting->is_active ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">
                        店铺营业中
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="allow_reviews"
                           value="1"
                           {{ $shopSetting->allow_reviews ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">
                        允许客户评价
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="show_contact_info"
                           value="1"
                           {{ $shopSetting->show_contact_info ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">
                        显示联系信息
                    </label>
                </div>
            </div>
        </div>

        <!-- SEO设置 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">SEO设置</h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">页面标题</label>
                    <input type="text" 
                           name="meta_title"
                           value="{{ $shopSetting->meta_title }}"
                           placeholder="店铺页面标题"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">页面描述</label>
                    <textarea name="meta_description"
                              rows="3"
                              placeholder="店铺页面描述，用于搜索引擎..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ $shopSetting->meta_description }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">关键词</label>
                    <input type="text" 
                           name="meta_keywords"
                           value="{{ $shopSetting->meta_keywords }}"
                           placeholder="关键词1, 关键词2, 关键词3"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- 提交按钮 -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-save mr-2"></i>
                保存设置
            </button>
        </div>
    </form>
</div>
@endsection