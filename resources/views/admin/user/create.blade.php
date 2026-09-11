@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="is-active">Create</li>
                </ul>
                <h4 class="a-page-title">Create User</h4>
                <p class="a-page-desc">Add a new customer or admin account.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>User Information</h5>
            </div>
            <div class="a-card-body">
                <form action="" method="POST" id="categoryForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label a-required">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Name" required>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="Email">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label a-required">Phone</label>
                                <input type="tel" name="phone" id="phone" class="form-control"
                                    placeholder="Mobile Number" required>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Block</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label a-required">Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="">Select One</option>
                                    <option value="2">Admin</option>
                                    <option value="1">Customer</option>
                                </select>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label a-required">Password</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password"
                                        class="form-control pe-5" placeholder="Password" required>
                                    <i class="ri-eye-line text-muted"
                                        style="cursor: pointer; position: absolute; right: 12px; top: 50%; transform: translateY(-50%);"
                                        onclick="togglePassword('password', this)"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label a-required">Confirm Password</label>
                                <div class="position-relative">
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control pe-5" placeholder="Confirm Password" required>
                                    <i class="ri-eye-line text-muted"
                                        style="cursor: pointer; position: absolute; right: 12px; top: 50%; transform: translateY(-50%);"
                                        onclick="togglePassword('confirm_password', this)"></i>
                                </div>
                                <p id="passwordError" class="text-danger small mt-1" style="display:none;">
                                    Passwords do not match
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="btn btn-theme">Create</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#categoryForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('users.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'User added successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.href = "{{ route('users.index') }}";
                        }, 1500);
                    } else {
                        var errors = response['errors'] || {};
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').html('');
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).siblings('.invalid-feedback').html(value);
                        });
                    }
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });

        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');

            if (field.type === "password") {
                field.type = "text";
                icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
            } else {
                field.type = "password";
                icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
            }
        }

        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
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
    </script>
@endsection
