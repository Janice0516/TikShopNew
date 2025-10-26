<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateAdminRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 更新超级管理员角色
        $superAdmin = Admin::where('username', 'admin')->first();
        if ($superAdmin) {
            $superAdminRole = Role::where('slug', 'super_admin')->first();
            if ($superAdminRole) {
                $superAdmin->assignRoles([$superAdminRole->id]);
                echo "超级管理员角色分配完成\n";
            }
        }

        // 更新普通管理员角色
        $manager = Admin::where('username', 'manager')->first();
        if ($manager) {
            $adminRole = Role::where('slug', 'admin')->first();
            if ($adminRole) {
                $manager->assignRoles([$adminRole->id]);
                echo "普通管理员角色分配完成\n";
            }
        }
    }
}
