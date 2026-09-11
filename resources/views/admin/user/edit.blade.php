@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit User</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('users.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="POST" id="categoryForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Name" value="{{ $user->name }}" required>
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="text" name="email" id="email" value="{{ $user->email }}" class="form-control"
                                        placeholder="Email">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="tel" name="phone" id="phone" class="form-control"
                                        placeholder="Mobile Number" value="{{ $user->phone }}" required>
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                   <select name="status" id="status" class="form-control">
                                        <option {{ ($user->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                        <option {{ ($user->status == 0) ? 'selected' : '' }} value="0">Block</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role">Role</label>
                                   <select name="role" id="role" class="form-control">
                                        <option {{ ($user->role == 2) ? 'selected' : '' }} value="2">Admin</option>
                                        <option {{ ($user->role == 1) ? 'selected' : '' }} value="1">Customer</option>
                                    </select>
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <div class="position-relative">
                                        <input type="password" name="password" id="password" class="form-control pe-5"
                                            placeholder="Password" required>
                                        <i class="bi bi-eye text-muted"
                                            style="cursor: pointer; position: absolute; right: 12px; top: 50%; transform: translateY(-50%);"
                                            onclick="togglePassword('password', this)"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                                    <div class="position-relative">
                                        <input type="password" name="confirm_password" id="confirm_password"
                                            class="form-control pe-5" placeholder="Confirm Password" required>
                                        <i class="bi bi-eye text-muted"
                                            style="cursor: pointer; position: absolute; right: 12px; top: 50%; transform: translateY(-50%);"
                                            onclick="togglePassword('confirm_password', this)"></i>
                                    </div>
                                    <p id="passwordError" class="text-danger small mt-1" style="display:none;">
                                        Passwords do not match
                                    </p>
                                </div>
                            </div>
                        </div>




                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#categoryForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('users.update', $user->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] == true) {

                        window.location.href = "{{ route('users.index') }}";

                        $("#email").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#phone").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');
                        
                        $("#role").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                    } else {
                        var errors = response['errors'];
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
                        
                         if (errors['role']) {
                            $("#role").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['role']);
                        } else {
                            $("#role").removeClass('is-invalid')
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
        // Toggle password visibility
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');

            if (field.type === "password") {
                field.type = "text";
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                field.type = "password";
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Check if passwords match in real time
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
