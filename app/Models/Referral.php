<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Referral extends Model
{
    protected $fillable = ['user_id', 'code'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uses(): HasMany
    {
        return $this->hasMany(ReferralUse::class);
    }

    public function getUrlAttribute(): string
    {
        return url('/?ref='.$this->code);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'DF-'.strtoupper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
