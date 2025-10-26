<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PlatformProduct;

class PlatformProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // 电子产品
            [
                'name' => 'Apple AirPods Pro 2',
                'description' => '主动降噪无线耳机，空间音频，自适应透明模式，最长6小时聆听时间',
                'price' => 249.99,
                'cost_price' => 120.00,
                'image' => 'https://via.placeholder.com/300x200?text=AirPods+Pro+2',
                'category' => '电子产品',
                'brand' => 'Apple',
                'rating' => 4.8,
                'sales' => 15420,
                'stock' => 500,
                'sku' => 'APPLE-AIRPODS-PRO-2',
                'specifications' => [
                    '连接方式' => '蓝牙5.3',
                    '电池续航' => '最长6小时',
                    '充电盒续航' => '最长30小时',
                    '防水等级' => 'IPX4',
                    '重量' => '5.3g'
                ],
                'images' => [
                    'https://via.placeholder.com/300x200?text=AirPods+Pro+2+1',
                    'https://via.placeholder.com/300x200?text=AirPods+Pro+2+2',
                    'https://via.placeholder.com/300x200?text=AirPods+Pro+2+3'
                ],
            ],
            [
                'name' => 'Samsung Galaxy Watch 6',
                'description' => '智能手表，健康监测，运动追踪，睡眠分析，GPS定位',
                'price' => 299.99,
                'cost_price' => 150.00,
                'image' => 'https://via.placeholder.com/300x200?text=Galaxy+Watch+6',
                'category' => '电子产品',
                'brand' => 'Samsung',
                'rating' => 4.6,
                'sales' => 8920,
                'stock' => 300,
                'sku' => 'SAMSUNG-GALAXY-WATCH-6',
                'specifications' => [
                    '屏幕尺寸' => '1.4英寸',
                    '电池续航' => '最长40小时',
                    '防水等级' => '5ATM',
                    '连接方式' => '蓝牙/WiFi',
                    '重量' => '28.7g'
                ],
            ],
            [
                'name' => 'Anker PowerCore 20000',
                'description' => '20000mAh大容量充电宝，支持快充，双USB输出，LED指示灯',
                'price' => 59.99,
                'cost_price' => 25.00,
                'image' => 'https://via.placeholder.com/300x200?text=PowerCore+20000',
                'category' => '电子产品',
                'brand' => 'Anker',
                'rating' => 4.7,
                'sales' => 25680,
                'stock' => 800,
                'sku' => 'ANKER-POWERCORE-20000',
                'specifications' => [
                    '容量' => '20000mAh',
                    '输出功率' => '18W',
                    '接口' => '2×USB-A',
                    '重量' => '345g',
                    '尺寸' => '158×75×20mm'
                ],
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => '无线降噪耳机，30小时续航，快速充电，多点连接',
                'price' => 399.99,
                'cost_price' => 200.00,
                'image' => 'https://via.placeholder.com/300x200?text=WH-1000XM5',
                'category' => '电子产品',
                'brand' => 'Sony',
                'rating' => 4.9,
                'sales' => 12350,
                'stock' => 200,
                'sku' => 'SONY-WH-1000XM5',
                'specifications' => [
                    '降噪技术' => 'HD降噪处理器QN1',
                    '电池续航' => '30小时',
                    '快充' => '3分钟充电3小时',
                    '重量' => '250g',
                    '连接' => '蓝牙5.2'
                ],
            ],
            
            // 生活用品
            [
                'name' => 'Hydro Flask 32oz 保温杯',
                'description' => '不锈钢保温杯，24小时保温，12小时保冷，防漏设计',
                'price' => 45.99,
                'cost_price' => 18.00,
                'image' => 'https://via.placeholder.com/300x200?text=Hydro+Flask+32oz',
                'category' => '生活用品',
                'brand' => 'Hydro Flask',
                'rating' => 4.6,
                'sales' => 18920,
                'stock' => 1000,
                'sku' => 'HYDRO-FLASK-32OZ',
                'specifications' => [
                    '容量' => '32oz (946ml)',
                    '材质' => '18/8不锈钢',
                    '保温时间' => '24小时',
                    '保冷时间' => '12小时',
                    '重量' => '454g'
                ],
            ],
            [
                'name' => 'Lululemon Yoga Mat',
                'description' => '防滑瑜伽垫，环保材质，多种颜色可选，便携设计',
                'price' => 68.00,
                'cost_price' => 28.00,
                'image' => 'https://via.placeholder.com/300x200?text=Lululemon+Yoga+Mat',
                'category' => '运动健身',
                'brand' => 'Lululemon',
                'rating' => 4.5,
                'sales' => 15680,
                'stock' => 600,
                'sku' => 'LULULEMON-YOGA-MAT',
                'specifications' => [
                    '尺寸' => '71×26英寸',
                    '厚度' => '5mm',
                    '材质' => '环保TPE',
                    '重量' => '1.8kg',
                    '防滑' => '双面防滑'
                ],
            ],
            [
                'name' => 'Dyson V15 Detect 吸尘器',
                'description' => '无线吸尘器，激光探测，60分钟续航，智能调节吸力',
                'price' => 749.99,
                'cost_price' => 350.00,
                'image' => 'https://via.placeholder.com/300x200?text=Dyson+V15+Detect',
                'category' => '生活用品',
                'brand' => 'Dyson',
                'rating' => 4.8,
                'sales' => 5420,
                'stock' => 150,
                'sku' => 'DYSON-V15-DETECT',
                'specifications' => [
                    '续航时间' => '60分钟',
                    '吸力' => '230AW',
                    '过滤系统' => 'HEPA过滤',
                    '重量' => '3.0kg',
                    '充电时间' => '4.5小时'
                ],
            ],
            
            // 服装配饰
            [
                'name' => 'Nike Air Max 270',
                'description' => '运动鞋，Max Air气垫，透气网面，舒适缓震',
                'price' => 150.00,
                'cost_price' => 65.00,
                'image' => 'https://via.placeholder.com/300x200?text=Nike+Air+Max+270',
                'category' => '服装配饰',
                'brand' => 'Nike',
                'rating' => 4.4,
                'sales' => 32150,
                'stock' => 1200,
                'sku' => 'NIKE-AIR-MAX-270',
                'specifications' => [
                    '鞋面材质' => '网面+合成革',
                    '中底技术' => 'Max Air气垫',
                    '外底材质' => '橡胶',
                    '适用场景' => '日常/运动',
                    '重量' => '320g'
                ],
            ],
            [
                'name' => 'Patagonia Better Sweater',
                'description' => '羊毛混纺毛衣，保暖透气，环保材质，经典设计',
                'price' => 129.00,
                'cost_price' => 55.00,
                'image' => 'https://via.placeholder.com/300x200?text=Patagonia+Better+Sweater',
                'category' => '服装配饰',
                'brand' => 'Patagonia',
                'rating' => 4.7,
                'sales' => 8920,
                'stock' => 400,
                'sku' => 'PATAGONIA-BETTER-SWEATER',
                'specifications' => [
                    '材质' => '87%聚酯纤维+13%羊毛',
                    '保暖性' => '中等',
                    '透气性' => '良好',
                    '护理' => '机洗',
                    '产地' => '美国'
                ],
            ],
            
            // 家居用品
            [
                'name' => 'IKEA HEMNES 书柜',
                'description' => '实木书柜，5层设计，可调节隔板，北欧风格',
                'price' => 199.99,
                'cost_price' => 85.00,
                'image' => 'https://via.placeholder.com/300x200?text=IKEA+HEMNES+Bookcase',
                'category' => '家居用品',
                'brand' => 'IKEA',
                'rating' => 4.3,
                'sales' => 4560,
                'stock' => 200,
                'sku' => 'IKEA-HEMNES-BOOKCASE',
                'specifications' => [
                    '材质' => '实木+刨花板',
                    '尺寸' => '90×30×202cm',
                    '层数' => '5层',
                    '承重' => '每层25kg',
                    '颜色' => '白色/深色'
                ],
            ],
            [
                'name' => 'Philips Hue 智能灯泡套装',
                'description' => '智能LED灯泡，1600万色彩，语音控制，手机APP调节',
                'price' => 79.99,
                'cost_price' => 35.00,
                'image' => 'https://via.placeholder.com/300x200?text=Philips+Hue+Smart+Bulbs',
                'category' => '家居用品',
                'brand' => 'Philips',
                'rating' => 4.6,
                'sales' => 12890,
                'stock' => 500,
                'sku' => 'PHILIPS-HUE-SMART-BULBS',
                'specifications' => [
                    '功率' => '9W',
                    '亮度' => '800流明',
                    '色彩' => '1600万色',
                    '连接' => 'WiFi+蓝牙',
                    '寿命' => '25000小时'
                ],
            ],
        ];

        foreach ($products as $product) {
            PlatformProduct::create($product);
        }
    }
}