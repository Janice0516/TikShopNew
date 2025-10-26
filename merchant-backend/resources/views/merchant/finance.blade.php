@extends('layouts.merchant')

@section('title', '财务管理')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">财务管理</h1>
            <p class="text-gray-600">查看您的财务记录和统计信息</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-sync-alt mr-2"></i>
                刷新数据
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
                    <h3 class="text-sm font-medium text-red-800">加载失败</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $error }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 财务统计卡片 -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-arrow-up text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总收入 (近30天)</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['total_income'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-arrow-down text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">总支出 (近30天)</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['total_expense'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-wallet text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">当前余额</p>
                    <p class="text-2xl font-semibold text-gray-900">RM{{ number_format($stats['current_balance'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-exchange-alt text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">交易笔数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['transaction_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 收入趋势图表 -->
    @if(isset($incomeTrend) && count($incomeTrend) > 0)
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">最近7天收入趋势</h3>
        <div class="h-64">
            <canvas id="incomeTrendChart"></canvas>
        </div>
    </div>
    @endif

    <!-- 收入类型统计 -->
    @if(isset($incomeByType) && count($incomeByType) > 0)
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">收入类型统计 (近30天)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($incomeByType as $type)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $type['type_text'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $type['count'] }} 笔交易</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-semibold text-green-600">RM{{ number_format($type['total_amount'], 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form method="GET" action="/merchant/finance" class="space-y-4">
            <!-- 搜索框 -->
            <div class="flex space-x-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="keyword"
                               value="{{ $searchKeyword }}"
                               placeholder="搜索交易ID、描述、备注"
                               class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    搜索
                </button>
                <a href="/merchant/finance" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    重置
                </a>
            </div>
            
            <!-- 筛选选项 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">交易类型</label>
                    <select name="type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">全部类型</option>
                        <option value="income" {{ $selectedType === 'income' ? 'selected' : '' }}>收入</option>
                        <option value="expense" {{ $selectedType === 'expense' ? 'selected' : '' }}>支出</option>
                        <option value="withdrawal" {{ $selectedType === 'withdrawal' ? 'selected' : '' }}>提现</option>
                        <option value="refund" {{ $selectedType === 'refund' ? 'selected' : '' }}>退款</option>
                        <option value="commission" {{ $selectedType === 'commission' ? 'selected' : '' }}>佣金</option>
                        <option value="bonus" {{ $selectedType === 'bonus' ? 'selected' : '' }}>奖金</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">开始日期</label>
                    <input type="date" 
                           name="start_date"
                           value="{{ $startDate }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">结束日期</label>
                    <input type="date" 
                           name="end_date"
                           value="{{ $endDate }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </form>
    </div>

    <!-- 财务记录列表 -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                财务记录 
                @if(count($records) > 0)
                    <span class="text-sm text-gray-500">({{ count($records) }} 条记录)</span>
                @endif
            </h3>
        </div>
        
        @if(count($records) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">交易ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">类型</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">金额</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">余额</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">描述</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">时间</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($records as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    {{ $record->transaction_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($record->type === 'income') bg-green-100 text-green-800
                                        @elseif($record->type === 'expense') bg-red-100 text-red-800
                                        @elseif($record->type === 'withdrawal') bg-yellow-100 text-yellow-800
                                        @elseif($record->type === 'refund') bg-blue-100 text-blue-800
                                        @elseif($record->type === 'commission') bg-purple-100 text-purple-800
                                        @elseif($record->type === 'bonus') bg-indigo-100 text-indigo-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $record->type_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="@if($record->amount >= 0) text-green-600 @else text-red-600 @endif font-medium">
                                        {{ $record->amount >= 0 ? '+' : '' }}RM{{ number_format($record->amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    RM{{ number_format($record->balance_after, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="{{ $record->description }}">
                                        {{ $record->description }}
                                    </div>
                                    @if($record->notes)
                                        <div class="text-xs text-gray-500 mt-1 max-w-xs truncate" title="{{ $record->notes }}">
                                            {{ $record->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($record->status === 'completed') bg-green-100 text-green-800
                                        @elseif($record->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($record->status === 'failed') bg-red-100 text-red-800
                                        @elseif($record->status === 'cancelled') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $record->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $record->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- 分页 -->
            @if(isset($pagination) && $pagination->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pagination->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">暂无财务记录</h3>
                <p class="text-gray-500 mb-4">您还没有任何财务交易记录</p>
            </div>
        @endif
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 收入趋势图表
    @if(isset($incomeTrend) && count($incomeTrend) > 0)
    const incomeTrendCtx = document.getElementById('incomeTrendChart');
    if (incomeTrendCtx) {
        const incomeData = @json(array_column($incomeTrend, 'income'));
        const incomeLabels = @json(array_map(function($item) { return date('m/d', strtotime($item['date'])); }, $incomeTrend));
        
        new Chart(incomeTrendCtx, {
            type: 'line',
            data: {
                labels: incomeLabels,
                datasets: [{
                    label: '收入 (RM)',
                    data: incomeData,
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
});
</script>
@endsection