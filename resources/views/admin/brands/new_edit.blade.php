@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-sm-8 m-auto">
                        <div class="card">

                            <form action="" method="POST" id="editBrandForm">
                                <div class="card-body">
                                    <div class="card-header-2">
                                        <h5>Edit Brand Information</h5>
                                    </div>

                                    <div class="theme-form theme-form-2 mega-form">
                                        <div class="mb-4 row align-items-center">
                                            <label class="form-label-title col-sm-3 mb-0">Brand Name</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="text" placeholder="Brands Name"
                                                    id="name" name="name" value="{{ $brands->name }}">
                                                <p class="invalid-feedback"></p>

                                            </div>
                                        </div>

                                        <div class="mb-4 row align-items-center">
                                            <label class="form-label-title col-sm-3 mb-0">Slug</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="text" placeholder="Brands Slug"
                                                    id="slug" name="slug" readonly value="{{ $brands->slug }}">
                                                <p class="invalid-feedback"></p>

                                            </div>
                                        </div>

                                        <div class="mb-4 row align-items-center">
                                            <label class="form-label-title col-sm-3 mb-0">Status</label>
                                            <div class="col-sm-9">
                                                <select name="status" id="status" class="form-control" name="status">
                                                    <option {{ $brands->status == 1 ? 'selected' : '' }} value="1">
                                                        Active</option>
                                                    <option {{ $brands->status == 0 ? 'selected' : '' }} value="0">
                                                        Inactive</option>
                                                </select>
                                                <p class="invalid-feedback"></p>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-submit-button">
                                    <button class="btn btn-animation ms-auto" type="submit">Submit</button>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
 <script>
        $("#editBrandForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('brands.update', $brands->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {


                          Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Brands Updated successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('brands.index') }}";
                        }, 2000);


                        $("#name").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#slug").removeClass('is-invalid')
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

                        if (errors['slug']) {
                            $("#slug").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['slug']);
                        } else {
                            $("#slug").removeClass('is-invalid')
                                .siblings('.invalid-feedback').html('');
                        }

                    }



                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });

        $("#name").change(function() {
            element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('getSlug') }}',
                type: 'get',
                data: {
                    title: element.val()
                },
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response["status"] == true) {
                        $("#slug").val(response["slug"]);
                    }

                }
            });

        });
    </script>
@endsection
