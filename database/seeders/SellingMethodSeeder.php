<?php

namespace Database\Seeders;

use App\Models\SellingMethod;
use Illuminate\Database\Seeder;

class SellingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Per Piece',  'slug' => 'per-piece',  'description' => 'Customers buy individual pieces',                     'config_type' => 'per_piece',  'is_system' => true],
            ['name' => 'Per Set',    'slug' => 'per-set',    'description' => 'Customers buy pre-made sets (e.g. 2 or 3-piece sets)', 'config_type' => 'per_set',    'is_system' => true],
            ['name' => 'Per Bundle', 'slug' => 'per-bundle', 'description' => 'Bundle of multiple items with defined yield',          'config_type' => 'per_bundle', 'is_system' => true],
            ['name' => 'Per Length', 'slug' => 'per-length', 'description' => 'Fabric sold by yards or meters',                       'config_type' => 'per_length', 'is_system' => true],
            ['name' => 'Per Loom',   'slug' => 'per-loom',   'description' => 'Sold by loom measurement (e.g. 45-yard loom)',         'config_type' => 'per_loom',   'is_system' => true],
        ];

        foreach ($methods as $method) {
            SellingMethod::firstOrCreate(
                ['slug' => $method['slug']],
                array_merge($method, ['is_active' => true])
            );
        }
    }
}
