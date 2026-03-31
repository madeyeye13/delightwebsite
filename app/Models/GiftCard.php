<?php

namespace App\Models;

use Database\Factories\GiftCardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GiftCard extends Model
{
    /** @use HasFactory<GiftCardFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'status',
        'initial_balance',
        'current_balance',
        'purchased_by_user_id',
        'purchased_order_id',
        'recipient_email',
        'recipient_name',
        'personal_message',
        'is_pos_issued',
        'issued_by_admin_id',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'initial_balance' => 'integer',
            'current_balance' => 'integer',
            'is_pos_issued' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function purchasedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by_user_id');
    }

    public function purchasedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'purchased_order_id');
    }

    public function issuedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_admin_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class)->latest();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return $this->current_balance > 0;
    }

    public function isFullyRedeemed(): bool
    {
        return $this->current_balance <= 0;
    }

    public function getNotificationEmail(): ?string
    {
        // Prefer recipient email; fall back to purchaser email
        if ($this->recipient_email) {
            return $this->recipient_email;
        }

        return $this->purchasedByUser?->email;
    }

    public function getNotificationName(): string
    {
        if ($this->recipient_name) {
            return $this->recipient_name;
        }

        return $this->purchasedByUser?->name ?? 'Gift Card Holder';
    }
}
