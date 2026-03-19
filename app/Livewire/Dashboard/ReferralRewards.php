<?php

namespace App\Livewire\Dashboard;

use App\Models\Referral;
use App\Models\RewardPoint;
use App\Models\RewardSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Referral & Rewards')]
class ReferralRewards extends Component
{
    public function render()
    {
        $user     = auth()->user();
        $referral = Referral::firstOrCreate(
            ['user_id' => $user->id],
            ['code'    => Referral::generateCode()]
        );

        $pointBalance  = RewardPoint::balanceFor($user->id);
        $nairaPerPoint = RewardSetting::nairaPerPoint();
        $maxPerOrder   = RewardSetting::maxPointsPerOrder();
        $pointsValue   = $pointBalance * $nairaPerPoint;

        $history = RewardPoint::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        $referralUses = $referral->uses()
            ->with('order')
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.dashboard.referral-rewards', compact(
            'referral',
            'pointBalance',
            'nairaPerPoint',
            'maxPerOrder',
            'pointsValue',
            'history',
            'referralUses',
        ));
    }
}