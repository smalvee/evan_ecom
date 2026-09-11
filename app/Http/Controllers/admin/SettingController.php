<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Fields that are simple text/url values stored as-is.
     */
    protected array $fields = [
        'site_phone',
        'site_email',
        'site_address',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'linkedin_url',
        'twitter_url',
        'pinterest_url',
        'google_url',
    ];

    public function index()
    {
        $settings = Setting::siteSettings();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_logo' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'site_phone' => 'nullable|string|max:30',
            'site_email' => 'nullable|email|max:150',
            'site_address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'pinterest_url' => 'nullable|url|max:255',
            'google_url' => 'nullable|url|max:255',
        ]);

        // Logo upload (stored on the dedicated "uploads" disk => public/uploads/settings).
        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::get('site_logo');
            $newPath = $request->file('site_logo')->store('settings', 'uploads');

            Setting::set('site_logo', $newPath);

            // Remove the previous logo only if it is managed by this settings system.
            if ($oldLogo && str_starts_with($oldLogo, 'settings/') && Storage::disk('uploads')->exists($oldLogo)) {
                Storage::disk('uploads')->delete($oldLogo);
            }
        }

        foreach ($this->fields as $field) {
            Setting::set($field, $request->input($field));
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Website settings updated successfully.');
    }
}
