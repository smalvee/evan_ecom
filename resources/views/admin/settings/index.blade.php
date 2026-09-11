@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Settings</li>
                </ul>
                <h4 class="a-page-title">Website Settings</h4>
                <p class="a-page-desc">Manage your website branding, contact information and social media links.</p>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
            @csrf
            @method('PUT')

            {{-- Branding + contact --}}
            <div class="a-card mb-3">
                <div class="a-card-head">
                    <h5>Website Branding</h5>
                </div>
                <div class="a-card-body">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="form-label">Website Logo</label>
                            <div class="a-logo-preview">
                                <img id="logoPreview"
                                    src="{{ $settings['site_logo_url'] ?? asset('new-front-assets/images/logo/8.png') }}"
                                    alt="Website logo">
                            </div>
                            <input type="file" name="site_logo" id="site_logo"
                                class="form-control mt-3 @error('site_logo') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.webp,.svg">
                            @error('site_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="text-muted small mt-2 mb-0">
                                JPG, PNG, WEBP or SVG · max 2MB.<br>Transparent PNG / WEBP recommended.
                            </p>
                        </div>

                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label class="form-label" for="site_phone">Phone Number</label>
                                <input type="text" name="site_phone" id="site_phone"
                                    class="form-control @error('site_phone') is-invalid @enderror"
                                    value="{{ old('site_phone', $settings['site_phone']) }}" placeholder="+880 1XXXXXXXXX">
                                @error('site_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="site_email">Email Address</label>
                                <input type="email" name="site_email" id="site_email"
                                    class="form-control @error('site_email') is-invalid @enderror"
                                    value="{{ old('site_email', $settings['site_email']) }}" placeholder="info@example.com">
                                @error('site_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label" for="site_address">Business Address</label>
                                <textarea name="site_address" id="site_address" rows="3"
                                    class="form-control @error('site_address') is-invalid @enderror"
                                    placeholder="270 West Haji Nagar, Staff Quarter, Demra, Dhaka-1360">{{ old('site_address', $settings['site_address']) }}</textarea>
                                @error('site_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social media --}}
            <div class="a-card mb-3">
                <div class="a-card-head">
                    <h5>Social Media</h5>
                    <span class="text-muted small">Leave a field empty to hide that icon in the footer.</span>
                </div>
                <div class="a-card-body">
                    <div class="row g-3">
                        @php
                            $socials = [
                                'facebook_url' => ['Facebook', 'fab fa-facebook-f'],
                                'instagram_url' => ['Instagram', 'fab fa-instagram'],
                                'youtube_url' => ['YouTube', 'fab fa-youtube'],
                                'tiktok_url' => ['TikTok', 'fab fa-tiktok'],
                                'linkedin_url' => ['LinkedIn', 'fab fa-linkedin-in'],
                                'twitter_url' => ['Twitter', 'fab fa-twitter'],
                                'pinterest_url' => ['Pinterest', 'fab fa-pinterest-p'],
                                'google_url' => ['Google', 'fab fa-google'],
                            ];
                        @endphp

                        @foreach ($socials as $key => [$label, $icon])
                            <div class="col-lg-6">
                                <label class="form-label" for="{{ $key }}">
                                    <i class="{{ $icon }} me-1"></i> {{ $label }}
                                </label>
                                <input type="url" name="{{ $key }}" id="{{ $key }}"
                                    class="form-control @error($key) is-invalid @enderror"
                                    value="{{ old($key, $settings[$key]) }}"
                                    placeholder="https://{{ str_replace('_url', '', $key) }}.com/your-page">
                                @error($key)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="a-card">
                <div class="a-card-body d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-theme"><i class="ri-save-3-line"></i> Save Changes</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <style>
        .a-logo-preview {
            border: 1px dashed var(--a-border-strong);
            border-radius: var(--a-radius);
            background: #fbfcfd;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            overflow: hidden;
        }

        .a-logo-preview img {
            max-width: 100%;
            max-height: 140px;
            object-fit: contain;
        }
    </style>
    <script>
        // Client-side logo preview (file is only uploaded on form submit).
        document.getElementById('site_logo').addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('logoPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Saved!',
                text: @json(session('success')),
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
