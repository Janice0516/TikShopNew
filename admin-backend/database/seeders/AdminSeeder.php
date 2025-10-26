<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建超级管理员
        $superAdmin = Admin::create([
            'username' => 'admin',
            'email' => 'admin@tiktokshop.com',
            'password' => Hash::make('admin123'),
            'name' => '超级管理员',
            'role' => 'super_admin',
            'permissions' => ['*'], // 所有权限
            'is_active' => true,
        ]);

        // 创建普通管理员
        $manager = Admin::create([
            'username' => 'manager',
            'email' => 'manager@tiktokshop.com',
            'password' => Hash::make('manager123'),
            'name' => '管理员',
            'role' => 'admin',
            'permissions' => [
                'dashboard.view',
                'merchants.view',
                'merchants.edit',
                'orders.view',
                'orders.edit',
                'products.view',
                'products.edit',
                'users.view',
                'settings.view',
            ],
            'is_active' => true,
        ]);

        // 分配角色（如果角色存在）
        try {
            $superAdminRole = \App\Models\Role::where('slug', 'super_admin')->first();
            if ($superAdminRole) {
                $superAdmin->assignRoles([$superAdminRole->id]);
            }

            $adminRole = \App\Models\Role::where('slug', 'admin')->first();
            if ($adminRole) {
                $manager->assignRoles([$adminRole->id]);
            }
        } catch (\Exception $e) {
            // 角色可能还没有创建，忽略错误
        }
    }
}
