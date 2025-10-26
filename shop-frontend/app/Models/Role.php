<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'is_system',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    /**
     * 获取角色的权限
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * 获取拥有此角色的管理员
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'admin_role');
    }

    /**
     * 检查角色是否有特定权限
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * 检查角色是否有任一权限
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions()->whereIn('slug', $permissions)->exists();
    }

    /**
     * 检查角色是否有所有权限
     */
    public function hasAllPermissions(array $permissions): bool
    {
        $rolePermissions = $this->permissions()->pluck('slug')->toArray();
        return count(array_intersect($permissions, $rolePermissions)) === count($permissions);
    }

    /**
     * 给角色分配权限
     */
    public function assignPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * 移除角色的权限
     */
    public function revokePermissions(array $permissionIds): void
    {
        $this->permissions()->detach($permissionIds);
    }

    /**
     * 作用域：激活的角色
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 作用域：系统角色
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * 作用域：非系统角色
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * 作用域：按排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }
}
