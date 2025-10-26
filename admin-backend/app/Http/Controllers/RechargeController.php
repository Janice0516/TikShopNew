<?php

namespace App\Http\Controllers;

use App\Models\RechargeRequest;
use App\Models\RechargeLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RechargeController extends Controller
{
    /**
     * 显示充值申请列表
     */
    public function index(Request $request)
    {
        $query = RechargeRequest::with(['user', 'processor']);

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // 按用户筛选
        if ($request->filled('user_id')) {
            $query->user($request->user_id);
        }

        // 按用户类型筛选
        if ($request->filled('user_type')) {
            $query->userType($request->user_type);
        }

        // 按支付方式筛选
        if ($request->filled('payment_method')) {
            $query->paymentMethod($request->payment_method);
        }

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // 按金额范围筛选
        if ($request->filled('min_amount') && $request->filled('max_amount')) {
            $query->amountRange($request->min_amount, $request->max_amount);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $rechargeRequests = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $users = User::whereIn('type', ['merchant', 'customer'])->get();

        // 获取统计信息
        $stats = [
            'total_requests' => RechargeRequest::count(),
            'pending_requests' => RechargeRequest::status('pending')->count(),
            'processing_requests' => RechargeRequest::status('processing')->count(),
            'completed_requests' => RechargeRequest::status('completed')->count(),
            'rejected_requests' => RechargeRequest::status('rejected')->count(),
            'total_amount' => RechargeRequest::sum('amount'),
            'completed_amount' => RechargeRequest::status('completed')->sum('amount'),
            'pending_amount' => RechargeRequest::status('pending')->sum('amount'),
        ];

        return view('admin.recharge.index', compact('rechargeRequests', 'users', 'stats'));
    }

    /**
     * 显示充值申请详情
     */
    public function show(RechargeRequest $rechargeRequest)
    {
        $rechargeRequest->load(['user', 'processor', 'logs.operator']);
        
        return view('admin.recharge.show', compact('rechargeRequest'));
    }

    /**
     * 审核充值申请
     */
    public function audit(Request $request, RechargeRequest $rechargeRequest)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string',
            'rejection_reason' => 'required_if:action,reject|string',
        ]);

        $adminId = Auth::guard('admin')->id();

        if ($request->action === 'approve') {
            if (!$rechargeRequest->canProcess()) {
                return redirect()->back()->with('error', '该充值申请无法处理');
            }

            $rechargeRequest->updateStatus('processing', $adminId, 'admin', '开始处理充值申请', [
                'admin_notes' => $request->admin_notes,
            ]);

            return redirect()->route('admin.recharge.index')
                ->with('success', '充值申请已开始处理');
        } else {
            if (!$rechargeRequest->canReject()) {
                return redirect()->back()->with('error', '该充值申请无法拒绝');
            }

            $rechargeRequest->update([
                'rejection_reason' => $request->rejection_reason,
                'admin_notes' => $request->admin_notes,
            ]);

            $rechargeRequest->updateStatus('rejected', $adminId, 'admin', '拒绝充值申请', [
                'rejection_reason' => $request->rejection_reason,
                'admin_notes' => $request->admin_notes,
            ]);

            return redirect()->route('admin.recharge.index')
                ->with('success', '充值申请已拒绝');
        }
    }

    /**
     * 完成充值
     */
    public function complete(Request $request, RechargeRequest $rechargeRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        if ($rechargeRequest->status !== 'processing') {
            return redirect()->back()->with('error', '只能完成处理中的充值申请');
        }

        $adminId = Auth::guard('admin')->id();

        $rechargeRequest->update([
            'admin_notes' => $request->admin_notes,
        ]);

        $rechargeRequest->updateStatus('completed', $adminId, 'admin', '充值申请已完成', [
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.recharge.index')
            ->with('success', '充值申请已完成');
    }

    /**
     * 取消充值
     */
    public function cancel(Request $request, RechargeRequest $rechargeRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        if (!$rechargeRequest->canCancel()) {
            return redirect()->back()->with('error', '该充值申请无法取消');
        }

        $adminId = Auth::guard('admin')->id();

        $rechargeRequest->update([
            'admin_notes' => $request->admin_notes,
        ]);

        $rechargeRequest->updateStatus('cancelled', $adminId, 'admin', '取消充值申请', [
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.recharge.index')
            ->with('success', '充值申请已取消');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,process,complete',
            'recharge_request_ids' => 'required|array',
            'recharge_request_ids.*' => 'exists:recharge_requests,id',
            'admin_notes' => 'nullable|string',
            'rejection_reason' => 'required_if:action,reject|string',
        ]);

        $rechargeRequestIds = $request->recharge_request_ids;
        $adminId = Auth::guard('admin')->id();
        $count = 0;

        foreach ($rechargeRequestIds as $rechargeRequestId) {
            $rechargeRequest = RechargeRequest::find($rechargeRequestId);

            switch ($request->action) {
                case 'approve':
                    if ($rechargeRequest->canProcess()) {
                        $rechargeRequest->updateStatus('processing', $adminId, 'admin', '批量处理充值申请', [
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $count++;
                    }
                    break;
                    
                case 'reject':
                    if ($rechargeRequest->canReject()) {
                        $rechargeRequest->update([
                            'rejection_reason' => $request->rejection_reason,
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $rechargeRequest->updateStatus('rejected', $adminId, 'admin', '批量拒绝充值申请', [
                            'rejection_reason' => $request->rejection_reason,
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $count++;
                    }
                    break;
                    
                case 'process':
                    if ($rechargeRequest->status === 'pending') {
                        $rechargeRequest->updateStatus('processing', $adminId, 'admin', '批量开始处理充值申请', [
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $count++;
                    }
                    break;
                    
                case 'complete':
                    if ($rechargeRequest->status === 'processing') {
                        $rechargeRequest->update([
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $rechargeRequest->updateStatus('completed', $adminId, 'admin', '批量完成充值申请', [
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $count++;
                    }
                    break;
            }
        }

        $message = "成功处理 {$count} 个充值申请";
        return redirect()->route('admin.recharge.index')
            ->with('success', $message);
    }

    /**
     * 获取充值统计
     */
    public function statistics(Request $request)
    {
        $query = RechargeRequest::query();

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $stats = [
            'total_requests' => $query->count(),
            'pending_requests' => $query->clone()->status('pending')->count(),
            'processing_requests' => $query->clone()->status('processing')->count(),
            'completed_requests' => $query->clone()->status('completed')->count(),
            'rejected_requests' => $query->clone()->status('rejected')->count(),
            'cancelled_requests' => $query->clone()->status('cancelled')->count(),
            'total_amount' => $query->clone()->sum('amount'),
            'completed_amount' => $query->clone()->status('completed')->sum('amount'),
            'pending_amount' => $query->clone()->status('pending')->sum('amount'),
            'avg_amount' => $query->clone()->avg('amount'),
            'max_amount' => $query->clone()->max('amount'),
            'min_amount' => $query->clone()->min('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * 导出充值数据
     */
    public function export(Request $request)
    {
        $query = RechargeRequest::with(['user', 'processor']);

        // 应用筛选条件
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('user_id')) {
            $query->user($request->user_id);
        }
        if ($request->filled('user_type')) {
            $query->userType($request->user_type);
        }
        if ($request->filled('payment_method')) {
            $query->paymentMethod($request->payment_method);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        $rechargeRequests = $query->orderBy('created_at', 'desc')->get();

        // 这里可以集成Laravel Excel来导出数据
        // 暂时返回JSON格式
        return response()->json([
            'data' => $rechargeRequests,
            'count' => $rechargeRequests->count(),
        ]);
    }
}
