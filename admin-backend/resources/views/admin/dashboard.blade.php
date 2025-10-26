@extends('admin.layouts.app')

@section('title', '仪表盘')
@section('page-title', '仪表盘')

@section('content')
<div class="space-y-6">
    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 总商家数 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-store text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总商家数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_merchants']) }}</p>
                </div>
            </div>
        </div>
        
        <!-- 总订单数 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总订单数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
                </div>
            </div>
        </div>
        
        <!-- 总商品数 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-box text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总商品数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                </div>
            </div>
        </div>
        
        <!-- 总用户数 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-users text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总用户数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 今日数据 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 今日订单 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <i class="fas fa-calendar-day text-indigo-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">今日订单</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['today_orders']) }}</p>
                </div>
            </div>
        </div>
        
        <!-- 今日收入 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-emerald-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">今日收入</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['today_revenue']) }}</p>
                </div>
            </div>
        </div>
        
        <!-- 待处理订单 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <i class="fas fa-clock text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">待处理订单</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['pending_orders']) }}</p>
                </div>
            </div>
        </div>
        
        <!-- 活跃商家 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-pink-100 rounded-lg">
                    <i class="fas fa-chart-line text-pink-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">活跃商家</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['active_merchants']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 最近订单 -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">最近订单</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-900">{{ $order['order_number'] }}</h4>
                                <span class="text-sm text-gray-500">{{ $order['created_at']->diffForHumans() }}</span>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-gray-600">
                                    商家: {{ $order['merchant_name'] }} | 客户: {{ $order['customer_name'] }}
                                </p>
                            </div>
                        </div>
                        <div class="ml-4 text-right">
                            <p class="text-sm font-medium text-gray-900">RM{{ number_format($order['total_amount'], 2) }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order['status'] === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order['status'] === 'shipped') bg-blue-100 text-blue-800
                                @elseif($order['status'] === 'delivered') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($order['status'] === 'pending') 待处理
                                @elseif($order['status'] === 'shipped') 已发货
                                @elseif($order['status'] === 'delivered') 已送达
                                @else {{ $order['status'] }} @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="/admin/orders" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        查看所有订单 →
                    </a>
                </div>
            </div>
        </div>

        <!-- 热门商家 -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">热门商家</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($topMerchants as $merchant)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                {{ substr($merchant['name'], 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $merchant['name'] }}</h4>
                                <div class="flex items-center mt-1">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= $merchant['rating'] ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-xs text-gray-500">{{ $merchant['rating'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ number_format($merchant['orders_count']) }} 订单</p>
                            <p class="text-xs text-gray-500">RM{{ number_format($merchant['revenue']) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="/admin/merchants" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        查看所有商家 →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 快捷操作 -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">快捷操作</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/admin/merchants" 
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-blue-100 rounded-lg mr-4">
                    <i class="fas fa-store text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">商家管理</h4>
                    <p class="text-sm text-gray-500">管理商家账户和权限</p>
                </div>
            </a>
            
            <a href="/admin/orders" 
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">订单管理</h4>
                    <p class="text-sm text-gray-500">处理订单和物流</p>
                </div>
            </a>
            
            <a href="/admin/products" 
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-purple-100 rounded-lg mr-4">
                    <i class="fas fa-box text-purple-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">商品管理</h4>
                    <p class="text-sm text-gray-500">管理商品和分类</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
