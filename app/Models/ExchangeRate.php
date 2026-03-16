<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency_id',
        'rate',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:8',
            'fetched_at' => 'datetime',
        ];
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
