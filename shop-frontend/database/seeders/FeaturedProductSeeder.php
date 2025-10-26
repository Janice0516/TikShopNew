<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\FeaturedProduct;

class FeaturedProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 获取一些商品
        $products = Product::where('status', 'active')->take(10)->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        // 清空现有特色商品
        FeaturedProduct::truncate();

        // 添加Savings for you商品
        $savingsProducts = $products->take(4);
        foreach ($savingsProducts as $index => $product) {
            FeaturedProduct::create([
                'product_id' => $product->id,
                'type' => 'savings',
                'sort_order' => $index + 1,
                'custom_title' => null, // 使用原商品名称
                'custom_price' => null, // 使用原价格
                'custom_original_price' => null, // 使用原原价
                'custom_rating' => null, // 使用原评分
                'custom_sales_count' => null, // 使用原销量
                'is_active' => true,
            ]);
        }

        // 添加Top deals for you商品
        $topDealsProducts = $products->skip(4)->take(2);
        foreach ($topDealsProducts as $index => $product) {
            FeaturedProduct::create([
                'product_id' => $product->id,
                'type' => 'top_deals',
                'sort_order' => $index + 1,
                'custom_title' => null, // 使用原商品名称
                'custom_price' => null, // 使用原价格
                'custom_original_price' => null, // 使用原原价
                'custom_rating' => null, // 使用原评分
                'custom_sales_count' => null, // 使用原销量
                'is_active' => true,
            ]);
        }

        $this->command->info('Featured products seeded successfully!');
    }
}
