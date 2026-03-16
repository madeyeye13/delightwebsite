<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Lace Fabrics',    'slug' => 'lace',      'description' => 'Premium lace fabrics including French lace, Swiss voile, and bridal lace.', 'sort_order' => 1],
            ['name' => 'Aso Oke',         'slug' => 'aso-oke',   'description' => 'Handwoven traditional Aso Oke sets for ceremonies and special occasions.', 'sort_order' => 2],
            ['name' => 'Ankara & Prints', 'slug' => 'ankara',    'description' => 'Bold, vibrant Ankara and African print fabrics for everyday and formal wear.', 'sort_order' => 3],
            ['name' => 'Cap Materials',   'slug' => 'caps',      'description' => 'Quality fabrics and trims for cap making.', 'sort_order' => 4],
            ['name' => 'Headties',        'slug' => 'headties',  'description' => 'Gele headties, sego, and related accessories.', 'sort_order' => 5],
            ['name' => 'Senator',         'slug' => 'senator',   'description' => 'Classic senator plain materials for men.', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true])
            );
        }
    }
}
