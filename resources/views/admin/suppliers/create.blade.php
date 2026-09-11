@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('supply.index') }}">Suppliers</a></li>
                    <li class="is-active">Create</li>
                </ul>
                <h4 class="a-page-title">Add New Supplier</h4>
                <p class="a-page-desc">Fill in the details to create a new supplier.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('supply.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Supplier Information</h5>
            </div>
            <div class="a-card-body">
                <form action="" method="POST" id="createSupplier" name="createSupplier">
                    <div class="a-form-section">
                        <h6 class="a-section-title">Supplier</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label a-required">Name</label>
                                    <input class="form-control" type="text" id="name" name="name">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label a-required">Email</label>
                                    <input class="form-control" type="email" id="email" name="email">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label a-required">Phone</label>
                                    <input class="form-control" type="text" id="phone" name="phone">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label a-required">Address</label>
                                    <input class="form-control" type="text" id="address" name="address">
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button class="btn btn-theme" type="submit">Submit</button>
                        <a href="{{ route('supply.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#createSupplier").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            $.ajax({
                url: '{{ route('supply.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    // $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Supplier created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('supply.index') }}";
                        }, 2000);


                    } else {
                        var errors = response['errors'] || {};

                        // Clear old validation states
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').html('');

                        // Loop through errors and display them
                        $.each(errors, function(key, value) {
                            var input = $('#' + key); // Correct ID selector
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').html(
                                value); // Works if feedback div is right after input
                        });

                    }

                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });
    </script>
@endsection
