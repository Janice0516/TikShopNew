<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 找到女装内衣分类
        $womenswearCategory = Category::where('name', 'like', '%女装%')->orWhere('name', 'like', '%Womenswear%')->first();
        
        if (!$womenswearCategory) {
            $this->command->warn('Womenswear category not found. Creating it first...');
            $womenswearCategory = Category::create([
                'name' => 'Womenswear & Underwear',
                'slug' => 'womenswear-underwear',
                'description' => 'Women\'s clothing and underwear',
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        // 女装内衣子分类
        $subcategories = [
            [
                'name' => 'Women\'s Underwear',
                'slug' => 'womens-underwear',
                'description' => 'Women\'s underwear and lingerie',
                'parent_id' => $womenswearCategory->id,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Women\'s Suits & Sets',
                'slug' => 'womens-suits-sets',
                'description' => 'Women\'s suits and matching sets',
                'parent_id' => $womenswearCategory->id,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Women\'s Dresses',
                'slug' => 'womens-dresses',
                'description' => 'Women\'s dresses and gowns',
                'parent_id' => $womenswearCategory->id,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Women\'s Tops',
                'slug' => 'womens-tops',
                'description' => 'Women\'s tops, shirts, and blouses',
                'parent_id' => $womenswearCategory->id,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Women\'s Sleepwear & Loungewear',
                'slug' => 'womens-sleepwear-loungewear',
                'description' => 'Women\'s sleepwear and loungewear',
                'parent_id' => $womenswearCategory->id,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Women\'s Bottoms',
                'slug' => 'womens-bottoms',
                'description' => 'Women\'s pants, jeans, and skirts',
                'parent_id' => $womenswearCategory->id,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Women\'s Special Occasion',
                'slug' => 'womens-special-occasion',
                'description' => 'Women\'s special occasion clothing',
                'parent_id' => $womenswearCategory->id,
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($subcategories as $subcategoryData) {
            Category::firstOrCreate(
                ['slug' => $subcategoryData['slug']],
                $subcategoryData
            );
        }

        $this->command->info('Subcategories created successfully!');
    }
}
