<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MerchantProfile;

class MerchantDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        $user = User::firstOrCreate(
            ['email' => 'merchant001@example.com'],
            [
                'name' => 'merchant001',
                'password' => Hash::make('password123'),
            ]
        );

        // Create merchant profile linked to user
        MerchantProfile::firstOrCreate(
            ['username' => 'merchant001'],
            [
                'user_id' => $user->id,
                'merchant_name' => 'Merchant 001',
                'contact_name' => 'Demo User',
                'contact_phone' => '0101234567',
                'shop_name' => 'Demo Shop',
                'invite_code' => 'INVITE1',
            ]
        );
    }
}