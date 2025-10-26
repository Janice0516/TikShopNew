<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\WithdrawalLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    /**
     * 显示提现申请列表
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with(['merchant', 'processor']);

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // 按商家筛选
        if ($request->filled('merchant_id')) {
            $query->merchant($request->merchant_id);
        }

        // 按提现方式筛选
        if ($request->filled('method')) {
            $query->method($request->method);
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

        $withdrawals = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $merchants = User::where('type', 'merchant')->get();

        // 获取统计信息
        $stats = [
            'total_withdrawals' => Withdrawal::count(),
            'pending_withdrawals' => Withdrawal::status('pending')->count(),
            'processing_withdrawals' => Withdrawal::status('processing')->count(),
            'completed_withdrawals' => Withdrawal::status('completed')->count(),
            'rejected_withdrawals' => Withdrawal::status('rejected')->count(),
            'total_amount' => Withdrawal::sum('amount'),
            'total_fee' => Withdrawal::sum('fee'),
            'total_actual_amount' => Withdrawal::sum('actual_amount'),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'merchants', 'stats'));
    }

    /**
     * 显示提现申请详情
     */
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['merchant', 'processor', 'logs.operator']);
        
        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * 处理提现申请
     */
    public function process(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string',
            'rejection_reason' => 'required_if:action,reject|string',
        ]);

        $adminId = Auth::guard('admin')->id();

        if ($request->action === 'approve') {
            if (!$withdrawal->canProcess()) {
                return redirect()->back()->with('error', '该提现申请无法处理');
            }

            $withdrawal->updateStatus('processing', $adminId, 'admin', '开始处理提现申请', [
                'admin_notes' => $request->admin_notes,
            ]);

            return redirect()->route('admin.withdrawals.index')
                ->with('success', '提现申请已开始处理');
        } else {
            if (!$withdrawal->canReject()) {
                return redirect()->back()->with('error', '该提现申请无法拒绝');
            }

            $withdrawal->update([
                'rejection_reason' => $request->rejection_reason,
                'admin_notes' => $request->admin_notes,
            ]);

            $withdrawal->updateStatus('rejected', $adminId, 'admin', '拒绝提现申请', [
                'rejection_reason' => $request->rejection_reason,
                'admin_notes' => $request->admin_notes,
            ]);

            return redirect()->route('admin.withdrawals.index')
                ->with('success', '提现申请已拒绝');
        }
    }

    /**
     * 完成提现
     */
    public function complete(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        if ($withdrawal->status !== 'processing') {
            return redirect()->back()->with('error', '只能完成处理中的提现申请');
        }

        $adminId = Auth::guard('admin')->id();

        $withdrawal->update([
            'admin_notes' => $request->admin_notes,
        ]);

        $withdrawal->updateStatus('completed', $adminId, 'admin', '提现申请已完成', [
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.withdrawals.index')
            ->with('success', '提现申请已完成');
    }

    /**
     * 取消提现
     */
    public function cancel(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        if (!$withdrawal->canCancel()) {
            return redirect()->back()->with('error', '该提现申请无法取消');
        }

        $adminId = Auth::guard('admin')->id();

        $withdrawal->update([
            'admin_notes' => $request->admin_notes,
        ]);

        $withdrawal->updateStatus('cancelled', $adminId, 'admin', '取消提现申请', [
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.withdrawals.index')
            ->with('success', '提现申请已取消');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,process,complete',
            'withdrawal_ids' => 'required|array',
            'withdrawal_ids.*' => 'exists:withdrawals,id',
            'admin_notes' => 'nullable|string',
            'rejection_reason' => 'required_if:action,reject|string',
        ]);

        $withdrawalIds = $request->withdrawal_ids;
        $adminId = Auth::guard('admin')->id();
        $count = 0;

        foreach ($withdrawalIds as $withdrawalId) {
            $withdrawal = Withdrawal::find($withdrawalId);

            switch ($request->action) {
                case 'approve':
                    if ($withdrawal->canProcess()) {
                        $withdrawal->updateStatus('processing', $adminId, 'admin', '批量处理提现申请', [
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $count++;
                    }
                    break;
                    
                case 'reject':
                    if ($withdrawal->canReject()) {
                        $withdrawal->update([
                            'rejection_reason' => $request->rejection_reason,
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $withdrawal->updateStatus('rejected', $adminId, 'admin', '批量拒绝提现申请', [
                            'rejection_reason' => $request->rejection_reason,
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $count++;
                    }
                    break;
                    
                case 'process':
                    if ($withdrawal->status === 'pending') {
                        $withdrawal->updateStatus('processing', $adminId, 'admin', '批量开始处理提现申请', [
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $count++;
                    }
                    break;
                    
                case 'complete':
                    if ($withdrawal->status === 'processing') {
                        $withdrawal->update([
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $withdrawal->updateStatus('completed', $adminId, 'admin', '批量完成提现申请', [
                            'admin_notes' => $request->admin_notes,
                        ]);
                        $count++;
                    }
                    break;
            }
        }

        $message = "成功处理 {$count} 个提现申请";
        return redirect()->route('admin.withdrawals.index')
            ->with('success', $message);
    }

    /**
     * 获取提现统计
     */
    public function statistics(Request $request)
    {
        $query = Withdrawal::query();

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $stats = [
            'total_withdrawals' => $query->count(),
            'pending_withdrawals' => $query->clone()->status('pending')->count(),
            'processing_withdrawals' => $query->clone()->status('processing')->count(),
            'completed_withdrawals' => $query->clone()->status('completed')->count(),
            'rejected_withdrawals' => $query->clone()->status('rejected')->count(),
            'cancelled_withdrawals' => $query->clone()->status('cancelled')->count(),
            'total_amount' => $query->clone()->sum('amount'),
            'total_fee' => $query->clone()->sum('fee'),
            'total_actual_amount' => $query->clone()->sum('actual_amount'),
            'avg_amount' => $query->clone()->avg('amount'),
            'max_amount' => $query->clone()->max('amount'),
            'min_amount' => $query->clone()->min('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * 导出提现数据
     */
    public function export(Request $request)
    {
        $query = Withdrawal::with(['merchant', 'processor']);

        // 应用筛选条件
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('merchant_id')) {
            $query->merchant($request->merchant_id);
        }
        if ($request->filled('method')) {
            $query->method($request->method);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->get();

        // 这里可以集成Laravel Excel来导出数据
        // 暂时返回JSON格式
        return response()->json([
            'data' => $withdrawals,
            'count' => $withdrawals->count(),
        ]);
    }
}
