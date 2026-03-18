<?php

namespace App\Livewire\Admin\Shipping;

use App\Models\DhlConfiguration;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class DhlSettings extends Component
{
    public string $testMode = '1';

    public string $markupPercentage = '15';

    public string $maxWeightKg = '70';

    public string $originName = '';

    public string $originStreet = '';

    public string $originCity = '';

    public string $originPostal = '';

    public string $originCountry = 'NG';

    public function mount(): void
    {
        $this->testMode = DhlConfiguration::get('test_mode', true) ? '1' : '0';
        $this->markupPercentage = (string) DhlConfiguration::get('markup_percentage', 15);
        $this->maxWeightKg = (string) DhlConfiguration::get('max_weight_kg', 70);
        $this->originName = DhlConfiguration::get('origin_name', config('services.dhl.origin.name', ''));
        $this->originStreet = DhlConfiguration::get('origin_street', config('services.dhl.origin.address_line', ''));
        $this->originCity = DhlConfiguration::get('origin_city', config('services.dhl.origin.city', ''));
        $this->originPostal = DhlConfiguration::get('origin_postal', config('services.dhl.origin.postal_code', ''));
        $this->originCountry = DhlConfiguration::get('origin_country', config('services.dhl.origin.country_code', 'NG'));
    }

    public function save(): void
    {
        $this->validate([
            'markupPercentage' => 'required|numeric|min:0|max:200',
            'maxWeightKg' => 'required|numeric|min:0.1|max:999',
            'originName' => 'required|string|max:100',
            'originStreet' => 'required|string|max:200',
            'originCity' => 'required|string|max:100',
            'originPostal' => 'required|string|max:20',
            'originCountry' => 'required|string|size:2',
        ]);

        $settings = [
            'test_mode' => $this->testMode === '1',
            'markup_percentage' => (float) $this->markupPercentage,
            'max_weight_kg' => (float) $this->maxWeightKg,
            'origin_name' => $this->originName,
            'origin_street' => $this->originStreet,
            'origin_city' => $this->originCity,
            'origin_postal' => $this->originPostal,
            'origin_country' => strtoupper($this->originCountry),
        ];

        foreach ($settings as $key => $value) {
            DhlConfiguration::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
        }

        Cache::forget('dhl_config');
        session()->flash('success', 'DHL settings saved.');
    }

    public function render(): View
    {
        return view('livewire.admin.shipping.dhl-settings');
    }
}
