<?php

namespace App\Http\Controllers;

use App\Models\InviteCode;
use App\Models\InviteCodeUsage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InviteCodeController extends Controller
{
    /**
     * 显示邀请码列表
     */
    public function index(Request $request)
    {
        $query = InviteCode::with(['creator', 'merchant']);

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按类型筛选
        if ($request->filled('type')) {
            $query->type($request->type);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            } elseif ($request->status === 'used_up') {
                $query->whereRaw('used_count >= max_uses');
            }
        }

        // 按商家筛选
        if ($request->filled('merchant_id')) {
            $query->merchant($request->merchant_id);
        }

        // 按创建时间筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $inviteCodes = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $merchants = User::where('type', 'merchant')->get();

        return view('admin.invite-codes.index', compact('inviteCodes', 'merchants'));
    }

    /**
     * 显示创建邀请码表单
     */
    public function create()
    {
        $merchants = User::where('type', 'merchant')->get();
        return view('admin.invite-codes.create', compact('merchants'));
    }

    /**
     * 创建邀请码
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|max:20|unique:invite_codes,code',
            'type' => 'required|string|in:merchant,customer,admin',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'merchant_id' => 'nullable|exists:users,id',
            'max_uses' => 'nullable|integer|min:1',
            'reward_amount' => 'nullable|numeric|min:0',
            'reward_type' => 'required|string|in:cash,points,discount',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['creator_id'] = Auth::guard('admin')->id();
        
        // 如果没有提供邀请码，自动生成
        if (empty($data['code'])) {
            $data['code'] = InviteCode::generateCode();
        }

        $inviteCode = InviteCode::create($data);

        return redirect()->route('admin.invite-codes.index')
            ->with('success', '邀请码创建成功');
    }

    /**
     * 显示邀请码详情
     */
    public function show(InviteCode $inviteCode)
    {
        $inviteCode->load(['creator', 'merchant', 'usages.user']);
        
        // 获取统计信息
        $stats = [
            'total_usages' => $inviteCode->usages->count(),
            'successful_usages' => $inviteCode->usages->where('status', 'success')->count(),
            'failed_usages' => $inviteCode->usages->where('status', 'failed')->count(),
            'total_reward_amount' => $inviteCode->usages->where('status', 'success')->sum('reward_amount'),
            'usage_rate' => $inviteCode->usage_rate,
        ];

        return view('admin.invite-codes.show', compact('inviteCode', 'stats'));
    }

    /**
     * 显示编辑邀请码表单
     */
    public function edit(InviteCode $inviteCode)
    {
        $merchants = User::where('type', 'merchant')->get();
        return view('admin.invite-codes.edit', compact('inviteCode', 'merchants'));
    }

    /**
     * 更新邀请码
     */
    public function update(Request $request, InviteCode $inviteCode)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:invite_codes,code,' . $inviteCode->id,
            'type' => 'required|string|in:merchant,customer,admin',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'merchant_id' => 'nullable|exists:users,id',
            'max_uses' => 'nullable|integer|min:1',
            'reward_amount' => 'nullable|numeric|min:0',
            'reward_type' => 'required|string|in:cash,points,discount',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $inviteCode->update($request->all());

        return redirect()->route('admin.invite-codes.index')
            ->with('success', '邀请码已更新');
    }

    /**
     * 删除邀请码
     */
    public function destroy(InviteCode $inviteCode)
    {
        $inviteCode->delete();

        return redirect()->route('admin.invite-codes.index')
            ->with('success', '邀请码删除成功');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'invite_code_ids' => 'required|array',
            'invite_code_ids.*' => 'exists:invite_codes,id',
        ]);

        $inviteCodeIds = $request->invite_code_ids;
        $count = 0;

        switch ($request->action) {
            case 'activate':
                InviteCode::whereIn('id', $inviteCodeIds)->update(['is_active' => true]);
                $count = count($inviteCodeIds);
                $message = "成功激活 {$count} 个邀请码";
                break;
                
            case 'deactivate':
                InviteCode::whereIn('id', $inviteCodeIds)->update(['is_active' => false]);
                $count = count($inviteCodeIds);
                $message = "成功停用 {$count} 个邀请码";
                break;
                
            case 'delete':
                InviteCode::whereIn('id', $inviteCodeIds)->delete();
                $count = count($inviteCodeIds);
                $message = "成功删除 {$count} 个邀请码";
                break;
        }

        return redirect()->route('admin.invite-codes.index')
            ->with('success', $message);
    }

    /**
     * 更新状态
     */
    public function updateStatus(Request $request, InviteCode $inviteCode)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $inviteCode->update(['is_active' => $request->is_active]);

        return response()->json(['success' => true]);
    }

    /**
     * 批量生成邀请码
     */
    public function batchGenerate(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
            'type' => 'required|string|in:merchant,customer,admin',
            'name_prefix' => 'nullable|string|max:50',
            'max_uses' => 'nullable|integer|min:1',
            'reward_amount' => 'nullable|numeric|min:0',
            'reward_type' => 'required|string|in:cash,points,discount',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $count = $request->count;
        $created = 0;

        for ($i = 0; $i < $count; $i++) {
            $data = [
                'code' => InviteCode::generateCode(),
                'type' => $request->type,
                'name' => $request->name_prefix ? $request->name_prefix . ($i + 1) : null,
                'creator_id' => Auth::guard('admin')->id(),
                'max_uses' => $request->max_uses,
                'reward_amount' => $request->reward_amount,
                'reward_type' => $request->reward_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => true,
            ];

            InviteCode::create($data);
            $created++;
        }

        return redirect()->route('admin.invite-codes.index')
            ->with('success', "成功生成 {$created} 个邀请码");
    }

    /**
     * 获取邀请码统计
     */
    public function statistics(Request $request)
    {
        $query = InviteCode::query();

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $stats = [
            'total_invite_codes' => $query->count(),
            'active_invite_codes' => $query->clone()->active()->count(),
            'expired_invite_codes' => $query->clone()->where('end_date', '<', now())->count(),
            'used_up_invite_codes' => $query->clone()->whereRaw('used_count >= max_uses')->count(),
            'total_usages' => InviteCodeUsage::whereHas('inviteCode', function($q) use ($query) {
                $q->whereIn('id', $query->clone()->pluck('id'));
            })->count(),
            'total_reward_amount' => InviteCodeUsage::whereHas('inviteCode', function($q) use ($query) {
                $q->whereIn('id', $query->clone()->pluck('id'));
            })->where('status', 'success')->sum('reward_amount'),
            'new_codes_today' => InviteCode::whereDate('created_at', today())->count(),
            'new_codes_this_month' => InviteCode::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * 获取使用记录
     */
    public function usages(Request $request, InviteCode $inviteCode)
    {
        $query = $inviteCode->usages()->with('user');

        // 按状态筛选
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // 按用户类型筛选
        if ($request->filled('user_type')) {
            $query->userType($request->user_type);
        }

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        $usages = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.invite-codes.usages', compact('inviteCode', 'usages'));
    }
}
