@extends('admin.layouts.app')

@section('title', '信用评级管理')

@section('content')
<div class="p-6">
    <!-- 页面标题 -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">信用评级管理</h1>
        <div class="flex space-x-3">
            <button onclick="exportData()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-download mr-2"></i>导出数据
            </button>
            <button onclick="showStatistics()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-chart-bar mr-2"></i>统计信息
            </button>
        </div>
    </div>

    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">总评级数</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_ratings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trophy text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">优秀评级</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['excellent_ratings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-thumbs-up text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">良好评级</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['good_ratings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star-half-alt text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">平均评分</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['avg_overall_score'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 筛选和搜索 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.credit-ratings.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- 搜索 -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">搜索</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="搜索商家名称、邮箱..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- 评级等级筛选 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">评级等级</label>
                    <select name="rating_level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">全部等级</option>
                        <option value="A+" {{ request('rating_level') === 'A+' ? 'selected' : '' }}>A+ (优秀+)</option>
                        <option value="A" {{ request('rating_level') === 'A' ? 'selected' : '' }}>A (优秀)</option>
                        <option value="B+" {{ request('rating_level') === 'B+' ? 'selected' : '' }}>B+ (良好+)</option>
                        <option value="B" {{ request('rating_level') === 'B' ? 'selected' : '' }}>B (良好)</option>
                        <option value="C+" {{ request('rating_level') === 'C+' ? 'selected' : '' }}>C+ (一般+)</option>
                        <option value="C" {{ request('rating_level') === 'C' ? 'selected' : '' }}>C (一般)</option>
                        <option value="D" {{ request('rating_level') === 'D' ? 'selected' : '' }}>D (较差)</option>
                    </select>
                </div>

                <!-- 商家筛选 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">商家</label>
                    <select name="merchant_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">全部商家</option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}" {{ request('merchant_id') == $merchant->id ? 'selected' : '' }}>
                                {{ $merchant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 评分范围 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">评分范围</label>
                    <div class="flex space-x-2">
                        <input type="number" name="min_score" value="{{ request('min_score') }}" 
                               placeholder="最低分" step="0.1" min="0" max="5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="number" name="max_score" value="{{ request('max_score') }}" 
                               placeholder="最高分" step="0.1" min="0" max="5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- 操作按钮 -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>搜索
                    </button>
                    <a href="{{ route('admin.credit-ratings.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-undo mr-2"></i>重置
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 批量操作 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form id="bulkActionForm" method="POST" action="{{ route('admin.credit-ratings.bulk-recalculate') }}">
                @csrf
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="selectAll" class="ml-2 text-sm font-medium text-gray-700">全选</label>
                    </div>
                    
                    <button type="button" onclick="executeBulkAction()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-calculator mr-2"></i>批量重新计算评分
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 信用评级列表 -->
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllTable" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商家</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">总体评分</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">服务评分</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">质量评分</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">配送评分</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">沟通评分</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评级等级</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评价数</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">最后更新</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($creditRatings as $creditRating)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="credit_rating_ids[]" value="{{ $creditRating->id }}" class="credit-rating-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $creditRating->merchant->name }}</div>
                                <div class="text-sm text-gray-500">{{ $creditRating->merchant->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($creditRating->overall_score, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($creditRating->service_score, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($creditRating->quality_score, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($creditRating->delivery_score, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($creditRating->communication_score, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if(in_array($creditRating->rating_level, ['A+', 'A'])) bg-green-100 text-green-800
                                    @elseif(in_array($creditRating->rating_level, ['B+', 'B'])) bg-blue-100 text-blue-800
                                    @elseif(in_array($creditRating->rating_level, ['C+', 'C'])) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $creditRating->rating_level }} ({{ $creditRating->rating_level_label }})
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $creditRating->total_reviews }}</div>
                                <div class="text-xs text-gray-500">
                                    好评: {{ $creditRating->positive_reviews }} | 
                                    差评: {{ $creditRating->negative_reviews }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $creditRating->last_updated ? $creditRating->last_updated->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.credit-ratings.show', $creditRating) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="recalculateRating({{ $creditRating->id }})" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-calculator"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-4 text-center text-gray-500">
                                暂无信用评级数据
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 分页 -->
        @if($creditRatings->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $creditRatings->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// 全选功能
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.credit-rating-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('selectAllTable').checked = this.checked;
});

document.getElementById('selectAllTable').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.credit-rating-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('selectAll').checked = this.checked;
});

// 批量操作
function executeBulkAction() {
    const selectedCheckboxes = document.querySelectorAll('.credit-rating-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('请选择要操作的信用评级');
        return;
    }
    
    if (confirm(`确定要重新计算选中的 ${selectedCheckboxes.length} 个信用评级吗？`)) {
        document.getElementById('bulkActionForm').submit();
    }
}

// 重新计算评分
function recalculateRating(creditRatingId) {
    if (confirm('确定要重新计算该商家的信用评级吗？')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/credit-ratings/${creditRatingId}/recalculate`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

// 导出数据
function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.open(`{{ route('admin.credit-ratings.export') }}?${params.toString()}`, '_blank');
}

// 显示统计信息
function showStatistics() {
    // 这里可以显示更详细的统计信息
    alert('统计功能开发中...');
}
</script>
@endsection
