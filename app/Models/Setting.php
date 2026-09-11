<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value website settings with a single cached read.
 *
 * Usage:
 *   Setting::get('site_phone');
 *   Setting::set('site_phone', '+880 ...');
 *   Setting::siteSettings();   // friendly array for views
 *   Setting::socialLinks();    // only configured social links
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public const CACHE_KEY = 'site_settings';

    /** Social platform definitions (key => label + Font Awesome icon). */
    public const SOCIALS = [
        'facebook_url' => ['label' => 'Facebook', 'icon' => 'fab fa-facebook-f'],
        'instagram_url' => ['label' => 'Instagram', 'icon' => 'fab fa-instagram'],
        'youtube_url' => ['label' => 'YouTube', 'icon' => 'fab fa-youtube'],
        'tiktok_url' => ['label' => 'TikTok', 'icon' => 'fab fa-tiktok'],
        'linkedin_url' => ['label' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in'],
        'twitter_url' => ['label' => 'Twitter', 'icon' => 'fab fa-twitter'],
        'pinterest_url' => ['label' => 'Pinterest', 'icon' => 'fab fa-pinterest-p'],
        'google_url' => ['label' => 'Google', 'icon' => 'fab fa-google'],
    ];

    /**
     * All settings as an associative array, cached forever.
     *
     * @return array<string, string|null>
     */
    public static function all_cached(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return static::query()->pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            // Table not available yet (e.g. during install) — fail gracefully.
            return [];
        }
    }

    public static function get(string $key, $default = null)
    {
        $all = static::all_cached();

        return array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== ''
            ? $all[$key]
            : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::flush();
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Public URL for the uploaded logo, or null when none is set.
     */
    public static function logoUrl(): ?string
    {
        $path = static::get('site_logo');

        return $path ? asset('uploads/' . $path) : null;
    }

    /**
     * Friendly, view-ready settings array.
     */
    public static function siteSettings(): array
    {
        return [
            'site_logo' => static::get('site_logo'),
            'site_logo_url' => static::logoUrl(),
            'site_phone' => static::get('site_phone'),
            'site_email' => static::get('site_email'),
            'site_address' => static::get('site_address'),
            'facebook_url' => static::get('facebook_url'),
            'instagram_url' => static::get('instagram_url'),
            'youtube_url' => static::get('youtube_url'),
            'tiktok_url' => static::get('tiktok_url'),
            'linkedin_url' => static::get('linkedin_url'),
            'twitter_url' => static::get('twitter_url'),
            'pinterest_url' => static::get('pinterest_url'),
            'google_url' => static::get('google_url'),
        ];
    }

    /**
     * Only the social links that have been configured.
     */
    public static function socialLinks(): array
    {
        $links = [];

        foreach (self::SOCIALS as $key => $meta) {
            $url = static::get($key);

            if (!empty($url)) {
                $links[] = [
                    'key' => $key,
                    'label' => $meta['label'],
                    'icon' => $meta['icon'],
                    'url' => $url,
                ];
            }
        }

        return $links;
    }
}
