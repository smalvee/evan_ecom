@extends('front.account.layout')

@section('account-title', 'Profile')
@section('account-heading', 'Profile')
@section('account-subheading', 'Manage your personal information and password.')

@section('account-content')
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="account-card h-100">
                <div class="account-card-head">
                    <h2>Personal Information</h2>
                </div>
                <div class="account-card-body">
                    <form action="" method="POST" id="profileupdate" class="account-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ $user->name }}"
                                class="form-control" required>
                            <p class="invalid-feedback"></p>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ $user->email }}"
                                class="form-control" required>
                            <p class="invalid-feedback"></p>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ $user->phone ?? '' }}"
                                class="form-control" required>
                            <p class="invalid-feedback"></p>
                        </div>
                        <p class="text-muted small">
                            <strong>Registered On:</strong> {{ optional($user->created_at)->format('d M Y') ?? '—' }}
                        </p>
                        <button type="submit" class="account-btn account-btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="account-card h-100">
                <div class="account-card-head">
                    <h2>Update Password</h2>
                </div>
                <div class="account-card-body">
                    <form action="" method="POST" id="passwordUpdate" class="account-form">
                        <div class="mb-3 position-relative">
                            <label for="oldPassword" class="form-label">Old Password</label>
                            <input type="password" name="oldPassword" id="oldPassword" class="form-control pe-5"
                                required>
                            <span onclick="togglePassword('oldPassword', this)"
                                style="position:absolute;right:12px;top:42px;cursor:pointer;">👁️</span>
                            <p class="invalid-feedback"></p>
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" name="newPassword" id="newPassword" class="form-control pe-5"
                                required>
                            <span onclick="togglePassword('newPassword', this)"
                                style="position:absolute;right:12px;top:42px;cursor:pointer;">👁️</span>
                            <p class="invalid-feedback"></p>
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" name="confirmPassword" id="confirmPassword"
                                class="form-control pe-5" required>
                            <span onclick="togglePassword('confirmPassword', this)"
                                style="position:absolute;right:12px;top:42px;cursor:pointer;">👁️</span>
                            <p id="passwordError" class="text-danger small mt-1" style="display:none;">Passwords
                                do not match</p>
                            <p class="invalid-feedback"></p>
                        </div>
                        <button type="submit" class="account-btn account-btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('account-js')
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
                    } else {
                        var errors = response['errors'] || {};
                        ['name', 'email', 'phone'].forEach(function(field) {
                            var input = $("#" + field);
                            if (errors[field]) {
                                input.addClass('is-invalid').siblings('.invalid-feedback').html(errors[
                                    field]);
                            } else {
                                input.removeClass('is-invalid').siblings('.invalid-feedback').html('');
                            }
                        });
                    }
                },
                error: function() {
                    console.log("Something went wrong");
                }
            })
        });

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

            $.ajax({
                url: '{{ route('account.updatePassword') }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === true) {
                        window.location.href = "{{ route('account.profile') }}";
                    } else if (response.errors) {
                        $.each(response.errors, function(key, val) {
                            $("#" + key).addClass('is-invalid').siblings('.invalid-feedback').html(val);
                        });
                    } else if (response.message) {
                        $("#oldPassword").addClass('is-invalid').siblings('.invalid-feedback').html(response
                            .message);
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
