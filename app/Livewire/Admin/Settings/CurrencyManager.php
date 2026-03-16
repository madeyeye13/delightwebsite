<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\View\View;
use Livewire\Component;

class CurrencyManager extends Component
{
    public string $editingCode = '';

    public float $editingRate = 0.0;

    public float $editingMarkup = 1.0;

    public bool $editingActive = true;

    public function editCurrency(int $id): void
    {
        $currency = Currency::with('latestRate')->findOrFail($id);
        $this->editingCode = $currency->code;
        $this->editingRate = (float) ($currency->latestRate?->rate ?? 0.0);
        $this->editingMarkup = (float) $currency->markup;
        $this->editingActive = (bool) $currency->is_active;
    }

    public function saveRate(int $id): void
    {
        $this->validate([
            'editingRate' => ['required', 'numeric', 'min:0'],
            'editingMarkup' => ['required', 'numeric', 'min:0.01'],
        ]);

        $currency = Currency::findOrFail($id);

        ExchangeRate::updateOrCreate(
            ['currency_id' => $currency->id],
            ['rate' => $this->editingRate, 'fetched_at' => now()]
        );

        $currency->update([
            'markup' => $this->editingMarkup,
            'is_active' => $this->editingActive,
        ]);

        app(CurrencyService::class)->clearCache();

        $this->editingCode = '';
        $this->dispatch('toast', type: 'success', message: "Rate for {$currency->code} updated.");
    }

    public function setDefault(int $id): void
    {
        Currency::query()->update(['is_default' => false]);
        Currency::findOrFail($id)->update(['is_default' => true]);

        app(CurrencyService::class)->clearCache();

        $this->dispatch('toast', type: 'success', message: 'Default currency updated.');
    }

    public function render(): View
    {
        $currencies = Currency::with('latestRate')->orderByDesc('is_default')->orderBy('code')->get();

        return view('livewire.admin.settings.currency-manager', compact('currencies'));
    }
}
