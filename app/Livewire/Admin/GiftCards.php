<?php

namespace App\Livewire\Admin;

use App\Models\GiftCard;
use App\Services\GiftCardService;
use Illuminate\View\View;
use Livewire\Component;

class GiftCards extends Component
{
    public string $code = '';

    public string $orderAmount = '';

    /** @var array<string, mixed>|null */
    public ?array $foundCard = null;

    public int $step = 1;

    /** @var array<string, mixed>|null */
    public ?array $result = null;

    public string $errorMessage = '';

    public string $notes = '';

    private int $lookupAttempts = 0;

    public function lookupCode(): void
    {
        $this->errorMessage = '';

        $code = strtoupper(trim($this->code));
        if (empty($code)) {
            $this->errorMessage = 'Please enter a gift card code.';

            return;
        }

        $validation = app(GiftCardService::class)->validate($code);

        if (! $validation['valid']) {
            $this->errorMessage = $validation['message'];
            $this->foundCard = null;

            return;
        }

        /** @var GiftCard $card */
        $card = $validation['card'];

        $this->foundCard = [
            'id' => $card->id,
            'code' => $card->code,
            'current_balance' => $card->current_balance,
            'initial_balance' => $card->initial_balance,
            'status' => $card->status,
            'expires_at' => $card->expires_at?->format('d M Y'),
            'recipient_name' => $card->recipient_name,
            'recipient_email' => $card->recipient_email,
            'is_pos_issued' => $card->is_pos_issued,
        ];

        $this->step = 2;
        $this->orderAmount = '';
        $this->notes = '';
    }

    public function applyCard(): void
    {
        $this->errorMessage = '';

        $amount = (int) $this->orderAmount;
        if ($amount <= 0) {
            $this->errorMessage = 'Please enter a valid order amount.';

            return;
        }

        if (! $this->foundCard) {
            $this->errorMessage = 'No gift card loaded. Please look up a code first.';

            return;
        }

        $card = GiftCard::find($this->foundCard['id']);
        if (! $card) {
            $this->errorMessage = 'Gift card not found.';

            return;
        }

        $adminId = auth()->id();
        $redemption = app(GiftCardService::class)->redeemForPos(
            card: $card,
            orderAmount: $amount,
            adminId: $adminId,
            notes: trim($this->notes),
        );

        if (! $redemption['success']) {
            $this->errorMessage = $redemption['message'];

            return;
        }

        $this->result = $redemption;
        $this->step = 3;
    }

    public function resetPanel(): void
    {
        $this->code = '';
        $this->orderAmount = '';
        $this->foundCard = null;
        $this->step = 1;
        $this->result = null;
        $this->errorMessage = '';
        $this->notes = '';
    }

    public function render(): View
    {
        return view('livewire.admin.gift-cards');
    }
}
