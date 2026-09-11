@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('categories.index') }}">Categories</a></li>
                    <li class="is-active">Add Category</li>
                </ul>
                <h4 class="a-page-title">Add Category</h4>
                <p class="a-page-desc">Create a new product category.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Category Information</h5>
            </div>
            <div class="a-card-body">
                <form action="" method="POST" id="categoryForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label a-required">Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" id="slug" name="slug" readonly>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Images</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="product_image" accept="image/*">
                                    <button type="button" class="btn btn-theme" id="upload_btn">Upload</button>
                                </div>
                                <div id="image_preview" class="row g-2 mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <div id="product-gallery" class="row g-3 mt-3"></div>

                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-theme d-none" id="submit_btn">Submit</button>
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
            // $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('categories.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $("button[type=submit]").prop('disabled', true);


                    if (response["status"] == true) {

                        
                         Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Category created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('categories.index') }}";
                        }, 2000);

                        

                        $("#name").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#slug").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                    } else {
                        $("button[type=submit]").prop('disabled', false);

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const productInput = document.getElementById('product_image');
            const uploadBtn = document.getElementById('upload_btn');
            const previewDiv = document.getElementById('image_preview');
            const galleryDiv = document.getElementById('product-gallery');
            const submitBtn = document.getElementById('submit_btn');

            // -------------------------------
            // 1. Preview selected images
            // -------------------------------
            productInput.addEventListener('change', function() {
                previewDiv.innerHTML = '';
                [...this.files].forEach(file => {
                    if (!file.type.startsWith('image/')) return;

                    previewDiv.innerHTML += `
                <div class="col-6 col-md-3">
                    <img src="${URL.createObjectURL(file)}"
                         class="img-thumbnail"
                         style="height:150px;object-fit:cover">
                </div>
            `;
                });
            });

            // -------------------------------
            // 2. Upload images (ONE BY ONE)
            // -------------------------------
            uploadBtn.addEventListener('click', async function() {

                if (!productInput.files.length) {
                    alert('Please select images first');
                    return;
                }

                uploadBtn.disabled = true;
                const originalText = uploadBtn.innerText;
                uploadBtn.innerText = 'Uploading...';

                for (let file of productInput.files) {

                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const res = await fetch("{{ route('temp-images.create') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: formData
                        });

                        const data = await res.json();

                        if (data.status === true) {

                            galleryDiv.insertAdjacentHTML('beforeend', `
                    <div class="col-md-3" id="image-row-${data.image_id}">
                        <div class="card shadow-sm">
                            <input type="hidden" name="image_id" name="image_id" value="${data.image_id}">
                            <img src="${data.ImagePath}"
                                 class="card-img-top"
                                 style="height:150px;object-fit:cover">
                            <div class="card-body text-center">
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="deleteImage(${data.image_id})">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `);

                            submitBtn.classList.remove('d-none');
                        }

                    } catch (err) {
                        console.error('Upload failed:', err);
                    }
                }

                previewDiv.innerHTML = '';
                productInput.value = '';

                uploadBtn.disabled = false;
                uploadBtn.innerText = originalText;
            });
        });

        // -------------------------------
        // 3. Delete image
        // -------------------------------
        function deleteImage(imageId) {

            const row = document.getElementById(`image-row-${imageId}`);
            if (row) row.remove();

            fetch(`/admin/delete-temp-image/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            // Hide submit button if no images left
            const gallery = document.getElementById('product-gallery');
            if (gallery.children.length === 0) {
                document.getElementById('submit_btn').classList.add('d-none');
            }
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 1500,
                showConfirmButton: false
            });
        </script>
    @endif
@endsection
