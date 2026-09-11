@extends('front.layouts.new_app')

@section('content')
    <!-- Breadcrumb Section Start -->
    {{-- <section class="breadcrumb-section pt-0">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-contain">
                        <h2 class="mb-2">Log In</h2>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">
                                        <i class="fa-solid fa-house"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">Log In</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Breadcrumb Section End -->

    <!-- log in section start -->
    <section class="log-in-section section-b-space">
        <div class="container-fluid-lg w-100">
            <div class="row">
                <div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
                    <div class="image-contain">
                        <img src="{{ asset('new-front-assets/images/inner-page/sign-up.png') }}" class="img-fluid" alt="">
                    </div>
                </div>

                <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
                    <div class="log-in-box">
                        <div class="log-in-title">

                            <h4>Create New Account</h4>
                        </div>

                        <div class="input-box">
                            <form class="row g-4" action="{{ route('account.processRegister') }}" method="post"
                                name="registrationForm" id="registrationForm">
                                @csrf
                                <div class="col-12">
                                    <div class="form-floating theme-form-floating">
                                        <input id="name" name="name" type="text" class="form-control"
                                            id="fullname" placeholder="Full Name">
                                        <p class="invalid-feedback"></p>
                                        <label for="fullname">Full Name</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating theme-form-floating">
                                        <input type="tel" id="phone" name="phone" type="text"
                                            class="form-control" id="fullname" placeholder="Mobile Number">
                                        <p class="invalid-feedback"></p>
                                        <label for="fullname">Mobile Number</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating theme-form-floating">
                                        <input type="email" id="email" name="email" class="form-control"
                                            id="email" placeholder="Email Address">
                                        <p class="invalid-feedback"></p>
                                        <label for="email">Email Address</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating">
                                        <input type="password" id="password" name="password" class="form-control"
                                            id="password" placeholder="Password">
                                        <p class="invalid-feedback"></p>
                                        <label for="password">Password</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating">
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="form-control" id="password" placeholder="Confirm Password">
                                        <p class="invalid-feedback"></p>
                                        <label for="password">Confirm Password</label>
                                    </div>
                                </div>



                                <div class="col-12">
                                    <button class="btn btn-animation w-100" type="submit">Sign Up</button>
                                </div>
                            </form>
                        </div>




                        <div class="other-log-in">
                            <h6></h6>
                        </div>

                        <div class="sign-up-box">
                            <h4>Already have an account?</h4>
                            <a href="{{ route('account.userLogin') }}">Log In</a>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-7 col-xl-6 col-lg-6"></div>
            </div>
        </div>
    </section>
    <!-- log in section end -->
@endsection

@section('customJs')
    <script type="text/javascript">
        $("#registrationForm").submit(function(event) {
            event.preventDefault();
            // $("button[type=submit]").prop('disabled', true);

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
