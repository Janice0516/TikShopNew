<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * 显示角色列表
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // 按类型筛选
        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->system();
            } elseif ($request->type === 'custom') {
                $query->custom();
            }
        }

        // 排序
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $roles = $query->paginate(20)->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * 显示创建角色表单
     */
    public function create()
    {
        $permissions = Permission::active()->ordered()->get()->groupBy('group');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * 创建角色
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // 分配权限
        if ($request->filled('permissions')) {
            $role->assignPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', '角色创建成功');
    }

    /**
     * 显示角色详情
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'admins']);
        return view('admin.roles.show', compact('role'));
    }

    /**
     * 显示编辑角色表单
     */
    public function edit(Role $role)
    {
        $permissions = Permission::active()->ordered()->get()->groupBy('group');
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * 更新角色
     */
    public function update(Request $request, Role $role)
    {
        // 系统角色不能修改名称和状态
        $rules = [
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        if (!$role->is_system) {
            $rules['name'] = 'required|string|max:255|unique:roles,name,' . $role->id;
            $rules['is_active'] = 'boolean';
            $rules['sort_order'] = 'integer|min:0';
        }

        $request->validate($rules);

        $data = [
            'description' => $request->description,
        ];

        if (!$role->is_system) {
            $data['name'] = $request->name;
            $data['slug'] = Str::slug($request->name);
            $data['is_active'] = $request->boolean('is_active', true);
            $data['sort_order'] = $request->sort_order ?? 0;
        }

        $role->update($data);

        // 更新权限
        if ($request->filled('permissions')) {
            $role->assignPermissions($request->permissions);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('admin.roles.index')
            ->with('success', '角色更新成功');
    }

    /**
     * 删除角色
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('admin.roles.index')
                ->with('error', '系统角色不能删除');
        }

        // 检查是否有管理员使用此角色
        if ($role->admins()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', '该角色正在被使用，无法删除');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', '角色删除成功');
    }

    /**
     * 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $roleIds = $request->role_ids;

        switch ($request->action) {
            case 'activate':
                Role::whereIn('id', $roleIds)
                    ->where('is_system', false)
                    ->update(['is_active' => true]);
                $message = '角色已批量激活';
                break;
            case 'deactivate':
                Role::whereIn('id', $roleIds)
                    ->where('is_system', false)
                    ->update(['is_active' => false]);
                $message = '角色已批量停用';
                break;
            case 'delete':
                // 检查是否有管理员使用
                $rolesInUse = Role::whereIn('id', $roleIds)
                    ->whereHas('admins')
                    ->count();
                
                if ($rolesInUse > 0) {
                    return redirect()->route('admin.roles.index')
                        ->with('error', '部分角色正在被使用，无法删除');
                }

                Role::whereIn('id', $roleIds)
                    ->where('is_system', false)
                    ->delete();
                $message = '角色已批量删除';
                break;
        }

        return redirect()->route('admin.roles.index')
            ->with('success', $message);
    }

    /**
     * 更新角色状态
     */
    public function updateStatus(Request $request, Role $role)
    {
        if ($role->is_system) {
            return response()->json(['error' => '系统角色不能修改状态'], 400);
        }

        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $role->update(['is_active' => $request->is_active]);

        return response()->json(['success' => true]);
    }

    /**
     * 复制角色
     */
    public function duplicate(Role $role)
    {
        $newRole = Role::create([
            'name' => $role->name . ' (副本)',
            'slug' => Str::slug($role->name . '-copy-' . time()),
            'description' => $role->description,
            'is_active' => false,
            'sort_order' => $role->sort_order,
        ]);

        // 复制权限
        $permissionIds = $role->permissions()->pluck('permissions.id')->toArray();
        $newRole->assignPermissions($permissionIds);

        return redirect()->route('admin.roles.edit', $newRole)
            ->with('success', '角色复制成功');
    }
}
