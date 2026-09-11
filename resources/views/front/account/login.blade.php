@extends('front.layouts.app')

@section('content')
    <div style="display:flex; justify-content:center; align-items:center; height:100vh; padding:20px;">
        <div
            style="background:#fff; max-width:450px; width:100%; border-radius:15px; padding:30px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
            @if (Session::has('success'))
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if (Session::has('error'))
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ Session::get('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            <h2 style="text-align:center; font-size:2.5rem; color:#FC8934; margin-bottom:25px;">Login to your account</h2>

            <form action="{{ route('account.authenticate') }}" method="post">
                @csrf

                <!-- Email or Phone -->
                <div style="margin-bottom:20px;">
                    <label for="login" style="display:block; margin-bottom:8px; font-weight:bold; color:#333;">Email /
                        Phone</label>
                    <input type="text" id="login" name="login" placeholder="Enter your email or phone"
                        value="{{ old('login') }}" class="form-control @error('login') is-invalid @enderror"
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:1rem; outline:none; transition:0.3s;">
                    @error('login')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div style="margin-bottom:20px;">
                    <label for="password"
                        style="display:block; margin-bottom:8px; font-weight:bold; color:#333;">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password"
                        class="form-control @error('password') is-invalid @enderror"
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:1rem; outline:none; transition:0.3s;">
                    @error('password')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Button -->
                <div style="text-align:center;">
                    <button type="submit" class="form-control" style="background:#FC8934; color: #fff;">Login</button>
                </div>
            </form>


            <!-- Already have account -->
            <p style="text-align:center; margin-top:20px; font-size:0.95rem; color:#555;">
                Dont have anu account?
                <a href="{{ route('account.register') }}"
                    style="color:#FC8934; font-weight:bold; text-decoration:none;">Register</a>
            </p>
        </div>
    </div>
@endsection

@section('customJs')
@endsection
