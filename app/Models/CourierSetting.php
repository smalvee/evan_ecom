<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Per-provider courier configuration.
 *
 * API credentials are encrypted at rest (Laravel `encrypted` cast) and are
 * never exposed to the browser or logs.
 */
class CourierSetting extends Model
{
    use HasFactory;

    public const MODE_TEST = 'test';
    public const MODE_LIVE = 'live';

    protected $fillable = [
        'provider',
        'mode',
        'base_url',
        'api_key',
        'secret_key',
        'is_active',
    ];

    protected $hidden = [
        'api_key',
        'secret_key',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'secret_key' => 'encrypted',
        'is_active' => 'boolean',
    ];

    /**
     * The settings row that is currently in effect (never null).
     *
     * Falls back to an unsaved Test/Mock configuration so the courier module
     * works out of the box with no credentials.
     */
    public static function current(): self
    {
        $provider = config('courier.default', 'steadfast');

        return static::query()->where('provider', $provider)->first()
            ?? new static([
                'provider' => $provider,
                'mode' => self::MODE_TEST,
                'is_active' => true,
            ]);
    }

    public static function forProvider(string $provider): self
    {
        return static::firstOrNew(['provider' => $provider]);
    }

    public function isLive(): bool
    {
        return $this->mode === self::MODE_LIVE;
    }

    public function isTest(): bool
    {
        return $this->mode !== self::MODE_LIVE;
    }

    public function hasCredentials(): bool
    {
        return filled($this->api_key) && filled($this->secret_key);
    }

    /**
     * Masked API key for display — never reveals the full credential.
     */
    public function maskedApiKey(): ?string
    {
        return $this->mask($this->api_key);
    }

    public function maskedSecretKey(): ?string
    {
        return $this->mask($this->secret_key);
    }

    protected function mask(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $tail = strlen($value) > 4 ? substr($value, -4) : '';

        return str_repeat('•', 12) . $tail;
    }
}
