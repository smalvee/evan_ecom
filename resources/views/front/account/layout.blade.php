@extends('front.layouts.new_app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('new-front-assets/css/customer-account.css') }}">
@endpush

@section('content')
    <section class="account-hero">
        <div class="container">
            <nav class="account-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('front.home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('account.userDashboard') }}">My Account</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">@yield('account-title', 'Dashboard')</span>
            </nav>
            <h1>@yield('account-heading', 'My Account')</h1>
            @hasSection('account-subheading')
                <p>@yield('account-subheading')</p>
            @endif
        </div>
    </section>

    <section class="account-section">
        <div class="container">
            <div class="account-layout">
                <aside class="account-sidebar">
                    <x-account.sidebar />
                </aside>

                <main class="account-content">
                    @yield('account-content')
                </main>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    @yield('account-js')
@endsection
