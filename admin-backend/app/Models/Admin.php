<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'name',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
        'avatar',
        'notifications',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'notifications' => 'array',
            'settings' => 'array',
        ];
    }

    /**
     * 获取管理员的角色
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'admin_role');
    }

    /**
     * 检查管理员是否有特定权限
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        // 检查角色权限
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        // 检查直接权限
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * 检查管理员是否有任一权限
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查管理员是否有所有权限
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 给管理员分配角色
     */
    public function assignRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
    }

    /**
     * 移除管理员的角色
     */
    public function revokeRoles(array $roleIds): void
    {
        $this->roles()->detach($roleIds);
    }

    /**
     * 检查管理员是否有特定角色
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * 获取管理员的所有权限
     */
    public function getAllPermissions(): array
    {
        $permissions = [];
        
        // 从角色获取权限
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->slug;
            }
        }
        
        // 添加直接权限
        $directPermissions = $this->permissions ?? [];
        $permissions = array_merge($permissions, $directPermissions);
        
        return array_unique($permissions);
    }
}
