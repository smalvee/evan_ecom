@extends('front.layouts.app')

@section('content')
    <!-- Top Header -->
    <section style="background:#FC8934;color:#fff;" class="py-3">
        <div class="container text-center">
            <h1 class="fw-bold mb-1">About Us</h1>
            {{-- <p class="lead mb-0">Learn more about Ghorer Bazar and our mission</p> --}}
        </div>
    </section>

    <!-- Who We Are Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="{{ asset('front-assets/images/logo.jpg') }}" class="img-fluid rounded shadow" alt="About Us">
                </div>
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3">Who We Are</h2>
                    {!! $about_us->who_we_are !!}
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section style="background:#fff7f0;" class="py-5">
        <div class="container">
            <div class="row text-center">
                {{-- <div class="col-md-6 mb-4">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <i class="bi bi-bullseye fs-1 mb-3" style="color:#FC8934;"></i>
                        <h3 class="fw-bold mb-2">Our Mission</h3>
                        {!! $about_us->our_mission !!}
                    </div>
                </div> --}}
                <div class="col-md-6 mb-4">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <i class="bi bi-eye fs-1 mb-3" style="color:#FC8934;"></i>
                        <h3 class="fw-bold mb-2">Our Mission</h3>
                        {!! $about_us->our_mission !!}
                    </div>
                </div>
                {{-- <div class="col-md-6 mb-4">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <i class="bi bi-eye fs-1 mb-3" style="color:#FC8934;"></i>
                        <h3 class="fw-bold mb-2">Our Mission</h3>
                        {!! $about_us->our_mission !!}
                    </div>
                </div> --}}

                <div class="col-md-6 mb-4">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <i class="bi bi-bullseye fs-1 mb-3" style="color:#FC8934;"></i>
                        <h3 class="fw-bold mb-2">Our Vision</h3>
                        {!! $about_us->our_vision !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    {{-- <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Meet Our Team</h2>
                <p class="text-muted">Dedicated people behind Ghorer Bazar</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <img src="{{ asset('front-assets/images/team1.jpg') }}" class="rounded-circle mb-3" style="width:150px; height:150px; object-fit:cover;" alt="Team Member">
                    <h5 class="fw-bold mb-0">John Doe</h5>
                    <small class="text-muted">CEO</small>
                </div>
                <div class="col-md-3 text-center">
                    <img src="{{ asset('front-assets/images/team2.jpg') }}" class="rounded-circle mb-3" style="width:150px; height:150px; object-fit:cover;" alt="Team Member">
                    <h5 class="fw-bold mb-0">Jane Smith</h5>
                    <small class="text-muted">Marketing Head</small>
                </div>
                <div class="col-md-3 text-center">
                    <img src="{{ asset('front-assets/images/team3.jpg') }}" class="rounded-circle mb-3" style="width:150px; height:150px; object-fit:cover;" alt="Team Member">
                    <h5 class="fw-bold mb-0">Ali Rahman</h5>
                    <small class="text-muted">Operations</small>
                </div>
                <div class="col-md-3 text-center">
                    <img src="{{ asset('front-assets/images/team4.jpg') }}" class="rounded-circle mb-3" style="width:150px; height:150px; object-fit:cover;" alt="Team Member">
                    <h5 class="fw-bold mb-0">Sara Khan</h5>
                    <small class="text-muted">Customer Support</small>
                </div>
            </div>
        </div>
    </section> --}}

    <br>
    <br>
    <br>
    <br>
@endsection

@section('customJs')
@endsection
