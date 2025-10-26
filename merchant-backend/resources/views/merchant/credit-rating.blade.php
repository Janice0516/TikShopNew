@extends('layouts.merchant')

@section('title', '信用评级')

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">信用评级</h1>
            <p class="text-gray-600">查看您的信用评级和改进建议</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-sync-alt mr-2"></i>
                刷新数据
            </button>
        </div>
    </div>

    <!-- 信用评级概览 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-indigo-600">{{ $creditRating->rating_level }}</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $creditRating->rating_text }}</h3>
                    <p class="text-sm text-gray-500">综合评分: {{ $creditRating->overall_score }}/100</p>
                </div>
            </div>
            <div class="text-right">
                <div class="flex items-center space-x-2">
                    @if($trend['trend'] === 'up')
                        <i class="fas fa-arrow-up text-green-500"></i>
                        <span class="text-green-600 font-medium">+{{ $trend['change'] }}</span>
                    @elseif($trend['trend'] === 'down')
                        <i class="fas fa-arrow-down text-red-500"></i>
                        <span class="text-red-600 font-medium">-{{ $trend['change'] }}</span>
                    @else
                        <i class="fas fa-minus text-gray-500"></i>
                        <span class="text-gray-600 font-medium">0</span>
                    @endif
                    <span class="text-sm text-gray-500">近{{ $trend['period'] }}天</span>
                </div>
            </div>
        </div>

        <!-- 各项评分 -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-star text-blue-600"></i>
                </div>
                <h4 class="font-medium text-gray-900">服务质量</h4>
                <p class="text-2xl font-bold text-blue-600">{{ $creditRating->service_score }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-gem text-green-600"></i>
                </div>
                <h4 class="font-medium text-gray-900">商品质量</h4>
                <p class="text-2xl font-bold text-green-600">{{ $creditRating->quality_score }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-shipping-fast text-purple-600"></i>
                </div>
                <h4 class="font-medium text-gray-900">配送服务</h4>
                <p class="text-2xl font-bold text-purple-600">{{ $creditRating->shipping_score }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-comments text-yellow-600"></i>
                </div>
                <h4 class="font-medium text-gray-900">沟通效率</h4>
                <p class="text-2xl font-bold text-yellow-600">{{ $creditRating->communication_score }}</p>
            </div>
        </div>
    </div>

    <!-- 徽章和认证 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">徽章和认证</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($creditRating->badge_texts as $badge)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                    <i class="fas fa-medal mr-1"></i>
                    {{ $badge }}
                </span>
            @endforeach
            
            @if($creditRating->is_verified)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i>
                    认证商家
                </span>
            @endif
            
            @if($creditRating->is_premium)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-crown mr-1"></i>
                    高级商家
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- 评级历史图表 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">评级趋势 (近30天)</h3>
        <div class="h-64">
            <canvas id="ratingHistoryChart"></canvas>
        </div>
    </div>

        <!-- 评级分布 -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">平台评级分布</h3>
            <div class="space-y-3">
                @foreach($ratingDistribution as $level => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-medium">{{ $level }}</span>
                            <span class="text-sm text-gray-700">{{ $level }}级商家</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($count / max($ratingDistribution)) * 100 }}%"></div>
                            </div>
                            <span class="text-sm text-gray-500 w-8 text-right">{{ $count }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 详细统计 -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">详细统计</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $creditRating->total_reviews }}</div>
                <div class="text-sm text-gray-500">总评价数</div>
                <div class="text-xs text-gray-400 mt-1">
                    好评: {{ $creditRating->positive_reviews }} | 
                    中评: {{ $creditRating->neutral_reviews }} | 
                    差评: {{ $creditRating->negative_reviews }}
                </div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $creditRating->total_orders }}</div>
                <div class="text-sm text-gray-500">总订单数</div>
                <div class="text-xs text-gray-400 mt-1">
                    完成: {{ $creditRating->completed_orders }} | 
                    取消: {{ $creditRating->cancelled_orders }} | 
                    退款: {{ $creditRating->refund_orders }}
                </div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $creditRating->avg_response_time }}分钟</div>
                <div class="text-sm text-gray-500">平均响应时间</div>
                <div class="text-xs text-gray-400 mt-1">
                    发货: {{ $creditRating->avg_shipping_time }}小时 | 
                    配送: {{ $creditRating->avg_delivery_time }}天
                </div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $creditRating->refund_rate }}%</div>
                <div class="text-sm text-gray-500">退款率</div>
                <div class="text-xs text-gray-400 mt-1">
                    总收入: RM{{ number_format($creditRating->total_revenue, 2) }}
                </div>
            </div>
        </div>
    </div>

    <!-- 改进建议 -->
    @if(count($suggestions) > 0)
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">改进建议</h3>
        <div class="space-y-4">
            @foreach($suggestions as $suggestion)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($suggestion['priority'] === 'high') bg-red-100 text-red-800
                                    @elseif($suggestion['priority'] === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $suggestion['category'] }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">{{ $suggestion['title'] }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $suggestion['description'] }}</p>
                        </div>
                        <div class="ml-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($suggestion['priority'] === 'high') bg-red-100 text-red-800
                                @elseif($suggestion['priority'] === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($suggestion['priority'] === 'high') 高优先级
                                @elseif($suggestion['priority'] === 'medium') 中优先级
                                @else 低优先级 @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 评级说明 -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-2">评级说明</h3>
        <div class="text-sm text-blue-800 space-y-2">
            <p><strong>评级标准：</strong></p>
            <ul class="list-disc list-inside space-y-1 ml-4">
                <li><strong>A+ (95-100分)：</strong>优秀商家，各项指标表现卓越</li>
                <li><strong>A (90-94分)：</strong>优秀商家，整体表现优秀</li>
                <li><strong>B+ (85-89分)：</strong>良好商家，表现良好</li>
                <li><strong>B (80-84分)：</strong>良好商家，基本达标</li>
                <li><strong>C+ (75-79分)：</strong>普通商家，有待改善</li>
                <li><strong>C (70-74分)：</strong>普通商家，需要改进</li>
                <li><strong>D (0-69分)：</strong>较差商家，急需改善</li>
            </ul>
            <p class="mt-3"><strong>评级更新：</strong>评级会根据您的实际表现实时更新，建议定期查看并按照改进建议优化服务。</p>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 评级历史图表
    const ratingHistoryCtx = document.getElementById('ratingHistoryChart');
    if (ratingHistoryCtx) {
        const ratingData = @json(array_column($ratingHistory, 'score'));
        const ratingLabels = @json(array_map(function($item) { return date('m/d', strtotime($item['date'])); }, $ratingHistory));
        
        new Chart(ratingHistoryCtx, {
            type: 'line',
            data: {
                labels: ratingLabels,
                datasets: [{
                    label: '信用评分',
                    data: ratingData,
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
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
                        min: 0,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '分';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection