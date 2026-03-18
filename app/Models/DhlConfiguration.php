<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DhlConfiguration extends Model
{
    protected $fillable = ['key', 'value', 'type', 'label'];

    /**
     * Get a configuration value, optionally casting by type.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $record = Cache::remember("dhl_config_{$key}", now()->addMinutes(30), function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (! $record) {
            return $default;
        }

        return match ($record->type) {
            'boolean' => filter_var($record->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $record->value,
            'float' => (float) $record->value,
            default => $record->value,
        };
    }

    /**
     * Set a configuration value and bust cache.
     */
    public static function set(string $key, mixed $value, string $type = 'string', ?string $label = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type, 'label' => $label]
        );
        Cache::forget("dhl_config_{$key}");
    }
}
