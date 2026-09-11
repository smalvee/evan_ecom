@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-sm-8 m-auto">
                        <div class="card">
                            <div class="card-body">
                                <div class="title-header option-title">
                                    <h5>Add New Supplier</h5>
                                </div>


                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel">
                                        <form class="theme-form theme-form-2 mega-form" action="" method="POST"
                                            id="updateSupplier" name="updateSupplier">


                                            <div class="row">
                                                <div class="mb-4 row align-items-center">
                                                    <label class="form-label-title col-lg-2 col-md-3 mb-0">
                                                        Name</label>
                                                    <div class="col-md-9 col-lg-10">
                                                        <input class="form-control" type="text" id="name"
                                                            name="name" value="{{ $supplier_info->name }}">
                                                        <p class="invalid-feedback"></p>
                                                    </div>
                                                </div>

                                                <div class="mb-4 row align-items-center">
                                                    <label class="col-lg-2 col-md-3 col-form-label form-label-title">Email
                                                    </label>
                                                    <div class="col-md-9 col-lg-10">
                                                        <input class="form-control" type="email" id="email"
                                                            name="email" value="{{ $supplier_info->email }}">
                                                        <p class="invalid-feedback"></p>
                                                    </div>
                                                </div>

                                                <div class="mb-4 row align-items-center">
                                                    <label
                                                        class="col-lg-2 col-md-3 col-form-label form-label-title">Phone</label>
                                                    <div class="col-md-9 col-lg-10">
                                                        <input class="form-control" type="text" id="phone"
                                                            name="phone" value="{{ $supplier_info->phone }}">
                                                        <p class="invalid-feedback"></p>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center">
                                                    <label
                                                        class="col-lg-2 col-md-3 col-form-label form-label-title">Address</label>
                                                    <div class="col-md-9 col-lg-10">
                                                        <input class="form-control" type="text" id="address"
                                                            name="address" value="{{ $supplier_info->address }}">
                                                        <p class="invalid-feedback"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-submit-button">
                                                <a href="{{ route('supply.index') }}"><button
                                                        class="btn btn-animation ms-auto" type="button"
                                                        style="float: left">Back</button></a>
                                                <button class="btn btn-animation ms-auto" type="submit">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#updateSupplier").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            $.ajax({
                url: '{{ route('supply.update', $supplier_info->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    // $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Supplier updated successfully!',
                            timer: 1000,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('supply.index') }}";
                        }, 1000);


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
