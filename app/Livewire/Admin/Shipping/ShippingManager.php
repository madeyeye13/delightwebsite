<?php

namespace App\Livewire\Admin\Shipping;

use App\Models\NigerianCityShipping;
use App\Models\StateShipping;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ShippingManager extends Component
{
    use WithPagination;

    public string $stateSearch = '';

    public string $citySearch = '';

    public string $activeTab = 'states'; // 'states' | 'cities'

    // State form
    public ?int $editingStateId = null;

    public string $stateName = '';

    public float $shippingFee = 0;

    public int $estimatedDays = 3;

    public bool $showStateForm = false;

    // City form
    public ?int $editingCityId = null;

    public string $cityName = '';

    public string $cityStateName = '';

    public float $cityShippingFee = 0;

    public int $cityDays = 2;

    public bool $showCityForm = false;

    public function updatedStateSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCitySearch(): void
    {
        $this->resetPage();
    }

    public function editState(int $id): void
    {
        $state = StateShipping::findOrFail($id);
        $this->editingStateId = $id;
        $this->stateName = $state->state_name;
        $this->shippingFee = $state->shipping_fee;
        $this->estimatedDays = $state->estimated_days;
        $this->showStateForm = true;
    }

    public function saveState(): void
    {
        $this->validate([
            'stateName' => 'required|string|max:100',
            'shippingFee' => 'required|numeric|min:0',
            'estimatedDays' => 'required|integer|min:1',
        ]);

        StateShipping::updateOrCreate(
            ['id' => $this->editingStateId],
            [
                'state_name' => $this->stateName,
                'shipping_fee' => $this->shippingFee,
                'estimated_days' => $this->estimatedDays,
            ]
        );

        $this->resetStateForm();
        session()->flash('success', 'State shipping updated.');
    }

    public function editCity(int $id): void
    {
        $city = NigerianCityShipping::with('stateShipping')->findOrFail($id);
        $this->editingCityId = $id;
        $this->cityName = $city->city_name;
        $this->cityStateName = $city->stateShipping->state_name;
        $this->cityShippingFee = $city->shipping_fee ?? 0;
        $this->cityDays = $city->estimated_days ?? 2;
        $this->showCityForm = true;
    }

    public function saveCity(): void
    {
        $this->validate([
            'cityName' => 'required|string|max:100',
            'cityStateName' => 'required|string|exists:state_shippings,state_name',
        ]);

        $stateId = StateShipping::where('state_name', $this->cityStateName)->value('id');

        NigerianCityShipping::where('id', $this->editingCityId)->update([
            'city_name' => $this->cityName,
            'state_shipping_id' => $stateId,
            'shipping_fee' => $this->cityShippingFee ?: null,
            'estimated_days' => $this->cityDays ?: null,
        ]);

        $this->resetCityForm();
        session()->flash('success', 'City shipping updated.');
    }

    public function resetStateForm(): void
    {
        $this->editingStateId = null;
        $this->stateName = '';
        $this->shippingFee = 0;
        $this->estimatedDays = 3;
        $this->showStateForm = false;
    }

    public function resetCityForm(): void
    {
        $this->editingCityId = null;
        $this->cityName = '';
        $this->cityStateName = '';
        $this->cityShippingFee = 0;
        $this->cityDays = 2;
        $this->showCityForm = false;
    }

    #[Computed]
    public function states()
    {
        return StateShipping::query()
            ->when($this->stateSearch, fn ($q) => $q->where('state_name', 'like', '%'.$this->stateSearch.'%'))
            ->orderBy('state_name')
            ->paginate(20);
    }

    #[Computed]
    public function cities()
    {
        return NigerianCityShipping::query()
            ->join('state_shippings', 'state_shippings.id', '=', 'nigerian_city_shippings.state_shipping_id')
            ->select('nigerian_city_shippings.*', 'state_shippings.state_name')
            ->when($this->citySearch, fn ($q) => $q->where('nigerian_city_shippings.city_name', 'like', '%'.$this->citySearch.'%')
                ->orWhere('state_shippings.state_name', 'like', '%'.$this->citySearch.'%')
            )
            ->orderBy('state_shippings.state_name')
            ->orderBy('nigerian_city_shippings.city_name')
            ->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.admin.shipping.shipping-manager');
    }
}
