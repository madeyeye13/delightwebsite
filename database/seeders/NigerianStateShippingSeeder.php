<?php

namespace Database\Seeders;

use App\Models\StateShipping;
use Illuminate\Database\Seeder;

class NigerianStateShippingSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['state_name' => 'Abia',        'state_code' => 'AB', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Adamawa',     'state_code' => 'AD', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'Akwa Ibom',   'state_code' => 'AK', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Anambra',     'state_code' => 'AN', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Bauchi',      'state_code' => 'BA', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'Bayelsa',     'state_code' => 'BY', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Benue',       'state_code' => 'BE', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Borno',       'state_code' => 'BO', 'shipping_fee' => 5000, 'estimated_days' => 6],
            ['state_name' => 'Cross River', 'state_code' => 'CR', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Delta',       'state_code' => 'DE', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Ebonyi',      'state_code' => 'EB', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Edo',         'state_code' => 'ED', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Ekiti',       'state_code' => 'EK', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Enugu',       'state_code' => 'EN', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Gombe',       'state_code' => 'GO', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'Imo',         'state_code' => 'IM', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Jigawa',      'state_code' => 'JI', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'Kaduna',      'state_code' => 'KD', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Kano',        'state_code' => 'KN', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Katsina',     'state_code' => 'KT', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'Kebbi',       'state_code' => 'KE', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'Kogi',        'state_code' => 'KO', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Kwara',       'state_code' => 'KW', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Lagos',       'state_code' => 'LA', 'shipping_fee' => 2000, 'estimated_days' => 2],
            ['state_name' => 'Nasarawa',    'state_code' => 'NA', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Niger',       'state_code' => 'NI', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Ogun',        'state_code' => 'OG', 'shipping_fee' => 2500, 'estimated_days' => 2],
            ['state_name' => 'Ondo',        'state_code' => 'ON', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Osun',        'state_code' => 'OS', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Oyo',         'state_code' => 'OY', 'shipping_fee' => 3000, 'estimated_days' => 3],
            ['state_name' => 'Plateau',     'state_code' => 'PL', 'shipping_fee' => 4000, 'estimated_days' => 4],
            ['state_name' => 'Rivers',      'state_code' => 'RI', 'shipping_fee' => 3500, 'estimated_days' => 3],
            ['state_name' => 'Sokoto',      'state_code' => 'SO', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'Taraba',      'state_code' => 'TA', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'Yobe',        'state_code' => 'YO', 'shipping_fee' => 5000, 'estimated_days' => 6],
            ['state_name' => 'Zamfara',     'state_code' => 'ZA', 'shipping_fee' => 4500, 'estimated_days' => 5],
            ['state_name' => 'FCT',         'state_code' => 'FC', 'shipping_fee' => 6000, 'estimated_days' => 3],
        ];

        foreach ($states as $state) {
            StateShipping::updateOrCreate(
                ['state_code' => $state['state_code']],
                [
                    'state_name' => $state['state_name'],
                    'shipping_fee' => $state['shipping_fee'],
                    'currency' => 'NGN',
                    'estimated_days' => $state['estimated_days'],
                    'is_active' => true,
                    'tier_1_limit' => 3.00,
                    'tier_2_limit' => 5.00,
                    'tier_2_surcharge' => 1500,
                    'tier_3_limit' => 8.00,
                    'tier_3_surcharge' => 3000,
                    'contact_for_heavy' => true,
                ]
            );
        }
    }
}
