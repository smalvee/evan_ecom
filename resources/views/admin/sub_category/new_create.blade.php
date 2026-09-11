@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('sub-categories.index') }}">Sub Categories</a></li>
                    <li class="is-active">Add Sub Category</li>
                </ul>
                <h4 class="a-page-title">Add Sub Category</h4>
                <p class="a-page-desc">Create a new product sub category.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('sub-categories.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Sub Category Information</h5>
            </div>
            <div class="a-card-body">
                <form action="" name="subCategoryForm" id="subCategoryForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label a-required">Category</label>
                                <select id="category" class="form-select" name="category">
                                    @if ($categories->isNotEmpty())
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label a-required">Sub Category Name</label>
                                <input class="form-control" type="text" placeholder="Sub Category Name"
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
                        <a href="{{ route('sub-categories.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#subCategoryForm").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('sub-categories.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {

                        window.location.href = "{{ route('sub-categories.index') }}";

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

                        if (errors['category']) {
                            $("#category").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['category']);
                        } else {
                            $("#category").removeClass('is-invalid')
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
