<?php

namespace App\Livewire\Admin;

use App\Models\RewardSetting;
use Livewire\Component;  // ← no Layout/Title imports needed

class RewardSettings extends Component
{
    public int $points_per_referral = 100;

    public int $naira_per_point = 10;

    public int $max_points_per_order = 300;

    public int $referral_discount_percent = 5;

    public bool $saved = false;

    public function mount(): void
    {
        $this->points_per_referral = (int) RewardSetting::get('points_per_referral', 100);
        $this->naira_per_point = (int) RewardSetting::get('naira_per_point', 10);
        $this->max_points_per_order = (int) RewardSetting::get('max_points_per_order', 300);
        $this->referral_discount_percent = (int) RewardSetting::get('referral_discount_percent', 5);
    }

    public function save(): void
    {
        $this->validate([
            'points_per_referral' => 'required|integer|min:1|max:10000',
            'naira_per_point' => 'required|integer|min:1|max:10000',
            'max_points_per_order' => 'required|integer|min:1|max:100000',
            'referral_discount_percent' => 'required|integer|min:1|max:100',
        ]);

        RewardSetting::set('points_per_referral', $this->points_per_referral);
        RewardSetting::set('naira_per_point', $this->naira_per_point);
        RewardSetting::set('max_points_per_order', $this->max_points_per_order);
        RewardSetting::set('referral_discount_percent', $this->referral_discount_percent);

        $this->saved = true;
        $this->dispatch('toast', type: 'success', message: 'Reward settings saved.');
    }

    public function render()
    {
        return view('livewire.admin.reward-settings');
    }
}
