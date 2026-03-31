<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'order_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the total points balance for a user.
     */
    public static function balanceFor(int $userId): int
    {
        return (int) static::where('user_id', $userId)->sum('points');
    }
}
