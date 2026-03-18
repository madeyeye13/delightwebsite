<?php

namespace App\Services;

use App\Models\NigerianCityShipping;
use App\Models\StateShipping;

class CustomShippingService
{
    private const STORE_PICKUP_ID = 'store_pickup';

    private const STORE_PICKUP_ADDRESS = '30b Opebi Rd, Opebi, Lagos 100281';

    /**
     * Get available shipping options for the given address and total cart weight.
     *
     * For Nigeria: looks up per-city rates first, falls back to state rates.
     * Store pickup is always included for Nigerian orders.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOptions(string $country, string $state, string $city, float $totalWeightKg): array
    {
        if (strtoupper($country) !== 'NG') {
            return [];
        }

        $options = [];

        $delivery = $this->resolveDeliveryOption($state, $city, $totalWeightKg);
        if ($delivery) {
            $options[] = $delivery;
        }

        $options[] = $this->storePickupOption();

        return $options;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveDeliveryOption(string $state, string $city, float $weightKg): ?array
    {
        // 1. Try city-level lookup
        if (! empty($city)) {
            $cityRecord = NigerianCityShipping::with('stateShipping')
                ->where('is_active', true)
                ->whereRaw('LOWER(city_name) = ?', [strtolower(trim($city))])
                ->first();

            if ($cityRecord) {
                $result = $cityRecord->calculateCost($weightKg);

                return $this->buildDeliveryOption(
                    id: 'custom_city_'.$cityRecord->id,
                    name: 'Home Delivery',
                    locationLabel: $cityRecord->city_name.', '.$cityRecord->stateShipping->state_name,
                    estimatedDays: $cityRecord->stateShipping?->estimated_days ?? 3,
                    cost: $result['cost'],
                    contactRequired: $result['contact_required'],
                );
            }
        }

        // 2. Fall back to state-level lookup
        if (! empty($state)) {
            $stateName = $this->normalizeStateName($state);
            $stateRecord = StateShipping::where('is_active', true)
                ->where(function ($q) use ($stateName) {
                    $q->whereRaw('LOWER(state_name) = ?', [strtolower($stateName)])
                        ->orWhereRaw('LOWER(state_code) = ?', [strtolower($stateName)]);
                })
                ->first();

            if ($stateRecord) {
                $result = $stateRecord->calculateCost($weightKg);

                return $this->buildDeliveryOption(
                    id: 'custom_state_'.$stateRecord->id,
                    name: 'Home Delivery',
                    locationLabel: $stateRecord->state_name.' State',
                    estimatedDays: $stateRecord->estimated_days,
                    cost: $result['cost'],
                    contactRequired: $result['contact_required'],
                );
            }
        }

        return null;
    }

    /** @return array<string, mixed> */
    private function buildDeliveryOption(
        string $id,
        string $name,
        string $locationLabel,
        int $estimatedDays,
        float $cost,
        bool $contactRequired,
    ): array {
        $dayLabel = $estimatedDays === 1 ? '1 business day' : "{$estimatedDays} business days";

        if ($contactRequired) {
            return [
                'id' => $id,
                'name' => $name,
                'description' => "Contact us for heavy shipment quote · {$dayLabel}",
                'price' => 0,
                'badge' => 'QUOTE',
                'badgeCls' => 'text-[10px] bg-accent-100 text-accent-700 px-1.5 py-0.5 font-semibold',
                'contact_required' => true,
                'estimated_days' => $estimatedDays,
            ];
        }

        return [
            'id' => $id,
            'name' => $name,
            'description' => "{$locationLabel} · {$dayLabel}",
            'price' => $cost,
            'badge' => null,
            'badgeCls' => '',
            'contact_required' => false,
            'estimated_days' => $estimatedDays,
        ];
    }

    /** @return array<string, mixed> */
    private function storePickupOption(): array
    {
        return [
            'id' => self::STORE_PICKUP_ID,
            'name' => 'Store Pickup',
            'description' => self::STORE_PICKUP_ADDRESS,
            'price' => 0,
            'badge' => 'FREE',
            'badgeCls' => 'text-[10px] bg-brand-100 text-brand-700 px-1.5 py-0.5 font-semibold',
            'contact_required' => false,
            'estimated_days' => 0,
        ];
    }

    /**
     * Normalise state names like "FCT - Abuja" → "FCT" for DB lookup.
     * Also strips " State" suffix if present.
     */
    private function normalizeStateName(string $state): string
    {
        $state = trim($state);

        if (str_starts_with(strtolower($state), 'fct')) {
            return 'FCT';
        }

        return str_ireplace(' State', '', $state);
    }
}
