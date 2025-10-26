<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminAccountController extends Controller
{
    /**
     * 显示管理员账户列表
     */
    public function index(Request $request)
    {
        $query = Admin::with('roles');

        // 搜索
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // 按角色筛选
        if ($request->filled('role_id')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            });
        }

        // 按状态筛选
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // 按注册时间筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $admins = $query->paginate(20)->withQueryString();

        // 获取角色列表用于筛选
        $roles = Role::active()->ordered()->get();

        return view('admin.admin-accounts.index', compact('admins', 'roles'));
    }

    /**
     * 显示创建管理员表单
     */
    public function create()
    {
        $roles = Role::active()->ordered()->get();
        return view('admin.admin-accounts.create', compact('roles'));
    }

    /**
     * 创建管理员
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:admins,username',
            'email' => 'required|email|max:255|unique:admins,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,super_admin',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $admin = Admin::create([
            'username' => $request->username,
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // 分配角色
        if ($request->filled('roles')) {
            $admin->assignRoles($request->roles);
        }

        return redirect()->route('admin.admin-accounts.index')
            ->with('success', '管理员账户创建成功');
    }

    /**
     * 显示管理员详情
     */
    public function show(Admin $admin)
    {
        $admin->load(['roles']);
        
        // 获取管理员统计信息
        $stats = [
            'login_count' => rand(10, 100), // 模拟数据
            'last_login' => $admin->last_login_at,
            'account_created' => $admin->created_at,
            'permissions_count' => $admin->getAllPermissions(),
        ];

        return view('admin.admin-accounts.show', compact('admin', 'stats'));
    }

    /**
     * 显示编辑管理员表单
     */
    public function edit(Admin $admin)
    {
        $roles = Role::active()->ordered()->get();
        $admin->load('roles');
        return view('admin.admin-accounts.edit', compact('admin', 'roles'));
    }

    /**
     * 更新管理员
     */
    public function update(Request $request, Admin $admin)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'role' => 'required|string|in:admin,super_admin',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ];

        // 如果不是超级管理员，不能修改自己的角色
        if (Auth::guard('admin')->id() === $admin->id && $admin->role === 'super_admin') {
            unset($rules['role']);
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->boolean('is_active', true),
        ];

        // 更新角色（如果不是修改自己）
        if (Auth::guard('admin')->id() !== $admin->id) {
            $data['role'] = $request->role;
        }

        $admin->update($data);

        // 更新角色分配
        if ($request->filled('roles')) {
            $admin->assignRoles($request->roles);
        } else {
            $admin->roles()->detach();
        }

        return redirect()->route('admin.admin-accounts.index')
            ->with('success', '管理员信息已更新');
    }

    /**
     * 删除管理员
     */
    public function destroy(Admin $admin)
    {
        // 不能删除自己
        if (Auth::guard('admin')->id() === $admin->id) {
            return redirect()->route('admin.admin-accounts.index')
                ->with('error', '不能删除自己的账户');
        }

        // 不能删除超级管理员
        if ($admin->role === 'super_admin') {
            return redirect()->route('admin.admin-accounts.index')
                ->with('error', '不能删除超级管理员账户');
        }

        $admin->delete();

        return redirect()->route('admin.admin-accounts.index')
            ->with('success', '管理员删除成功');
    }

    /**
     * 重置密码
     */
    public function resetPassword(Request $request, Admin $admin)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.admin-accounts.show', $admin)
            ->with('success', '密码重置成功');
    }

    /**
     * 更新状态
     */
    public function updateStatus(Request $request, Admin $admin)
    {
        // 不能修改自己的状态
        if (Auth::guard('admin')->id() === $admin->id) {
            return response()->json(['error' => '不能修改自己的状态'], 400);
        }

        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $admin->update(['is_active' => $request->is_active]);

        return response()->json(['success' => true]);
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'admin_ids' => 'required|array',
            'admin_ids.*' => 'exists:admins,id',
        ]);

        $adminIds = $request->admin_ids;
        $currentAdminId = Auth::guard('admin')->id();

        // 移除当前管理员ID
        $adminIds = array_filter($adminIds, function($id) use ($currentAdminId) {
            return $id != $currentAdminId;
        });

        if (empty($adminIds)) {
            return redirect()->route('admin.admin-accounts.index')
                ->with('error', '没有可操作的管理员');
        }

        switch ($request->action) {
            case 'activate':
                Admin::whereIn('id', $adminIds)
                    ->where('role', '!=', 'super_admin')
                    ->update(['is_active' => true]);
                $message = '管理员已批量激活';
                break;
            case 'deactivate':
                Admin::whereIn('id', $adminIds)
                    ->where('role', '!=', 'super_admin')
                    ->update(['is_active' => false]);
                $message = '管理员已批量停用';
                break;
            case 'delete':
                // 检查是否有超级管理员
                $superAdmins = Admin::whereIn('id', $adminIds)
                    ->where('role', 'super_admin')
                    ->count();
                
                if ($superAdmins > 0) {
                    return redirect()->route('admin.admin-accounts.index')
                        ->with('error', '不能删除超级管理员');
                }

                Admin::whereIn('id', $adminIds)->delete();
                $message = '管理员已批量删除';
                break;
        }

        return redirect()->route('admin.admin-accounts.index')
            ->with('success', $message);
    }

    /**
     * 获取管理员统计
     */
    public function statistics(Request $request)
    {
        $query = Admin::query();

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $stats = [
            'total_admins' => $query->count(),
            'active_admins' => $query->clone()->where('is_active', true)->count(),
            'inactive_admins' => $query->clone()->where('is_active', false)->count(),
            'super_admins' => $query->clone()->where('role', 'super_admin')->count(),
            'regular_admins' => $query->clone()->where('role', 'admin')->count(),
            'new_admins_today' => Admin::whereDate('created_at', today())->count(),
            'new_admins_this_month' => Admin::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        return response()->json($stats);
    }
}
