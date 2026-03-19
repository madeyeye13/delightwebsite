<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RewardSetting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'description'];

    /**
     * Get a setting value by key with caching.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("reward_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Update a setting and clear its cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget("reward_setting_{$key}");
    }

    // ── Convenience getters ──────────────────────────────────────────────

    public static function pointsPerReferral(): int
    {
        return (int) static::get('points_per_referral', 100);
    }

    public static function nairaPerPoint(): int
    {
        return (int) static::get('naira_per_point', 10);
    }

    public static function maxPointsPerOrder(): int
    {
        return (int) static::get('max_points_per_order', 300);
    }

    public static function referralDiscountPercent(): int
    {
        return (int) static::get('referral_discount_percent', 5);
    }
}