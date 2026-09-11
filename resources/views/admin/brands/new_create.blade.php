@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('brands.index') }}">Brands</a></li>
                    <li class="is-active">Add Brand</li>
                </ul>
                <h4 class="a-page-title">Add Brand</h4>
                <p class="a-page-desc">Create a new product brand.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Brand Information</h5>
            </div>
            <div class="a-card-body">
                <form action="" method="POST" id="createBrandForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label a-required">Brand Name</label>
                                <input class="form-control" type="text" placeholder="Brand Name"
                                    id="name" name="name">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input class="form-control" type="text" placeholder="Slug"
                                    id="slug" name="slug" readonly>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button class="btn btn-theme" type="submit">Create</button>
                        <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#createBrandForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('brands.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {
                        
                        window.location.href = "{{ route('brands.index') }}";


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
