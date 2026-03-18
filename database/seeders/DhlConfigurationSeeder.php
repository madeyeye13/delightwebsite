<?php

namespace Database\Seeders;

use App\Models\DhlConfiguration;
use Illuminate\Database\Seeder;

class DhlConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['key' => 'markup_percentage',   'value' => '15',  'type' => 'float',   'label' => 'DHL Markup Percentage (%)'],
            ['key' => 'account_active',      'value' => '0',   'type' => 'boolean', 'label' => 'DHL Account Active'],
            ['key' => 'test_mode',           'value' => '1',   'type' => 'boolean', 'label' => 'Use DHL Test Endpoint'],
            ['key' => 'default_length_cm',   'value' => '30',  'type' => 'integer', 'label' => 'Default Package Length (cm)'],
            ['key' => 'default_width_cm',    'value' => '30',  'type' => 'integer', 'label' => 'Default Package Width (cm)'],
            ['key' => 'default_height_cm',   'value' => '10',  'type' => 'integer', 'label' => 'Default Package Height (cm)'],
        ];

        foreach ($configs as $config) {
            DhlConfiguration::updateOrCreate(
                ['key' => $config['key']],
                ['value' => $config['value'], 'type' => $config['type'], 'label' => $config['label']]
            );
        }
    }
}
