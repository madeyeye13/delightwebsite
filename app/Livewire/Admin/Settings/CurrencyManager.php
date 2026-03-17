<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class CurrencyManager extends Component
{
    // ── Edit existing ─────────────────────────────────────────────────────────
    public string $editingCode = '';

    public float $editingRate = 0.0;

    public float $editingMarkup = 0.0;

    public bool $editingActive = true;

    // ── Create new ────────────────────────────────────────────────────────────
    public bool $showCreateForm = false;

    public string $newCode = '';

    public string $newName = '';

    public string $newSymbol = '';

    public float $newMarkup = 0.0;

    public bool $newIsActive = true;

    // ── Edit actions ──────────────────────────────────────────────────────────

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
            'editingMarkup' => ['required', 'numeric', 'min:0'],
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

    // ── Create actions ────────────────────────────────────────────────────────

    public function createCurrency(): void
    {
        $this->validate([
            'newCode' => ['required', 'string', 'min:2', 'max:6', 'regex:/^[A-Za-z]+$/', Rule::unique('currencies', 'code')],
            'newName' => ['required', 'string', 'min:2', 'max:100'],
            'newSymbol' => ['required', 'string', 'min:1', 'max:10'],
            'newMarkup' => ['required', 'numeric', 'min:0'],
        ]);

        $currency = Currency::create([
            'code' => strtoupper($this->newCode),
            'name' => $this->newName,
            'symbol' => $this->newSymbol,
            'markup' => $this->newMarkup,
            'is_active' => $this->newIsActive,
            'is_default' => false,
        ]);

        // Fetch the live rate immediately so the currency is usable right away.
        app(CurrencyService::class)->updateExchangeRates();
        app(CurrencyService::class)->clearCache();

        $this->reset(['newCode', 'newName', 'newSymbol', 'newMarkup']);
        $this->newIsActive = true;
        $this->showCreateForm = false;

        $this->dispatch('toast', type: 'success', message: "{$currency->code} added and exchange rate fetched.");
    }

    public function deleteCurrency(int $id): void
    {
        $currency = Currency::findOrFail($id);

        if ($currency->is_default || $currency->code === CurrencyService::BASE_CURRENCY) {
            $this->dispatch('toast', type: 'error', message: 'The base/default currency cannot be deleted.');

            return;
        }

        $currency->exchangeRates()->delete();
        $currency->delete();

        app(CurrencyService::class)->clearCache();

        $this->dispatch('toast', type: 'success', message: "{$currency->code} deleted.");
    }

    public function refreshRates(): void
    {
        app(CurrencyService::class)->updateExchangeRates();
        $this->dispatch('toast', type: 'success', message: 'Exchange rates refreshed from API.');
    }

    public function render(): View
    {
        $currencies = Currency::with('latestRate')->orderByDesc('is_default')->orderBy('code')->get();

        return view('livewire.admin.settings.currency-manager', compact('currencies'));
    }
}
