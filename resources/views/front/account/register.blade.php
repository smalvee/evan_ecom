@extends('front.layouts.app')

@section('content')
    <div style="display:flex; justify-content:center; align-items:center; height:100vh; padding:20px;">
        <div
            style="background:#fff; max-width:450px; width:100%; border-radius:15px; padding:30px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">

            <h2 style="text-align:center; font-size:2.5rem; color:#FC8934; margin-bottom:25px;">Registration</h2>

            <form action="{{ route('account.processRegister') }}" method="post" name="registrationForm" id="registrationForm">

                <!-- Name -->
                <div style="margin-bottom:20px;">
                    <label for="name" style="display:block; margin-bottom:8px; font-weight:bold; color:#333;">Full
                        Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name"
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:1rem; outline:none; transition:0.3s;">
                    <p class="invalid-feedback"></p>
                </div>

                <!-- phone -->
                <div style="margin-bottom:20px;">
                    <label for="phone" style="display:block; margin-bottom:8px; font-weight:bold; color:#333;">Phone
                        Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number"
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:1rem; outline:none;">
                    <p class="invalid-feedback"></p>
                </div>

                <!-- Email -->
                <div style="margin-bottom:20px;">
                    <label for="email"
                        style="display:block; margin-bottom:8px; font-weight:bold; color:#333;">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email"
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:1rem; outline:none; transition:0.3s;">
                    <p class="invalid-feedback"></p>
                </div>

                <!-- Password -->
                <div style="margin-bottom:20px;">
                    <label for="password"
                        style="display:block; margin-bottom:8px; font-weight:bold; color:#333;">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password"
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:1rem; outline:none; transition:0.3s;">
                    <p class="invalid-feedback"></p>
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom:25px;">
                    <label for="password_confirmation"
                        style="display:block; margin-bottom:8px; font-weight:bold; color:#333;">Confirm
                        Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="Confirm your password"
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:1rem; outline:none; transition:0.3s;">
                    <p class="invalid-feedback"></p>
                </div>

                <!-- Button -->
                <div style="text-align:center;">
                    <button type="submit" class="form-control"
                        style="background:#FC8934; color: #fff;">Registration</button>
                </div>
            </form>

            <!-- Already have account -->
            <p style="text-align:center; margin-top:20px; font-size:0.95rem; color:#555;">
                Already have an account?
                <a href="{{ route('account.userLogin') }}"
                    style="color:#FC8934; font-weight:bold; text-decoration:none;">Login</a>
            </p>
        </div>
    </div>
@endsection

@section('customJs')
    <script type="text/javascript">
        $("#registrationForm").submit(function(event) {
            event.preventDefault();
            $("button[type=submit]").prop('disabled', true);

            $.ajax({
                url: '{{ route('account.processRegister') }}',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {

                    $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {

                        window.location.href = "{{ route('account.userLogin') }}";

                        $("#name").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#email").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#phone").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#password").removeClass('is-invalid')
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

                        if (errors['password']) {
                            $("#password").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['password']);
                        } else {
                            $("#password").removeClass('is-invalid')
                                .siblings('.invalid-feedback').html('');
                        }

                    }
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong", jqXHR.responseText);
                }
            });
        });
    </script>
@endsection
