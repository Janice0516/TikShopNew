@extends('layouts.merchant')

@section('title', __('merchant.dashboard.title'))

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('merchant.dashboard.title') }}</h1>
            <p class="text-gray-600">{{ __('merchant.dashboard.welcome') }}, {{ session('merchant_info.name') ?? __('merchant.common.merchant') }}！</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-sync-alt mr-2"></i>
                {{ __('merchant.common.refresh') }}
            </button>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">{{ __('merchant.error.loading_failed') }}</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $error }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 今日收益 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('merchant.dashboard.today_revenue') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['todayRevenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- 今日订单 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-receipt text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('merchant.dashboard.today_orders') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['todayOrders'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <!-- 商品总数 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-box text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('merchant.dashboard.total_products') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['totalProducts'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <!-- 待发货 -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-truck text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('merchant.dashboard.pending_orders') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pendingShipment'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 快捷操作 -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('merchant.dashboard.quick_actions') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/merchant/select-products" 
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-indigo-100 rounded-lg mr-4">
                    <i class="fas fa-plus text-indigo-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">{{ __('merchant.dashboard.add_product') }}</h4>
                    <p class="text-sm text-gray-500">{{ __('merchant.dashboard.select_from_platform') }}</p>
                </div>
            </a>
            
            <a href="/merchant/orders?status=pending" 
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-yellow-100 rounded-lg mr-4">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">{{ __('merchant.dashboard.process_order') }}</h4>
                    <p class="text-sm text-gray-500">{{ __('merchant.dashboard.view_pending_orders') }}</p>
                </div>
            </a>
            
            <a href="/merchant/finance" 
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">{{ __('merchant.dashboard.view_finance') }}</h4>
                    <p class="text-sm text-gray-500">{{ __('merchant.dashboard.view_revenue_stats') }}</p>
                </div>
            </a>
        </div>
    </div>

    <!-- 财务统计 -->
    @if(isset($financeStats))
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">财务概览 (近30天)</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">RM{{ number_format($financeStats['total_income'] ?? 0, 2) }}</div>
                <div class="text-sm text-gray-500">总收入</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">RM{{ number_format($financeStats['total_expense'] ?? 0, 2) }}</div>
                <div class="text-sm text-gray-500">总支出</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">RM{{ number_format($financeStats['current_balance'] ?? 0, 2) }}</div>
                <div class="text-sm text-gray-500">当前余额</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $financeStats['transaction_count'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">交易笔数</div>
            </div>
        </div>
    </div>
    @endif

    <!-- 最近订单 -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">最近订单</h3>
        </div>
        <div class="p-6">
            @if(count($recentOrders) > 0)
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-gray-100 rounded-lg mr-4">
                                    <i class="fas fa-receipt text-gray-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">订单 #{{ $order['orderNumber'] ?? ($order['id'] ?? 'N/A') }}</h4>
                                    <p class="text-sm text-gray-500">{{ $order['customerName'] ?? '未知客户' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">RM{{ number_format($order['totalAmount'] ?? 0, 2) }}</p>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if(($order['statusColor'] ?? '') === 'yellow') bg-yellow-100 text-yellow-800
                                    @elseif(($order['statusColor'] ?? '') === 'blue') bg-blue-100 text-blue-800
                                    @elseif(($order['statusColor'] ?? '') === 'green') bg-green-100 text-green-800
                                    @elseif(($order['statusColor'] ?? '') === 'red') bg-red-100 text-red-800
                                    @elseif(($order['statusColor'] ?? '') === 'purple') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $order['statusText'] ?? ($order['status'] ?? '未知状态') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="/merchant/orders" class="text-indigo-600 hover:text-indigo-500 font-medium">
                        查看所有订单 <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">暂无订单</h3>
                    <p class="text-gray-500">您还没有收到任何订单</p>
                </div>
            @endif
    <!-- 数据可视化图表 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 收入趋势图表 -->
        @if(isset($stats))
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">收入趋势 (近7天)</h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        @endif

        <!-- 订单状态分布 -->
        @if(isset($stats))
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">订单状态分布</h3>
            <div class="h-64">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
        @endif
    </div>

    <!-- 商品销售排行 -->
    @if(isset($topProducts) && count($topProducts) > 0)
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">热销商品排行</h3>
        <div class="h-64">
            <canvas id="productSalesChart"></canvas>
        </div>
    </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 收入趋势图表
    @if(isset($stats))
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        // 生成最近7天的收入数据
        const revenueData = [];
        const revenueLabels = [];
        for (let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            revenueLabels.push(date.toLocaleDateString('zh-CN', { month: 'short', day: 'numeric' }));
            revenueData.push(Math.floor(Math.random() * 2000) + 500); // 模拟数据
        }
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: '收入 (RM)',
                    data: revenueData,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'RM' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // 订单状态分布图表
    @if(isset($stats))
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx) {
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['已完成', '处理中', '待处理', '已取消'],
                datasets: [{
                    data: [{{ $stats['delivered'] ?? 0 }}, {{ $stats['confirmed'] ?? 0 }}, {{ $stats['pending'] ?? 0 }}, {{ $stats['cancelled'] ?? 0 }}],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    @endif

    // 商品销售排行图表
    @if(isset($topProducts) && count($topProducts) > 0)
    const productSalesCtx = document.getElementById('productSalesChart');
    if (productSalesCtx) {
        const productNames = @json(array_column($topProducts, 'name'));
        const productSales = @json(array_column($topProducts, 'sales'));
        
        new Chart(productSalesCtx, {
            type: 'bar',
            data: {
                labels: productNames,
                datasets: [{
                    label: '销量',
                    data: productSales,
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endsection