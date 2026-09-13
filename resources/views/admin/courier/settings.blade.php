@extends('admin.layouts.new_app')

@php
    $isLive = $setting->isLive();
    $hasCredentials = $setting->hasCredentials();
@endphp

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.settings') }}">Settings</a></li>
                    <li class="is-active">Courier</li>
                </ul>
                <h4 class="a-page-title">Courier Settings</h4>
                <p class="a-page-desc">Configure the courier provider and environment used to dispatch orders.</p>
            </div>
            <div class="a-actions">
                <span class="a-badge {{ $isLive ? 'a-badge-danger' : 'a-badge-warning' }}">
                    <span class="dot"></span>{{ $isLive ? 'LIVE MODE' : 'TEST MODE' }}
                </span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.courier.settings.update') }}" method="POST" id="courierSettingsForm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Provider &amp; Environment</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="mb-3">
                                <label class="form-label" for="provider">Courier Provider</label>
                                <select name="provider" id="provider" class="form-select">
                                    @foreach ($providers as $key => $meta)
                                        <option value="{{ $key }}"
                                            {{ old('provider', $setting->provider) === $key ? 'selected' : '' }}>
                                            {{ $meta['label'] ?? ucfirst($key) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Environment</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mode" id="mode_test"
                                            value="test" {{ old('mode', $setting->mode) === 'test' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mode_test">
                                            Test / Mock <span class="text-muted small">(no real API calls)</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mode" id="mode_live"
                                            value="live" {{ old('mode', $setting->mode) === 'live' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mode_live">Live</label>
                                    </div>
                                </div>
                                <p class="text-muted small mb-0 mt-2">
                                    Live mode requires valid Steadfast credentials and will never fall back to Test mode.
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="api_key">API Key</label>
                                <input type="password" name="api_key" id="api_key" class="form-control"
                                    autocomplete="new-password"
                                    placeholder="{{ $hasCredentials ? $setting->maskedApiKey() . ' — leave blank to keep' : 'Enter API key' }}">
                                @if ($hasCredentials)
                                    <p class="text-muted small mb-0 mt-1">
                                        <i class="ri-lock-2-line"></i> Stored securely (encrypted). Leave blank to keep it.
                                    </p>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="secret_key">Secret Key</label>
                                <input type="password" name="secret_key" id="secret_key" class="form-control"
                                    autocomplete="new-password"
                                    placeholder="{{ $hasCredentials ? $setting->maskedSecretKey() . ' — leave blank to keep' : 'Enter secret key' }}">
                                @if ($hasCredentials)
                                    <p class="text-muted small mb-0 mt-1">
                                        <i class="ri-lock-2-line"></i> Stored securely (encrypted). Leave blank to keep it.
                                    </p>
                                @endif
                            </div>

                            {{-- Hidden 0 guarantees the field is posted when the switch is off. --}}
                            <input type="hidden" name="is_active" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                    value="1" {{ old('is_active', $setting->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Courier integration active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Connection</h5>
                        </div>
                        <div class="a-card-body">
                            <p class="text-muted small">
                                Verify the current configuration. Test mode always succeeds without credentials.
                            </p>
                            <div id="connectionResult" class="mb-3" hidden></div>
                            <button type="button" class="btn btn-outline-secondary w-100" id="testConnectionBtn">
                                <i class="ri-plug-line"></i> Test Connection
                            </button>
                        </div>
                    </div>

                    <div class="a-card">
                        <div class="a-card-head">
                            <h5>How it works</h5>
                        </div>
                        <div class="a-card-body">
                            <ul class="mb-0 ps-3 text-muted small">
                                <li>Test mode uses a built-in mock courier — no external requests.</li>
                                <li>Live mode sends real consignments to Steadfast.</li>
                                <li>COD is always calculated from the stored order total.</li>
                                <li>Credentials are encrypted at rest and never shown in full.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="a-card">
                <div class="a-card-body d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('admin.courier.settings') }}" class="btn btn-outline-secondary">Reset</a>
                    <button type="submit" class="btn btn-theme"><i class="ri-save-3-line"></i> Save Settings</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        (function() {
            'use strict';

            var btn = document.getElementById('testConnectionBtn');
            var result = document.getElementById('connectionResult');

            if (!btn) return;

            btn.addEventListener('click', function() {
                var original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing…';
                result.hidden = true;

                var payload = {
                    _token: '{{ csrf_token() }}',
                    provider: document.getElementById('provider').value,
                    mode: (document.querySelector('input[name="mode"]:checked') || {}).value || 'test',
                    api_key: document.getElementById('api_key').value,
                    secret_key: document.getElementById('secret_key').value
                };

                jQuery.ajax({
                    url: '{{ route('admin.courier.settings.test') }}',
                    type: 'POST',
                    data: payload,
                    dataType: 'json',
                    success: function(response) {
                        var ok = !!response.success;
                        result.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
                        result.innerHTML = (ok ? '✓ ' : '✕ ') + (response.message || '');
                        result.hidden = false;
                    },
                    error: function(xhr) {
                        result.className = 'alert alert-danger';
                        result.innerHTML = '✕ ' + ((xhr.responseJSON && xhr.responseJSON.message) ||
                            'Unable to test the connection. Please try again.');
                        result.hidden = false;
                    },
                    complete: function() {
                        btn.disabled = false;
                        btn.innerHTML = original;
                    }
                });
            });
        })();
    </script>
@endsection
