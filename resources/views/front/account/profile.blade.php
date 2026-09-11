@extends('front.layouts.app')

@section('content')
    <section id="relatedProducts" class="py-5">
        <div class="container">

            <section style="background:#FC8934;color:#fff;" class="py-3">
                <div class="container text-center">
                    <h1 class="fw-bold mb-1">Profile</h1>
                </div>
            </section>

            <section style="background:#f9fafc;min-height:100vh;padding:40px 0;font-family:'Segoe UI',sans-serif;">
                <div class="container" style="max-width:1100px;margin:auto;padding:0 20px;">

                    <!-- Centered Navbar -->
                    <div class="card mb-4"
                        style="border:none;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">
                        <div class="card-header"
                            style="background:linear-gradient(90deg,#FC8934,#ff9f50);color:#fff;padding:15px 25px;text-align:center;">
                            <ul
                                style="list-style:none;margin:0;padding:0;display:inline-flex;justify-content:center;align-items:center;gap:30px;white-space:nowrap;">
                                <li><a href="{{ route('account.userDashboard') }}"
                                        style="color:#fff;text-decoration:none;font-weight:500;transition:0.3s;">Dashboard</a>
                                </li>
                                <li><a href="{{ route('account.profile') }}"
                                        style="color:#fff;text-decoration:none;font-weight:500;transition:0.3s;">Profile</a>
                                </li>
                                <li><a href="{{ route('account.logout') }}"
                                        style="color:#fff;text-decoration:none;font-weight:500;transition:0.3s;">Logout</a>
                                </li>
                            </ul>

                        </div>
                    </div>
                    @if (Session::has('success'))
                        <div class="col-md-12">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ Session::get('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('error'))
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ Session::get('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    @endif

                    <!-- User Information -->
                    <div class="card mb-4"
                        style="border:none;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">
                        <div class="card-header"
                            style="background:linear-gradient(90deg,#FC8934,#ff9f50);color:#fff;padding:15px 25px;">
                            <h5 style="margin:0;font-weight:600;">Your Information</h5>
                        </div>
                        <div class="card-body" style="padding:25px;background:#fff;">

                            <div
                                style="background:#fff;border:1px solid #eee;border-radius:10px;padding:25px;box-shadow:0 4px 15px rgba(0,0,0,0.08);max-width:800px;margin:auto;">


                                <form action="" method="POST" id="profileupdate"
                                    style="display:flex;flex-wrap:wrap;gap:20px;justify-content:space-between;">
                                    <div style="flex:1 1 45%;min-width:250px;display:flex;flex-direction:column;">
                                        <label for="name" style="font-weight:500;margin-bottom:6px;color:#444;">Full
                                            Name</label>
                                        <input type="text" name="name" id="name" value="{{ $user->name }}"
                                            required
                                            style="padding:10px 14px;border:1px solid #ccc;border-radius:6px;font-size:15px;outline:none;transition:all 0.3s ease;">
                                        <p class="invalid-feedback" style="color:#e74c3c;font-size:13px;margin-top:4px;">
                                        </p>
                                    </div>

                                    <div style="flex:1 1 45%;min-width:250px;display:flex;flex-direction:column;">
                                        <label for="email" style="font-weight:500;margin-bottom:6px;color:#444;">Email
                                            Address</label>
                                        <input type="email" name="email" id="email" value="{{ $user->email }}"
                                            required
                                            style="padding:10px 14px;border:1px solid #ccc;border-radius:6px;font-size:15px;outline:none;transition:all 0.3s ease;">
                                        <p class="invalid-feedback" style="color:#e74c3c;font-size:13px;margin-top:4px;">
                                        </p>
                                    </div>

                                    <div style="flex:1 1 45%;min-width:250px;display:flex;flex-direction:column;">
                                        <label for="phone" style="font-weight:500;margin-bottom:6px;color:#444;">Phone
                                            Number</label>
                                        <input type="text" name="phone" id="phone"
                                            value="{{ $user->phone ?? '-' }}" required
                                            style="padding:10px 14px;border:1px solid #ccc;border-radius:6px;font-size:15px;outline:none;transition:all 0.3s ease;">
                                        <p class="invalid-feedback" style="color:#e74c3c;font-size:13px;margin-top:4px;">
                                        </p>
                                    </div>

                                    <div
                                        style="flex:1 1 45%;min-width:250px;display:flex;flex-direction:column;justify-content:flex-end;">
                                        <p style="margin:0;font-size:15px;color:#555;">
                                            <strong>Registered On:</strong> {{ $user->created_at->format('d M Y') }}
                                        </p>
                                    </div>

                                    <div style="width:100%;text-align:center;margin-top:15px;">
                                        <button type="submit"
                                            style="background:linear-gradient(90deg,#FC8934,#ff9f50);color:#fff;"
                                            class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                    <!-- Password Information -->
                    <div class="card mb-4"
                        style="border:none;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">
                        <div class="card-header"
                            style="background:linear-gradient(90deg,#FC8934,#ff9f50);color:#fff;padding:15px 25px;">
                            <h5 style="margin:0;font-weight:600;">Update Password</h5>
                        </div>
                        <div class="card-body" style="padding:25px;background:#fff;">
                            <div
                                style="background:#fff;border:1px solid #eee;border-radius:10px;padding:25px;box-shadow:0 4px 15px rgba(0,0,0,0.08);max-width:800px;margin:auto;">


                                <form action="" method="POST" id="passwordUpdate"
                                    style="display:flex;flex-wrap:wrap;gap:20px;justify-content:space-between;">

                                    <!-- Old Password -->
                                    <div
                                        style="flex:1 1 45%;min-width:250px;display:flex;flex-direction:column;position:relative;">
                                        <label for="oldPassword" style="font-weight:500;margin-bottom:6px;color:#444;">Old
                                            Password</label>
                                        <input type="password" name="oldPassword" id="oldPassword" required
                                            style="padding:10px 40px 10px 14px;border:1px solid #ccc;border-radius:6px;font-size:15px;outline:none;transition:all 0.3s ease;">
                                        <span onclick="togglePassword('oldPassword', this)"
                                            style="position:absolute;right:12px;top:38px;cursor:pointer;font-size:18px;color:#888;">👁️</span>
                                        <p class="invalid-feedback" style="color:#e74c3c;font-size:13px;margin-top:4px;">
                                        </p>
                                    </div>

                                    <!-- New Password -->
                                    <div
                                        style="flex:1 1 45%;min-width:250px;display:flex;flex-direction:column;position:relative;">
                                        <label for="newPassword" style="font-weight:500;margin-bottom:6px;color:#444;">New
                                            Password</label>
                                        <input type="password" name="newPassword" id="newPassword" required
                                            style="padding:10px 40px 10px 14px;border:1px solid #ccc;border-radius:6px;font-size:15px;outline:none;transition:all 0.3s ease;">
                                        <span onclick="togglePassword('newPassword', this)"
                                            style="position:absolute;right:12px;top:38px;cursor:pointer;font-size:18px;color:#888;">👁️</span>
                                        <p class="invalid-feedback" style="color:#e74c3c;font-size:13px;margin-top:4px;">
                                        </p>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div
                                        style="flex:1 1 45%;min-width:250px;display:flex;flex-direction:column;position:relative;">
                                        <label for="confirmPassword"
                                            style="font-weight:500;margin-bottom:6px;color:#444;">Confirm Password</label>
                                        <input type="password" name="confirmPassword" id="confirmPassword" required
                                            style="padding:10px 40px 10px 14px;border:1px solid #ccc;border-radius:6px;font-size:15px;outline:none;transition:all 0.3s ease;">
                                        <span onclick="togglePassword('confirmPassword', this)"
                                            style="position:absolute;right:12px;top:38px;cursor:pointer;font-size:18px;color:#888;">👁️</span>
                                        <p id="passwordError" class="text-danger small mt-1" style="display:none;">
                                            Passwords do not match
                                        </p>
                                    </div>

                                    <div style="width:100%;text-align:center;margin-top:15px;">
                                        <button type="submit" class="btn btn-warning">
                                            Update Password
                                        </button>
                                    </div>
                                </form>



                            </div>

                        </div>
                    </div>
                </div>
            </section>





        </div>
    </section>
@endsection

@section('customJs')
    <script>
        $("#profileupdate").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('account.profileUpdate') }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {

                        window.location.href = "{{ route('account.profile') }}";

                        $("#name").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#email").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#phone").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                    } else {
                        var errors = response['errors'];
                        if (errors['name']) {
                            $("#name").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['name']);
                        } else {
                            $("#name").removeClass('is-invalid')
                                .siblings('.invalid-feedback').html('');
                        }

                        if (errors['email']) {
                            $("#email").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['email']);
                        } else {
                            $("#email").removeClass('is-invalid')
                                .siblings('.invalid-feedback').html('');
                        }

                        if (errors['phone']) {
                            $("#phone").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['phone']);
                        } else {
                            $("#phone").removeClass('is-invalid')
                                .siblings('.invalid-feedback').html('');
                        }

                    }



                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });
    </script>
    <script>
        function togglePassword(fieldId, icon) {
            const input = document.getElementById(fieldId);
            if (input.type === "password") {
                input.type = "text";
                icon.textContent = "🙈";
            } else {
                input.type = "password";
                icon.textContent = "👁️";
            }
        }

        // Check if passwords match in real time
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('newPassword').value;
            const confirm = this.value;
            const errorMsg = document.getElementById('passwordError');

            if (confirm !== password) {
                errorMsg.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                errorMsg.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });

        $("#passwordUpdate").submit(function(event) {
            event.preventDefault();
            var element = $(this);

            // AJAX call to check old password & update
            $.ajax({
                url: '{{ route('account.updatePassword') }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {


                    if (response.status === true) {
                        window.location.href = "{{ route('account.profile') }}";
                         $("#oldPassword").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#newPassword").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#confirmPassword").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                    } else {
                        // Show server validation errors
                        if (response.errors) {
                            $.each(response.errors, function(key, val) {
                                $("#" + key).addClass('is-invalid')
                                    .siblings('.invalid-feedback').html(val);
                            });

                        } else if (response.message) {
                            // Old password mismatch
                            $("#oldPassword").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(response.message);

                        }
                    }
                },
                error: function() {
                    $("button[type=submit]").prop('disabled', false);
                    alert("Something went wrong. Please try again.");
                }
            });
        });
    </script>
@endsection
