<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class WomenswearProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 找到女装分类
        $womenswearCategory = Category::where('slug', 'womens-clothing')->first();
        
        if (!$womenswearCategory) {
            $this->command->warn('Womenswear category not found. Please run SubCategorySeeder first.');
            return;
        }

        // 找到或创建商家
        $merchant = User::where('type', 'merchant')->first();
        if (!$merchant) {
            $merchant = User::create([
                'name' => 'Fashion Store',
                'email' => 'fashion@example.com',
                'phone' => '+60123456789',
                'password' => bcrypt('password'),
                'type' => 'merchant',
            ]);
        }

        // 女装商品数据
        $products = [
            [
                'name' => 'ELGINI E16373 Jaket Varsiti S-X XL | Varsity Jacket',
                'description' => 'Stylish varsity jacket with large E emblem, perfect for casual wear',
                'price' => 39.90,
                'cost_price' => 25.00,
                'markup_price' => 39.90,
                'suggest_price' => 35.00,
                'rating' => 4.8,
                'sales_count' => 1800,
                'images' => ['https://via.placeholder.com/400x400/000000/FFFFFF?text=Varsity+Jacket'],
            ],
            [
                'name' => '360 Outfit 2 in 1 Set Satin Long Sleeve Pyjamas Women Sleep wear Silk Pajamas',
                'description' => 'Comfortable satin pajama set with long sleeves, available in multiple colors',
                'price' => 24.89,
                'cost_price' => 15.00,
                'markup_price' => 24.89,
                'suggest_price' => 22.00,
                'rating' => 4.7,
                'sales_count' => 21900,
                'images' => ['https://via.placeholder.com/400x400/FFB6C1/FFFFFF?text=Satin+Pajamas'],
            ],
            [
                'name' => 'Hoodie Cetakan Kucing, Saiz Besar Wanita, Sangat Comel, Zip, Longgar',
                'description' => 'Cute cat print hoodie with zip, loose fit, perfect for casual wear',
                'price' => 20.90,
                'cost_price' => 12.00,
                'markup_price' => 20.90,
                'suggest_price' => 18.00,
                'rating' => 4.8,
                'sales_count' => 131,
                'images' => ['https://via.placeholder.com/400x400/87CEEB/FFFFFF?text=Cat+Hoodie'],
            ],
            [
                'name' => 'Seluar Kargo Wanita Wide Leg Hitam Model Baggy Panjang Penuh elastic waist',
                'description' => 'Black wide leg cargo pants with elastic waist, comfortable and stylish',
                'price' => 16.49,
                'cost_price' => 10.00,
                'markup_price' => 16.49,
                'suggest_price' => 15.00,
                'rating' => 4.7,
                'sales_count' => 1900,
                'images' => ['https://via.placeholder.com/400x400/2F4F4F/FFFFFF?text=Cargo+Pants'],
            ],
            [
                'name' => 'A&R Women Jegging Skinnyfit premium quality Denim jegging ready stock',
                'description' => 'Premium quality denim jeggings with stretch, perfect fit for women',
                'price' => 13.85,
                'cost_price' => 8.00,
                'markup_price' => 13.85,
                'suggest_price' => 12.00,
                'rating' => 4.6,
                'sales_count' => 8700,
                'images' => ['https://via.placeholder.com/400x400/4169E1/FFFFFF?text=Denim+Jeggings'],
            ],
            [
                'name' => 'Women\'s Floral Summer Dress Casual A-Line Midi Dress',
                'description' => 'Beautiful floral summer dress, A-line cut, perfect for casual occasions',
                'price' => 28.90,
                'cost_price' => 18.00,
                'markup_price' => 28.90,
                'suggest_price' => 25.00,
                'rating' => 4.5,
                'sales_count' => 3200,
                'images' => ['https://via.placeholder.com/400x400/FF69B4/FFFFFF?text=Floral+Dress'],
            ],
            [
                'name' => 'Women\'s Basic Cotton T-Shirt Round Neck Short Sleeve',
                'description' => 'Comfortable basic cotton t-shirt, round neck, short sleeve',
                'price' => 12.50,
                'cost_price' => 7.00,
                'markup_price' => 12.50,
                'suggest_price' => 11.00,
                'rating' => 4.4,
                'sales_count' => 5600,
                'images' => ['https://via.placeholder.com/400x400/F0F8FF/000000?text=Cotton+T-Shirt'],
            ],
            [
                'name' => 'Women\'s High Waist Skinny Jeans Stretch Denim',
                'description' => 'High waist skinny jeans with stretch denim, comfortable fit',
                'price' => 35.00,
                'cost_price' => 22.00,
                'markup_price' => 35.00,
                'suggest_price' => 32.00,
                'rating' => 4.6,
                'sales_count' => 4200,
                'images' => ['https://via.placeholder.com/400x400/000080/FFFFFF?text=Skinny+Jeans'],
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name']],
                array_merge($productData, [
                    'sku' => 'WM-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'category_id' => $womenswearCategory->id,
                    'merchant_id' => $merchant->id,
                    'status' => 'active',
                    'stock' => rand(10, 100),
                    'weight' => rand(200, 800),
                    'specifications' => json_encode([
                        'Material' => 'Cotton/Polyester',
                        'Care Instructions' => 'Machine wash cold',
                        'Size' => 'S, M, L, XL',
                    ]),
                ])
            );
        }

        $this->command->info('Womenswear products created successfully!');
    }
}
