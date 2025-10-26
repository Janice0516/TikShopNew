<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchants = [
            [
                'merchant_name' => '张三数码店',
                'username' => 'zhangsan',
                'email' => 'zhangsan@example.com',
                'contact_name' => '张三',
                'contact_phone' => '0123456789',
                'shop_name' => '张三数码专营店',
                'status' => 'active',
                'verified' => true,
                'balance' => 15000.00,
            ],
            [
                'merchant_name' => '李四服装店',
                'username' => 'lisi',
                'email' => 'lisi@example.com',
                'contact_name' => '李四',
                'contact_phone' => '0123456790',
                'shop_name' => '李四时尚服装',
                'status' => 'active',
                'verified' => true,
                'balance' => 8500.00,
            ],
            [
                'merchant_name' => '王五家居',
                'username' => 'wangwu',
                'email' => 'wangwu@example.com',
                'contact_name' => '王五',
                'contact_phone' => '0123456791',
                'shop_name' => '王五家居生活馆',
                'status' => 'active',
                'verified' => false,
                'balance' => 3200.00,
            ],
            [
                'merchant_name' => '赵六美妆',
                'username' => 'zhaoliu',
                'email' => 'zhaoliu@example.com',
                'contact_name' => '赵六',
                'contact_phone' => '0123456792',
                'shop_name' => '赵六美妆护肤',
                'status' => 'inactive',
                'verified' => true,
                'balance' => 1200.00,
            ],
            [
                'merchant_name' => '孙七运动',
                'username' => 'sunqi',
                'email' => 'sunqi@example.com',
                'contact_name' => '孙七',
                'contact_phone' => '0123456793',
                'shop_name' => '孙七运动装备',
                'status' => 'suspended',
                'verified' => false,
                'balance' => 500.00,
            ],
        ];

        foreach ($merchants as $merchantData) {
            $verified = $merchantData['verified'];
            unset($merchantData['verified']);
            
            // 创建用户账户
            $user = User::create([
                'name' => $merchantData['merchant_name'],
                'email' => $merchantData['email'],
                'password' => Hash::make('password123'),
            ]);

            // 生成邀请码
            $inviteCode = strtoupper(Str::random(8));

            // 创建商家信息
            Merchant::create([
                'user_id' => $user->id,
                'merchant_name' => $merchantData['merchant_name'],
                'username' => $merchantData['username'],
                'contact_name' => $merchantData['contact_name'],
                'contact_phone' => $merchantData['contact_phone'],
                'shop_name' => $merchantData['shop_name'],
                'invite_code' => $inviteCode,
                'status' => $merchantData['status'],
                'balance' => $merchantData['balance'],
                'verified_at' => $verified ? now() : null,
                'last_login_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
            ]);
        }
    }
}
