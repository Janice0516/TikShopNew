<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建权限
        $permissions = [
            // 仪表盘权限
            ['name' => '查看仪表盘', 'slug' => 'dashboard.view', 'group' => 'dashboard', 'description' => '查看管理后台仪表盘'],
            
            // 商品管理权限
            ['name' => '查看商品', 'slug' => 'products.view', 'group' => 'products', 'description' => '查看商品列表'],
            ['name' => '创建商品', 'slug' => 'products.create', 'group' => 'products', 'description' => '创建新商品'],
            ['name' => '编辑商品', 'slug' => 'products.edit', 'group' => 'products', 'description' => '编辑商品信息'],
            ['name' => '删除商品', 'slug' => 'products.delete', 'group' => 'products', 'description' => '删除商品'],
            ['name' => '批量操作商品', 'slug' => 'products.bulk', 'group' => 'products', 'description' => '批量操作商品'],
            
            // 分类管理权限
            ['name' => '查看分类', 'slug' => 'categories.view', 'group' => 'categories', 'description' => '查看分类列表'],
            ['name' => '创建分类', 'slug' => 'categories.create', 'group' => 'categories', 'description' => '创建新分类'],
            ['name' => '编辑分类', 'slug' => 'categories.edit', 'group' => 'categories', 'description' => '编辑分类信息'],
            ['name' => '删除分类', 'slug' => 'categories.delete', 'group' => 'categories', 'description' => '删除分类'],
            ['name' => '批量操作分类', 'slug' => 'categories.bulk', 'group' => 'categories', 'description' => '批量操作分类'],
            
            // 商家管理权限
            ['name' => '查看商家', 'slug' => 'merchants.view', 'group' => 'merchants', 'description' => '查看商家列表'],
            ['name' => '创建商家', 'slug' => 'merchants.create', 'group' => 'merchants', 'description' => '创建新商家'],
            ['name' => '编辑商家', 'slug' => 'merchants.edit', 'group' => 'merchants', 'description' => '编辑商家信息'],
            ['name' => '删除商家', 'slug' => 'merchants.delete', 'group' => 'merchants', 'description' => '删除商家'],
            ['name' => '审核商家', 'slug' => 'merchants.verify', 'group' => 'merchants', 'description' => '审核商家认证'],
            ['name' => '批量操作商家', 'slug' => 'merchants.bulk', 'group' => 'merchants', 'description' => '批量操作商家'],
            
            // 订单管理权限
            ['name' => '查看订单', 'slug' => 'orders.view', 'group' => 'orders', 'description' => '查看订单列表'],
            ['name' => '编辑订单', 'slug' => 'orders.edit', 'group' => 'orders', 'description' => '编辑订单信息'],
            ['name' => '更新订单状态', 'slug' => 'orders.status', 'group' => 'orders', 'description' => '更新订单状态'],
            ['name' => '批量操作订单', 'slug' => 'orders.bulk', 'group' => 'orders', 'description' => '批量操作订单'],
            
            // 用户管理权限
            ['name' => '查看用户', 'slug' => 'users.view', 'group' => 'users', 'description' => '查看用户列表'],
            ['name' => '编辑用户', 'slug' => 'users.edit', 'group' => 'users', 'description' => '编辑用户信息'],
            ['name' => '删除用户', 'slug' => 'users.delete', 'group' => 'users', 'description' => '删除用户'],
            ['name' => '批量操作用户', 'slug' => 'users.bulk', 'group' => 'users', 'description' => '批量操作用户'],
            
            // 角色管理权限
            ['name' => '查看角色', 'slug' => 'roles.view', 'group' => 'roles', 'description' => '查看角色列表'],
            ['name' => '创建角色', 'slug' => 'roles.create', 'group' => 'roles', 'description' => '创建新角色'],
            ['name' => '编辑角色', 'slug' => 'roles.edit', 'group' => 'roles', 'description' => '编辑角色信息'],
            ['name' => '删除角色', 'slug' => 'roles.delete', 'group' => 'roles', 'description' => '删除角色'],
            ['name' => '分配权限', 'slug' => 'roles.permissions', 'group' => 'roles', 'description' => '分配角色权限'],
            
            // 系统设置权限
            ['name' => '查看设置', 'slug' => 'settings.view', 'group' => 'settings', 'description' => '查看系统设置'],
            ['name' => '编辑设置', 'slug' => 'settings.edit', 'group' => 'settings', 'description' => '编辑系统设置'],
            
            // 财务管理权限
            ['name' => '查看财务', 'slug' => 'finance.view', 'group' => 'finance', 'description' => '查看财务数据'],
            ['name' => '编辑财务', 'slug' => 'finance.edit', 'group' => 'finance', 'description' => '编辑财务数据'],
            
            // 提现管理权限
            ['name' => '查看提现', 'slug' => 'withdrawals.view', 'group' => 'withdrawals', 'description' => '查看提现申请'],
            ['name' => '审核提现', 'slug' => 'withdrawals.verify', 'group' => 'withdrawals', 'description' => '审核提现申请'],
        ];

        foreach ($permissions as $index => $permission) {
            Permission::create([
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'group' => $permission['group'],
                'description' => $permission['description'],
                'sort_order' => $index + 1,
            ]);
        }

        // 创建角色
        $roles = [
            [
                'name' => '超级管理员',
                'slug' => 'super_admin',
                'description' => '拥有所有权限的超级管理员',
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 1,
                'permissions' => Permission::all()->pluck('id')->toArray(),
            ],
            [
                'name' => '管理员',
                'slug' => 'admin',
                'description' => '普通管理员，拥有大部分权限',
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 2,
                'permissions' => Permission::whereNotIn('slug', [
                    'roles.delete',
                    'settings.edit',
                ])->pluck('id')->toArray(),
            ],
            [
                'name' => '商品管理员',
                'slug' => 'product_manager',
                'description' => '负责商品和分类管理',
                'is_system' => false,
                'is_active' => true,
                'sort_order' => 3,
                'permissions' => Permission::whereIn('group', ['dashboard', 'products', 'categories'])->pluck('id')->toArray(),
            ],
            [
                'name' => '订单管理员',
                'slug' => 'order_manager',
                'description' => '负责订单和用户管理',
                'is_system' => false,
                'is_active' => true,
                'sort_order' => 4,
                'permissions' => Permission::whereIn('group', ['dashboard', 'orders', 'users'])->pluck('id')->toArray(),
            ],
            [
                'name' => '财务管理员',
                'slug' => 'finance_manager',
                'description' => '负责财务和提现管理',
                'is_system' => false,
                'is_active' => true,
                'sort_order' => 5,
                'permissions' => Permission::whereIn('group', ['dashboard', 'finance', 'withdrawals'])->pluck('id')->toArray(),
            ],
            [
                'name' => '只读用户',
                'slug' => 'viewer',
                'description' => '只能查看数据，不能修改',
                'is_system' => false,
                'is_active' => true,
                'sort_order' => 6,
                'permissions' => Permission::where('slug', 'like', '%.view')->pluck('id')->toArray(),
            ],
        ];

        foreach ($roles as $roleData) {
            $permissionIds = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = Role::create($roleData);
            $role->assignPermissions($permissionIds);
        }
    }
}
