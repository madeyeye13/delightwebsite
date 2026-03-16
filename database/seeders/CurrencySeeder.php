<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Seed currencies and their initial exchange rates (NGN as base = 1).
     * Rates are approximate — admin can update them via the settings panel.
     */
    public function run(): void
    {
        $currencies = [
            ['code' => 'NGN', 'name' => 'Nigerian Naira',     'symbol' => '₦',    'is_default' => true,  'markup' => 1.0000, 'rate' => 1.00000000],
            ['code' => 'USD', 'name' => 'US Dollar',          'symbol' => '$',    'is_default' => false, 'markup' => 1.0000, 'rate' => 0.00065000],
            ['code' => 'GBP', 'name' => 'British Pound',      'symbol' => '£',    'is_default' => false, 'markup' => 1.0000, 'rate' => 0.00051000],
            ['code' => 'EUR', 'name' => 'Euro',                'symbol' => '€',    'is_default' => false, 'markup' => 1.0000, 'rate' => 0.00060000],
            ['code' => 'CAD', 'name' => 'Canadian Dollar',    'symbol' => 'CA$',  'is_default' => false, 'markup' => 1.0000, 'rate' => 0.00088000],
            ['code' => 'GHS', 'name' => 'Ghanaian Cedi',      'symbol' => 'GH₵',  'is_default' => false, 'markup' => 1.0000, 'rate' => 0.00970000],
            ['code' => 'CFA', 'name' => 'West African Franc', 'symbol' => 'CFA',  'is_default' => false, 'markup' => 1.0000, 'rate' => 0.39300000],
        ];

        foreach ($currencies as $data) {
            $currency = Currency::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'symbol' => $data['symbol'],
                    'is_active' => true,
                    'is_default' => $data['is_default'],
                    'markup' => $data['markup'],
                ]
            );

            ExchangeRate::updateOrCreate(
                ['currency_id' => $currency->id],
                [
                    'rate' => $data['rate'],
                    'fetched_at' => now(),
                ]
            );
        }
    }
}
