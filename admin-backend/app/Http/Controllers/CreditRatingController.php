<?php

namespace App\Http\Controllers;

use App\Models\CreditRating;
use App\Models\CreditRatingRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditRatingController extends Controller
{
    /**
     * 显示信用评级列表
     */
    public function index(Request $request)
    {
        $query = CreditRating::with(['merchant']);

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按评级等级筛选
        if ($request->filled('rating_level')) {
            $query->ratingLevel($request->rating_level);
        }

        // 按商家筛选
        if ($request->filled('merchant_id')) {
            $query->merchant($request->merchant_id);
        }

        // 按评分范围筛选
        if ($request->filled('min_score') && $request->filled('max_score')) {
            $query->scoreRange($request->min_score, $request->max_score);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'overall_score');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $creditRatings = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $merchants = User::where('type', 'merchant')->get();

        // 获取统计信息
        $stats = [
            'total_ratings' => CreditRating::count(),
            'excellent_ratings' => CreditRating::whereIn('rating_level', ['A+', 'A'])->count(),
            'good_ratings' => CreditRating::whereIn('rating_level', ['B+', 'B'])->count(),
            'average_ratings' => CreditRating::whereIn('rating_level', ['C+', 'C'])->count(),
            'poor_ratings' => CreditRating::where('rating_level', 'D')->count(),
            'avg_overall_score' => CreditRating::avg('overall_score'),
            'total_reviews' => CreditRating::sum('total_reviews'),
            'positive_reviews' => CreditRating::sum('positive_reviews'),
        ];

        return view('admin.credit-ratings.index', compact('creditRatings', 'merchants', 'stats'));
    }

    /**
     * 显示信用评级详情
     */
    public function show(CreditRating $creditRating)
    {
        $creditRating->load(['merchant', 'records.customer', 'records.order']);
        
        // 获取最近评价
        $recentRecords = $creditRating->records()
            ->with(['customer', 'order'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 获取评价统计
        $reviewStats = [
            'total_reviews' => $creditRating->records->count(),
            'verified_reviews' => $creditRating->records->where('is_verified', true)->count(),
            'positive_reviews' => $creditRating->records->where('review_type', 'positive')->count(),
            'neutral_reviews' => $creditRating->records->where('review_type', 'neutral')->count(),
            'negative_reviews' => $creditRating->records->where('review_type', 'negative')->count(),
        ];

        return view('admin.credit-ratings.show', compact('creditRating', 'recentRecords', 'reviewStats'));
    }

    /**
     * 更新信用评级
     */
    public function update(Request $request, CreditRating $creditRating)
    {
        $request->validate([
            'rating_summary' => 'nullable|string',
            'improvement_suggestions' => 'nullable|array',
        ]);

        $creditRating->update([
            'rating_summary' => $request->rating_summary,
            'improvement_suggestions' => $request->improvement_suggestions,
        ]);

        return redirect()->route('admin.credit-ratings.index')
            ->with('success', '信用评级已更新');
    }

    /**
     * 重新计算评分
     */
    public function recalculate(CreditRating $creditRating)
    {
        $creditRating->updateRating();

        return redirect()->route('admin.credit-ratings.show', $creditRating)
            ->with('success', '评分已重新计算');
    }

    /**
     * 批量重新计算评分
     */
    public function bulkRecalculate(Request $request)
    {
        $request->validate([
            'credit_rating_ids' => 'required|array',
            'credit_rating_ids.*' => 'exists:credit_ratings,id',
        ]);

        $count = 0;
        foreach ($request->credit_rating_ids as $creditRatingId) {
            $creditRating = CreditRating::find($creditRatingId);
            $creditRating->updateRating();
            $count++;
        }

        return redirect()->route('admin.credit-ratings.index')
            ->with('success', "成功重新计算 {$count} 个信用评级");
    }

    /**
     * 验证评价记录
     */
    public function verifyRecord(Request $request, CreditRatingRecord $record)
    {
        $request->validate([
            'is_verified' => 'required|boolean',
            'admin_notes' => 'nullable|string',
        ]);

        $adminId = Auth::guard('admin')->id();

        $record->update([
            'is_verified' => $request->is_verified,
            'verified_by' => $adminId,
            'verified_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        // 重新计算信用评级
        $record->creditRating->updateRating();

        return redirect()->back()
            ->with('success', '评价记录验证完成');
    }

    /**
     * 删除评价记录
     */
    public function deleteRecord(CreditRatingRecord $record)
    {
        $creditRating = $record->creditRating;
        $record->delete();

        // 重新计算信用评级
        $creditRating->updateRating();

        return redirect()->back()
            ->with('success', '评价记录已删除');
    }

    /**
     * 获取信用评级统计
     */
    public function statistics(Request $request)
    {
        $query = CreditRating::query();

        $stats = [
            'total_ratings' => $query->count(),
            'excellent_ratings' => $query->clone()->whereIn('rating_level', ['A+', 'A'])->count(),
            'good_ratings' => $query->clone()->whereIn('rating_level', ['B+', 'B'])->count(),
            'average_ratings' => $query->clone()->whereIn('rating_level', ['C+', 'C'])->count(),
            'poor_ratings' => $query->clone()->where('rating_level', 'D')->count(),
            'avg_overall_score' => $query->clone()->avg('overall_score'),
            'avg_service_score' => $query->clone()->avg('service_score'),
            'avg_quality_score' => $query->clone()->avg('quality_score'),
            'avg_delivery_score' => $query->clone()->avg('delivery_score'),
            'avg_communication_score' => $query->clone()->avg('communication_score'),
            'total_reviews' => $query->clone()->sum('total_reviews'),
            'positive_reviews' => $query->clone()->sum('positive_reviews'),
            'neutral_reviews' => $query->clone()->sum('neutral_reviews'),
            'negative_reviews' => $query->clone()->sum('negative_reviews'),
        ];

        return response()->json($stats);
    }

    /**
     * 导出信用评级数据
     */
    public function export(Request $request)
    {
        $query = CreditRating::with(['merchant']);

        // 应用筛选条件
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('rating_level')) {
            $query->ratingLevel($request->rating_level);
        }
        if ($request->filled('merchant_id')) {
            $query->merchant($request->merchant_id);
        }
        if ($request->filled('min_score') && $request->filled('max_score')) {
            $query->scoreRange($request->min_score, $request->max_score);
        }

        $creditRatings = $query->orderBy('overall_score', 'desc')->get();

        // 这里可以集成Laravel Excel来导出数据
        // 暂时返回JSON格式
        return response()->json([
            'data' => $creditRatings,
            'count' => $creditRatings->count(),
        ]);
    }
}
