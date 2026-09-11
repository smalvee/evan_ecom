@extends('admin.layouts.new_app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        img.preview-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-right: 6px;
            margin-bottom: 6px;
        }
    </style>
    <?php
    
    use App\Models\ProductImage;
    
    ?>

    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="is-active">Image Manager</li>
                </ul>
                <h4 class="a-page-title">Product Image Manager</h4>
                <p class="a-page-desc">Manage thumbnail and gallery images for this product variant.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8 m-auto">
                <form method="POST" id="productImage" name="productImage">
                    @csrf

                    <div class="a-card">
                        <div class="a-card-head">
                            <h5>Current Images</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="mb-3">
                                <label class="form-label" for="variant_sku">Product SKU</label>
                                <input type="text" class="form-control" id="variant_sku" name="variant_sku"
                                    value="{{ $product_info->sku }}" readonly>
                                <input type="hidden" class="form-control" id="variant_id" name="variant_id"
                                    value="{{ $product_info->id }}">
                            </div>

                            @php
                                $thumb_image = ProductImage::where('product_id', $product_info->id)
                                    ->where('is_thumb', 1)
                                    ->first();
                                $gallery_images = ProductImage::where('product_id', $product_info->id)
                                    ->where('is_thumb', 0)
                                    ->get();
                            @endphp

                            <div class="mb-4">
                                <h6 class="a-section-title">Product Thumbnail</h6>
                                @if (!empty($thumb_image))
                                    <div class="row">
                                        <div class="col-md-3 col-sm-4 col-6">
                                            <div class="a-card">
                                                <img src="{{ asset('uploads/products/thumb/' . $thumb_image->image) }}"
                                                    class="card-img-top" style="width: 100%;" alt="Product">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h6 class="a-section-title">Product Gallery</h6>
                                <div class="row g-3">
                                    @if (!empty($gallery_images))
                                        @foreach ($gallery_images as $gallery_image)
                                            <div class="col-md-3 col-sm-4 col-6">
                                                <div class="a-card">
                                                    <img src="{{ asset('uploads/products/small/' . $gallery_image->image) }}"
                                                        class="card-img-top" style="width: 100%;"
                                                        alt="Product">

                                                    <div class="card-body p-2 text-center">
                                                        <button type="button" class="btn btn-danger btn-sm w-100"
                                                            onclick="deleteGaleryImage({{ $gallery_image->id }})">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="a-card">
                        <div class="a-card-head">
                            <h5>Upload Thumbnail</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="input-group">
                                <input type="file" class="form-control" id="thumb_image" accept="image/*">
                                <button type="button" class="btn btn-theme" id="thumb_upload_btn">Upload</button>
                            </div>

                            <div id="thumb_preview" class="mt-2"></div>
                            <div id="thumb_gallery" class="row mt-3"></div>
                            <input type="hidden" name="thumb_image_id" id="thumb_image_id">
                        </div>
                    </div>

                    <div class="a-card">
                        <div class="a-card-head">
                            <h5>Upload Gallery Images</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="input-group">
                                <input type="file" class="form-control" id="gallery_images" multiple
                                    accept="image/*">
                                <button type="button" class="btn btn-theme" id="gallery_upload_btn">Upload</button>
                            </div>

                            <div id="gallery_preview" class="mt-2"></div>
                            <div id="gallery_container" class="row mt-3"></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-theme d-none" id="save_btn">Save</button>

                </form>
            </div>
        </div>
    </div>
@endsection


@section('customJs')
    <script>
        function initUploader(inputId, previewId, uploadBtnId, galleryId, type) {

            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const uploadBtn = document.getElementById(uploadBtnId);
            const gallery = document.getElementById(galleryId);
            const saveBtn = document.getElementById('save_btn');

            input.addEventListener('change', () => {
                preview.innerHTML = '';
                [...input.files].forEach(file => {
                    if (!file.type.startsWith('image/')) return;

                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.classList.add('preview-img');
                    preview.appendChild(img);
                });
            });

            uploadBtn.addEventListener('click', async () => {
                if (!input.files.length) {
                    alert('Please select image(s)');
                    return;
                }

                uploadBtn.disabled = true;
                uploadBtn.innerText = 'Uploading...';

                for (let file of input.files) {

                    let formData = new FormData();
                    formData.append('image', file);

                    try {
                        const response = await fetch("{{ route('temp-images.create') }}", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.status) {

                            // ✅ THUMB IMAGE
                            if (type === 'thumb') {
                                gallery.innerHTML = `
                            <div class="col-md-3 mb-3" id="image-row-${data.image_id}">
                                <img src="${data.ImagePath}" class="img-fluid rounded mb-1">
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteImage(${data.image_id}, 'thumb')">Delete</button>
                            </div>
                        `;

                                document.getElementById('thumb_image_id').value = data.image_id;
                            }

                            // ✅ GALLERY IMAGES
                            if (type === 'gallery') {
                                gallery.insertAdjacentHTML('beforeend', `
                            <div class="col-md-3 mb-3" id="image-row-${data.image_id}">
                                <input type="hidden" name="gallery_image_ids[]" value="${data.image_id}">
                                <img src="${data.ImagePath}" class="img-fluid rounded mb-1">
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteImage(${data.image_id}, 'gallery')">Delete</button>
                            </div>
                        `);
                            }

                            saveBtn.classList.remove('d-none');
                        }

                    } catch (error) {
                        console.error(error);
                    }
                }

                preview.innerHTML = '';
                input.value = '';
                uploadBtn.disabled = false;
                uploadBtn.innerText = 'Upload';
            });
        }


        // Initialize uploaders
        initUploader(
            'thumb_image',
            'thumb_preview',
            'thumb_upload_btn',
            'thumb_gallery',
            'thumb'
        );

        initUploader(
            'gallery_images',
            'gallery_preview',
            'gallery_upload_btn',
            'gallery_container',
            'gallery'
        );



        // ================= DELETE IMAGE =================
        function deleteImage(imageId, type) {

            document.getElementById(`image-row-${imageId}`)?.remove();

            if (type === 'thumb') {
                document.getElementById('thumb_image_id').value = '';
            }

            fetch(`/admin/delete-temp-image/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
        }
    </script>

    <script>
        $("#productImage").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            $.ajax({
                url: '{{ route('image.store') }}',
                type: 'post',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response["status"] == true) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Image Added successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        location.reload();

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


    <script>
        // ================= DELETE GALERY IMAGE =================
        function deleteGaleryImage(id) {
            var url = '{{ route('galery_image.destroy', 'ID') }}'.replace('ID', id);

            Swal.fire({
                title: 'Are you sure?',
                text: "This image will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === true) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Image Deleted!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                location.reload();

                                // Optional: remove image from DOM
                                // $('#image-row-' + id).remove();
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
