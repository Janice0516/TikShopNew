<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $merchants = User::where('type', 'merchant')->get();
        
        if ($categories->isEmpty() || $merchants->isEmpty()) {
            echo "No categories or merchants found. Please run CategorySeeder and MerchantSeeder first.\n";
            return;
        }

        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => '最新款iPhone，配备A17 Pro芯片，钛金属设计',
                'sku' => 'IPH15PM-256',
                'price' => 4500.00,
                'cost_price' => 3800.00,
                'stock' => 50,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/007AFF/FFFFFF?text=iPhone+15+Pro+Max'],
                'variants' => [
                    ['name' => '颜色', 'options' => ['深空黑色', '白色', '蓝色', '钛金属色']],
                    ['name' => '存储', 'options' => ['256GB', '512GB', '1TB']]
                ],
                'weight' => 0.22,
                'brand' => 'Apple',
                'specifications' => [
                    '屏幕尺寸' => '6.7英寸',
                    '处理器' => 'A17 Pro',
                    '摄像头' => '4800万像素主摄',
                    '电池' => '4422mAh'
                ],
                'is_featured' => true,
                'sort_order' => 1,
                'views_count' => 1250,
                'sales_count' => 45,
                'rating' => 4.8
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => '三星旗舰手机，配备S Pen，AI摄影功能',
                'sku' => 'SGS24U-512',
                'price' => 4200.00,
                'cost_price' => 3500.00,
                'stock' => 30,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/000000/FFFFFF?text=Galaxy+S24+Ultra'],
                'variants' => [
                    ['name' => '颜色', 'options' => ['钛黑色', '钛灰色', '钛紫色', '钛黄色']],
                    ['name' => '存储', 'options' => ['256GB', '512GB', '1TB']]
                ],
                'weight' => 0.23,
                'brand' => 'Samsung',
                'specifications' => [
                    '屏幕尺寸' => '6.8英寸',
                    '处理器' => 'Snapdragon 8 Gen 3',
                    '摄像头' => '200MP主摄',
                    '电池' => '5000mAh'
                ],
                'is_featured' => true,
                'sort_order' => 2,
                'views_count' => 980,
                'sales_count' => 32,
                'rating' => 4.7
            ],
            [
                'name' => 'MacBook Pro 16" M3 Max',
                'description' => '专业级笔记本电脑，M3 Max芯片，Liquid Retina XDR显示屏',
                'sku' => 'MBP16-M3MAX',
                'price' => 8500.00,
                'cost_price' => 7200.00,
                'stock' => 15,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/000000/FFFFFF?text=MacBook+Pro+16'],
                'variants' => [
                    ['name' => '颜色', 'options' => ['深空灰色', '银色']],
                    ['name' => '存储', 'options' => ['1TB', '2TB', '4TB', '8TB']],
                    ['name' => '内存', 'options' => ['36GB', '48GB', '64GB', '128GB']]
                ],
                'weight' => 2.16,
                'brand' => 'Apple',
                'specifications' => [
                    '屏幕尺寸' => '16.2英寸',
                    '处理器' => 'M3 Max',
                    '内存' => '36GB统一内存',
                    '存储' => '1TB SSD'
                ],
                'is_featured' => true,
                'sort_order' => 3,
                'views_count' => 750,
                'sales_count' => 18,
                'rating' => 4.9
            ],
            [
                'name' => 'Dell XPS 15',
                'description' => '高端商务笔记本电脑，4K OLED显示屏',
                'sku' => 'DXP15-OLED',
                'price' => 3200.00,
                'cost_price' => 2600.00,
                'stock' => 25,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/0078D4/FFFFFF?text=Dell+XPS+15'],
                'variants' => [
                    ['name' => '颜色', 'options' => ['铂金银', '深空灰']],
                    ['name' => '存储', 'options' => ['512GB', '1TB', '2TB']],
                    ['name' => '内存', 'options' => ['16GB', '32GB', '64GB']]
                ],
                'weight' => 1.8,
                'brand' => 'Dell',
                'specifications' => [
                    '屏幕尺寸' => '15.6英寸',
                    '处理器' => 'Intel Core i7-13700H',
                    '显卡' => 'NVIDIA GeForce RTX 4060',
                    '电池' => '86Wh'
                ],
                'is_featured' => false,
                'sort_order' => 4,
                'views_count' => 420,
                'sales_count' => 12,
                'rating' => 4.5
            ],
            [
                'name' => 'AirPods Pro 2',
                'description' => '主动降噪无线耳机，H2芯片，空间音频',
                'sku' => 'APP2-USB-C',
                'price' => 450.00,
                'cost_price' => 320.00,
                'stock' => 100,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/FFFFFF/000000?text=AirPods+Pro+2'],
                'variants' => [
                    ['name' => '颜色', 'options' => ['白色']],
                    ['name' => '充电盒', 'options' => ['Lightning', 'USB-C']]
                ],
                'weight' => 0.056,
                'brand' => 'Apple',
                'specifications' => [
                    '驱动单元' => '11mm动圈',
                    '芯片' => 'H2',
                    '电池续航' => '6小时（开启降噪）',
                    '充电盒' => 'USB-C'
                ],
                'is_featured' => true,
                'sort_order' => 5,
                'views_count' => 2100,
                'sales_count' => 156,
                'rating' => 4.6
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => '索尼旗舰降噪耳机，30小时续航',
                'sku' => 'SWH1000XM5',
                'price' => 380.00,
                'cost_price' => 280.00,
                'stock' => 80,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/000000/FFFFFF?text=Sony+WH-1000XM5'],
                'variants' => [
                    ['name' => '颜色', 'options' => ['黑色', '银色']]
                ],
                'weight' => 0.25,
                'brand' => 'Sony',
                'specifications' => [
                    '驱动单元' => '30mm动圈',
                    '芯片' => 'V1',
                    '电池续航' => '30小时',
                    '降噪' => '业界领先'
                ],
                'is_featured' => false,
                'sort_order' => 6,
                'views_count' => 890,
                'sales_count' => 67,
                'rating' => 4.7
            ],
            [
                'name' => 'iPad Pro 12.9" M2',
                'description' => '专业平板电脑，M2芯片，Liquid Retina XDR显示屏',
                'sku' => 'IPP129-M2',
                'price' => 2800.00,
                'cost_price' => 2200.00,
                'stock' => 40,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/007AFF/FFFFFF?text=iPad+Pro+12.9'],
                'variants' => [
                    ['name' => '颜色', 'options' => ['深空灰色', '银色']],
                    ['name' => '存储', 'options' => ['128GB', '256GB', '512GB', '1TB', '2TB']],
                    ['name' => '网络', 'options' => ['Wi-Fi', 'Wi-Fi + 蜂窝网络']]
                ],
                'weight' => 0.68,
                'brand' => 'Apple',
                'specifications' => [
                    '屏幕尺寸' => '12.9英寸',
                    '处理器' => 'M2',
                    '摄像头' => '1200万像素',
                    '电池' => '40.88Wh'
                ],
                'is_featured' => true,
                'sort_order' => 7,
                'views_count' => 650,
                'sales_count' => 28,
                'rating' => 4.8
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 Ultra',
                'description' => '三星旗舰平板，S Pen支持，14.6英寸大屏',
                'sku' => 'SGTS9U-512',
                'price' => 2200.00,
                'cost_price' => 1800.00,
                'stock' => 20,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/000000/FFFFFF?text=Galaxy+Tab+S9+Ultra'],
                'variants' => [
                    ['name' => '颜色', 'options' => ['钛黑色', '钛灰色']],
                    ['name' => '存储', 'options' => ['256GB', '512GB', '1TB']],
                    ['name' => '网络', 'options' => ['Wi-Fi', 'Wi-Fi + 5G']]
                ],
                'weight' => 0.73,
                'brand' => 'Samsung',
                'specifications' => [
                    '屏幕尺寸' => '14.6英寸',
                    '处理器' => 'Snapdragon 8 Gen 2',
                    '摄像头' => '1300万像素',
                    '电池' => '11200mAh'
                ],
                'is_featured' => false,
                'sort_order' => 8,
                'views_count' => 380,
                'sales_count' => 15,
                'rating' => 4.6
            ],
            [
                'name' => 'Apple Watch Series 9',
                'description' => '最新款智能手表，S9芯片，健康监测',
                'sku' => 'AWS9-45MM',
                'price' => 650.00,
                'cost_price' => 480.00,
                'stock' => 60,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/000000/FFFFFF?text=Apple+Watch+9'],
                'variants' => [
                    ['name' => '尺寸', 'options' => ['41mm', '45mm']],
                    ['name' => '颜色', 'options' => ['午夜色', '星光色', '粉色', '蓝色', '红色']],
                    ['name' => '表带', 'options' => ['运动表带', '编织表带', '皮革表带']]
                ],
                'weight' => 0.039,
                'brand' => 'Apple',
                'specifications' => [
                    '屏幕尺寸' => '45mm',
                    '芯片' => 'S9',
                    '电池续航' => '18小时',
                    '防水' => '50米'
                ],
                'is_featured' => true,
                'sort_order' => 9,
                'views_count' => 1800,
                'sales_count' => 89,
                'rating' => 4.7
            ],
            [
                'name' => 'Samsung Galaxy Watch 6 Classic',
                'description' => '三星智能手表，旋转表圈，健康监测',
                'sku' => 'SGWC6-47MM',
                'price' => 480.00,
                'cost_price' => 350.00,
                'stock' => 45,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/400x400/000000/FFFFFF?text=Galaxy+Watch+6'],
                'variants' => [
                    ['name' => '尺寸', 'options' => ['43mm', '47mm']],
                    ['name' => '颜色', 'options' => ['黑色', '银色']],
                    ['name' => '表带', 'options' => ['硅胶表带', '皮革表带', '金属表带']]
                ],
                'weight' => 0.059,
                'brand' => 'Samsung',
                'specifications' => [
                    '屏幕尺寸' => '47mm',
                    '芯片' => 'Exynos W930',
                    '电池续航' => '40小时',
                    '防水' => '5ATM'
                ],
                'is_featured' => false,
                'sort_order' => 10,
                'views_count' => 720,
                'sales_count' => 34,
                'rating' => 4.5
            ]
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'sku' => $productData['sku'],
                'price' => $productData['price'],
                'cost_price' => $productData['cost_price'],
                'stock' => $productData['stock'],
                'status' => $productData['status'],
                'images' => $productData['images'],
                'variants' => $productData['variants'],
                'category_id' => $categories->random()->id,
                'merchant_id' => $merchants->random()->id,
                'weight' => $productData['weight'],
                'brand' => $productData['brand'],
                'specifications' => $productData['specifications'],
                'is_featured' => $productData['is_featured'],
                'sort_order' => $productData['sort_order'],
                'views_count' => $productData['views_count'],
                'sales_count' => $productData['sales_count'],
                'rating' => $productData['rating'],
            ]);
            
            echo "Created product: {$product->name}\n";
        }
        
        echo "ProductSeeder completed successfully!\n";
    }
}