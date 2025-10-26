<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => '电子产品',
                'slug' => 'electronics',
                'description' => '各种电子设备和配件',
                'children' => [
                    ['name' => '手机', 'slug' => 'phones'],
                    ['name' => '电脑', 'slug' => 'computers'],
                    ['name' => '平板', 'slug' => 'tablets'],
                    ['name' => '耳机', 'slug' => 'headphones'],
                ]
            ],
            [
                'name' => '服装',
                'slug' => 'clothing',
                'description' => '时尚服装和配饰',
                'children' => [
                    ['name' => '男装', 'slug' => 'mens-clothing'],
                    ['name' => '女装', 'slug' => 'womens-clothing'],
                    ['name' => '童装', 'slug' => 'kids-clothing'],
                    ['name' => '鞋类', 'slug' => 'shoes'],
                ]
            ],
            [
                'name' => '家居用品',
                'slug' => 'home-garden',
                'description' => '家居装饰和生活用品',
                'children' => [
                    ['name' => '家具', 'slug' => 'furniture'],
                    ['name' => '装饰品', 'slug' => 'decorations'],
                    ['name' => '厨房用品', 'slug' => 'kitchen'],
                    ['name' => '清洁用品', 'slug' => 'cleaning'],
                ]
            ],
            [
                'name' => '美妆护肤',
                'slug' => 'beauty',
                'description' => '化妆品和护肤品',
                'children' => [
                    ['name' => '化妆品', 'slug' => 'makeup'],
                    ['name' => '护肤品', 'slug' => 'skincare'],
                    ['name' => '香水', 'slug' => 'perfume'],
                    ['name' => '美发用品', 'slug' => 'hair-care'],
                ]
            ],
            [
                'name' => '运动户外',
                'slug' => 'sports',
                'description' => '运动装备和户外用品',
                'children' => [
                    ['name' => '运动服装', 'slug' => 'sportswear'],
                    ['name' => '运动器材', 'slug' => 'equipment'],
                    ['name' => '户外装备', 'slug' => 'outdoor'],
                    ['name' => '健身器材', 'slug' => 'fitness'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'description' => $categoryData['description'],
                'is_active' => true,
                'sort_order' => 0,
            ]);

            foreach ($children as $childData) {
                Category::create([
                    'name' => $childData['name'],
                    'slug' => $childData['slug'],
                    'parent_id' => $category->id,
                    'is_active' => true,
                    'sort_order' => 0,
                ]);
            }
        }
    }
}
