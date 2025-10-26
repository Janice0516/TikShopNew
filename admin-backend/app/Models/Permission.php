<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * 获取拥有此权限的角色
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * 作用域：激活的权限
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 作用域：按分组
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
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
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('slug', 'like', "%{$keyword}%");
        });
    }

    /**
     * 获取权限分组
     */
    public static function getGroups()
    {
        return self::distinct()->pluck('group')->toArray();
    }

    /**
     * 按分组获取权限
     */
    public static function getByGroup($group = null)
    {
        $query = self::active()->ordered();
        
        if ($group) {
            $query->where('group', $group);
        }
        
        return $query->get()->groupBy('group');
    }
}
