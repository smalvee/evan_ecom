<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evan Store :: Administrative Panel</title>
    <link rel="stylesheet" href="{{ asset('new-admin-assets/css/vendors/bootstrap.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('new-admin-assets/css/admin.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: #f4f6f9;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center p-3">
    <div class="login-card card border-0 shadow-sm">
        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                    style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #0da487, #0a8a70); color: #fff; font-size: 24px; font-weight: 800;">
                    E
                </div>
                <h3 class="fw-bold mb-1">Evan Store</h3>
                <p class="text-muted mb-0">Administrative Panel</p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.authenticate') }}" method="post">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-theme w-100 justify-content-center">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
