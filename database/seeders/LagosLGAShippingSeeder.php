<?php

namespace Database\Seeders;

use App\Models\NigerianCityShipping;
use App\Models\StateShipping;
use Illuminate\Database\Seeder;

class LagosLGAShippingSeeder extends Seeder
{
    public function run(): void
    {
        $lagos = StateShipping::where('state_code', 'LA')->first();

        if (! $lagos) {
            $this->command->warn('Lagos state not found - run NigerianStateShippingSeeder first.');

            return;
        }

        $cities = [
            ['name' => 'Lagos Island',   'fee' => 3000, 'days' => 2],
            ['name' => 'Ebute Ero',      'fee' => 3000, 'days' => 2],
            ['name' => 'Oworoshoki',     'fee' => 3000, 'days' => 2],
            ['name' => 'Ebute Metta',    'fee' => 3000, 'days' => 2],
            ['name' => 'Marina',         'fee' => 3000, 'days' => 2],
            ['name' => 'Idumota',        'fee' => 3000, 'days' => 2],
            ['name' => 'CMS',            'fee' => 3000, 'days' => 2],
            ['name' => 'Ikoyi',          'fee' => 3000, 'days' => 2],
            ['name' => 'Ikeja',          'fee' => 3000, 'days' => 2],
            ['name' => 'Magodo',         'fee' => 3000, 'days' => 2],
            ['name' => 'Mile 12',        'fee' => 3000, 'days' => 2],
            ['name' => 'Sangotedo',      'fee' => 5000, 'days' => 2],
            ['name' => 'Awoyaya',        'fee' => 5000, 'days' => 2],
            ['name' => 'Abijo',          'fee' => 5000, 'days' => 2],
            ['name' => 'Alakuko',        'fee' => 4000, 'days' => 3],
            ['name' => 'Meiran',         'fee' => 4000, 'days' => 3],
            ['name' => 'Ijaiye/Ojokoro', 'fee' => 4000, 'days' => 3],
            ['name' => 'Abule Egba',     'fee' => 4000, 'days' => 3],
            ['name' => 'Moshalashi',     'fee' => 3500, 'days' => 2],
            ['name' => 'Berger/Opic',    'fee' => 3500, 'days' => 2],
            ['name' => 'Victoria Island', 'fee' => 3500, 'days' => 2],
            ['name' => 'Onipan',         'fee' => 2000, 'days' => 1],
            ['name' => 'Ajah',           'fee' => 4500, 'days' => 2],
            ['name' => 'Lekki Phase 1&2', 'fee' => 4000, 'days' => 2],
            ['name' => 'Lekki',          'fee' => 4000, 'days' => 2],
            ['name' => 'Ketu',           'fee' => 3000, 'days' => 2],
            ['name' => 'Ojota',          'fee' => 3000, 'days' => 2],
            // Additional Lagos cities
            ['name' => 'Surulere',       'fee' => 2500, 'days' => 1],
            ['name' => 'Yaba',           'fee' => 2500, 'days' => 1],
            ['name' => 'Apapa',          'fee' => 3000, 'days' => 2],
            ['name' => 'Oshodi',         'fee' => 2500, 'days' => 1],
            ['name' => 'Agege',          'fee' => 3000, 'days' => 2],
            ['name' => 'Badagry',        'fee' => 5000, 'days' => 3],
            ['name' => 'Epe',            'fee' => 5000, 'days' => 3],
            ['name' => 'Ikorodu',        'fee' => 4000, 'days' => 2],
            ['name' => 'Mushin',         'fee' => 2500, 'days' => 1],
            ['name' => 'Ojo',            'fee' => 3500, 'days' => 2],
            ['name' => 'Ajegunle',       'fee' => 3000, 'days' => 2],
            ['name' => 'Maryland',       'fee' => 2500, 'days' => 1],
            ['name' => 'Gbagada',        'fee' => 2500, 'days' => 1],
            ['name' => 'Anthony',        'fee' => 2500, 'days' => 1],
            ['name' => 'Palmgroove',     'fee' => 2500, 'days' => 1],
        ];

        foreach ($cities as $city) {
            NigerianCityShipping::updateOrCreate(
                [
                    'state_shipping_id' => $lagos->id,
                    'city_name' => $city['name'],
                ],
                [
                    'shipping_fee' => $city['fee'],
                    'currency' => 'NGN',
                    'estimated_days' => $city['days'],
                    'is_active' => true,
                    // Weight tiers null = inherit from state
                    'tier_1_limit' => null,
                    'tier_2_limit' => null,
                    'tier_2_surcharge' => null,
                    'tier_3_limit' => null,
                    'tier_3_surcharge' => null,
                    'contact_for_heavy' => null,
                ]
            );
        }
    }
}
