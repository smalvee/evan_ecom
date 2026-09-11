@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('categories.index') }}">Categories</a></li>
                    <li class="is-active">Edit Category</li>
                </ul>
                <h4 class="a-page-title">Edit Category</h4>
                <p class="a-page-desc">Update this product category.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Edit Category</h5>
            </div>
            <div class="a-card-body">
                <form action="" method="POST" id="categoryForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label a-required">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Name" value="{{ $category->name }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" name="slug" id="slug" readonly class="form-control"
                                    placeholder="Slug" value="{{ $category->slug }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image Upload</label>
                                <input type="hidden" id="image_id" name="image_id" value="">
                                <div id="image" class="dropzone dz-clickable"
                                    style="border: 2px dashed #6c757d; border-radius: 8px; background: #f8f9fa; padding: 30px; text-align: center; cursor: pointer;">
                                    <div class="dz-message needsclick">
                                        <i class="ri-upload-cloud-2-line" style="font-size: 40px; color: #6c757d;"></i>
                                        <p class="mb-0 mt-2">Drag &amp; drop files here or click to browse</p>
                                    </div>
                                </div>
                            </div>
                            @if (!empty($category->image))
                                <div class="mb-3">
                                    <img width="200" src="{{ asset('uploads/category/thumb/' . $category->image) }}"
                                        alt="">
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option {{ $category->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ $category->status == 0 ? 'selected' : '' }} value="0">Block</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="btn btn-theme">Update</button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
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
                url: '{{ route('categories.update', $category->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Category updated successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.href = "{{ route('categories.index') }}";
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

        $("#name").change(function() {
            element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('getSlug') }}',
                type: 'get',
                data: { title: element.val() },
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response["status"] == true) {
                        $("#slug").val(response["slug"]);
                    }
                }
            });
        });

        Dropzone.autoDiscover = false;
        const dropzone = $("#image").dropzone({
            init: function() {
                this.on('addedfile', function(file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                });
            },
            url: "{{ route('temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                $("#image_id").val(response.image_id);
            }
        });
    </script>
@endsection
