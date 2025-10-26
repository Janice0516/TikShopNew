<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 显示用户列表
     */
    public function index(Request $request)
    {
        $query = User::query();

        // 搜索
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        // 按用户类型筛选
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 按认证状态筛选（仅商家）
        if ($request->filled('verified')) {
            $query->where('is_verified', $request->boolean('verified'));
        }

        // 按注册时间筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $types = [
            'customer' => '客户',
            'merchant' => '商家',
        ];
        $statuses = [
            'active' => '正常',
            'inactive' => '禁用',
            'suspended' => '暂停',
        ];

        return view('admin.users.index', compact('users', 'types', 'statuses'));
    }

    /**
     * 显示创建用户表单
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * 存储新用户
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:customer,merchant',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'business_license' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'is_verified' => 'boolean',
            'balance' => 'nullable|numeric|min:0',
            'frozen_amount' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['is_verified'] = $request->boolean('is_verified');

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', '用户创建成功');
    }

    /**
     * 显示用户详情
     */
    public function show(User $user)
    {
        $user->load(['products', 'orders', 'inviteCodes']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * 显示编辑用户表单
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * 更新用户
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:customer,merchant',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'business_license' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'is_verified' => 'boolean',
            'balance' => 'nullable|numeric|min:0',
            'frozen_amount' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_verified'] = $request->boolean('is_verified');

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', '用户更新成功');
    }

    /**
     * 删除用户
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', '用户删除成功');
    }

    /**
     * 更新用户状态
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', '用户状态更新成功');
    }

    /**
     * 更新认证状态
     */
    public function updateVerification(Request $request, User $user)
    {
        if ($user->type !== 'merchant') {
            return redirect()->back()
                ->with('error', '只有商家才能进行认证操作');
        }

        $user->update(['is_verified' => !$user->is_verified]);

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
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->users)->get();

        switch ($request->action) {
            case 'activate':
                User::whereIn('id', $request->users)->update(['status' => 'active']);
                $message = '批量启用成功';
                break;
            case 'deactivate':
                User::whereIn('id', $request->users)->update(['status' => 'inactive']);
                $message = '批量禁用成功';
                break;
            case 'suspend':
                User::whereIn('id', $request->users)->update(['status' => 'suspended']);
                $message = '批量暂停成功';
                break;
            case 'delete':
                User::whereIn('id', $request->users)->delete();
                $message = '批量删除成功';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * 获取用户统计信息
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'total_customers' => User::where('type', 'customer')->count(),
            'total_merchants' => User::where('type', 'merchant')->count(),
            'active_users' => User::where('status', 'active')->count(),
            'verified_merchants' => User::where('type', 'merchant')->where('is_verified', true)->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
        ];

        return response()->json($stats);
    }
}