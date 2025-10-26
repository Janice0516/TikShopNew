<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MerchantController extends Controller
{
    /**
     * 显示商家列表
     */
    public function index(Request $request)
    {
        $query = User::where('type', 'merchant');

        // 搜索
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%');
            });
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 按认证状态筛选
        if ($request->filled('verified')) {
            $query->where('is_verified', $request->boolean('verified'));
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $merchants = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $statuses = [
            'active' => '正常',
            'inactive' => '停用',
            'suspended' => '暂停',
        ];

        return view('admin.merchants.index', compact('merchants', 'statuses'));
    }

    /**
     * 显示创建商家表单
     */
    public function create()
    {
        return view('admin.merchants.create');
    }

    /**
     * 存储新商家
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'business_license' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'is_verified' => 'boolean',
            'balance' => 'nullable|numeric|min:0',
            'frozen_amount' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['type'] = 'merchant';
        $data['is_verified'] = $request->boolean('is_verified');

        User::create($data);

        return redirect()->route('admin.merchants.index')
            ->with('success', '商家创建成功');
    }

    /**
     * 显示商家详情
     */
    public function show(User $merchant)
    {
        $merchant->load(['products', 'orders', 'inviteCodes']);
        return view('admin.merchants.show', compact('merchant'));
    }

    /**
     * 显示编辑商家表单
     */
    public function edit(User $merchant)
    {
        return view('admin.merchants.edit', compact('merchant'));
    }

    /**
     * 更新商家
     */
    public function update(Request $request, User $merchant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $merchant->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'business_license' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'is_verified' => 'boolean',
            'balance' => 'nullable|numeric|min:0',
            'frozen_amount' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_verified'] = $request->boolean('is_verified');

        $merchant->update($data);

        return redirect()->route('admin.merchants.index')
            ->with('success', '商家更新成功');
    }

    /**
     * 删除商家
     */
    public function destroy(User $merchant)
    {
        $merchant->delete();

        return redirect()->route('admin.merchants.index')
            ->with('success', '商家删除成功');
    }

    /**
     * 更新商家状态
     */
    public function updateStatus(Request $request, User $merchant)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $merchant->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', '商家状态更新成功');
    }

    /**
     * 更新认证状态
     */
    public function updateVerification(Request $request, User $merchant)
    {
        $merchant->update(['is_verified' => !$merchant->is_verified]);

        return redirect()->back()
            ->with('success', '认证状态更新成功');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,suspend,delete',
            'merchants' => 'required|array',
            'merchants.*' => 'exists:users,id',
        ]);

        $merchants = User::whereIn('id', $request->merchants)->get();

        switch ($request->action) {
            case 'activate':
                User::whereIn('id', $request->merchants)->update(['status' => 'active']);
                $message = '批量启用成功';
                break;
            case 'deactivate':
                User::whereIn('id', $request->merchants)->update(['status' => 'inactive']);
                $message = '批量禁用成功';
                break;
            case 'suspend':
                User::whereIn('id', $request->merchants)->update(['status' => 'suspended']);
                $message = '批量暂停成功';
                break;
            case 'delete':
                User::whereIn('id', $request->merchants)->delete();
                $message = '批量删除成功';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * 获取商家统计信息
     */
    public function statistics()
    {
        $stats = [
            'total_merchants' => User::where('type', 'merchant')->count(),
            'active_merchants' => User::where('type', 'merchant')->where('status', 'active')->count(),
            'verified_merchants' => User::where('type', 'merchant')->where('is_verified', true)->count(),
            'new_merchants_today' => User::where('type', 'merchant')->whereDate('created_at', today())->count(),
        ];

        return response()->json($stats);
    }
}