<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CourierSetting;
use App\Services\Courier\CourierManager;
use Illuminate\Http\Request;

class CourierSettingsController extends Controller
{
    /**
     * Courier provider + environment + credentials.
     */
    public function index()
    {
        $setting = CourierSetting::current();
        $providers = config('courier.providers', []);
        $defaultBaseUrl = config('courier.providers.steadfast.base_url');

        return view('admin.courier.settings', [
            'setting' => $setting,
            'providers' => $providers,
            'defaultBaseUrl' => $defaultBaseUrl,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|string|in:' . implode(',', array_keys(config('courier.providers', ['steadfast' => []]))),
            'mode' => 'required|in:test,live',
            'base_url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $setting = CourierSetting::forProvider($data['provider']);
        $setting->provider = $data['provider'];
        $setting->mode = $data['mode'];
        // Blank (or absent) clears the override, falling back to the config default.
        $setting->base_url = ($data['base_url'] ?? null) ?: null;
        // The form always posts is_active (hidden 0 + checkbox 1), so an
        // unchecked box correctly resolves to false.
        $setting->is_active = $request->boolean('is_active');

        // Blank credential fields keep the previously stored values.
        if (!empty($data['api_key'])) {
            $setting->api_key = $data['api_key'];
        }

        if (!empty($data['secret_key'])) {
            $setting->secret_key = $data['secret_key'];
        }

        // Live mode must not be saved without credentials.
        if ($setting->isLive() && !$setting->hasCredentials()) {
            return back()
                ->withErrors(['mode' => 'Please configure Steadfast API credentials before using Live mode.'])
                ->withInput();
        }

        $setting->save();

        return redirect()
            ->route('admin.courier.settings')
            ->with('success', 'Courier settings saved successfully.');
    }

    /**
     * Test connectivity for the submitted (or stored) configuration.
     */
    public function testConnection(Request $request)
    {
        $provider = $request->input('provider', config('courier.default', 'steadfast'));

        $setting = CourierSetting::forProvider($provider);
        $setting->provider = $provider;
        $setting->mode = $request->input('mode', $setting->mode ?: CourierSetting::MODE_TEST);

        // Allow testing credentials / base URL before they are persisted.
        if ($request->filled('base_url')) {
            $setting->base_url = $request->input('base_url');
        }

        if ($request->filled('api_key')) {
            $setting->api_key = $request->input('api_key');
        }

        if ($request->filled('secret_key')) {
            $setting->secret_key = $request->input('secret_key');
        }

        $response = CourierManager::make($setting)->testConnection();

        return response()->json([
            'success' => $response->success,
            'message' => $response->message,
            'mode' => $setting->mode,
        ]);
    }
}
