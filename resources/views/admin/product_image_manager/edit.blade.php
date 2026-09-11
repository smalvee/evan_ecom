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
        <div class="row">
            <div class="col-sm-8 m-auto">
                <div class="card">
                    <form method="POST" id="productImage" name="productImage">
                        @csrf
                        <div class="card-body">

                            <h5 class="mb-3">Product Image Manager</h5>

                            <!-- ================= THUMB IMAGE ================= -->
                            <div class="border p-3 rounded mb-4">
                                <h6>Product SKU</h6>

                                <div class="input-group">
                                    <input type="text" class="form-control" id="variant_sku" name="variant_sku"
                                        value="{{ $product_info->sku }}" readonly>
                                    <input type="hidden" class="form-control" id="variant_id" name="variant_id"
                                        value="{{ $product_info->id }}">

                                </div>
                                <br>
                                @php
                                    $thumb_image = ProductImage::where('product_id', $product_info->id)
                                        ->where('is_thumb', 1)
                                        ->first();
                                    $gallery_images = ProductImage::where('product_id', $product_info->id)
                                        ->where('is_thumb', 0)
                                        ->get();
                                @endphp

                                <div class="input-group">

                                    <h6>Product Thumbnail</h6>

                                    <div class="input-group" style="padding-top: 10px;">


                                        @if (!empty($thumb_image))
                                            <div class="col-md-3 col-sm-4 col-6">
                                               


                                                    <!-- Thumb Image -->
                                                    <img src="{{ asset('uploads/products/thumb/' . $thumb_image->image) }}"
                                                        class="card-img-top" style="width: 100%;" alt="Product">


                                               
                                            </div>
                                        @endif

                                    </div>
                                </div>

                                <div class="input-group" style="padding-top:10px">

                                    <h6>Product Galary</h6>

                                    <div class="row g-3 pt-3">
                                        @if (!empty($gallery_images))
                                            @foreach ($gallery_images as $gallery_image)
                                                <div class="col-md-3 col-sm-4 col-6">
                                                    
                                                        <!--  Image -->
                                                        <img src="{{ asset('uploads/products/small/' . $gallery_image->image) }}"
                                                            class="card-img-top" style="width: 100%;"
                                                            alt="Product">

                                                        <!-- Button at Bottom -->
                                                        <div class="card-body p-2 text-center">

                                                            <button type="button" class="btn btn-danger btn-sm w-100"
                                                                onclick="deleteGaleryImage({{ $gallery_image->id }})">
                                                                Delete
                                                            </button>

                                                        </div>

                                                   
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>


                                </div>
                            </div>

                            <!-- ================= THUMB IMAGE ================= -->
                            <div class="border p-3 rounded mb-4">
                                <h6>Product Thumbnail</h6>

                                <div class="input-group">
                                    <input type="file" class="form-control" id="thumb_image" accept="image/*">
                                    <p class="btn btn-success" id="thumb_upload_btn">Upload</p>
                                </div>

                                <div id="thumb_preview" class="mt-2"></div>
                                <div id="thumb_gallery" class="row mt-3"></div>
                                <input type="hidden" name="thumb_image_id" id="thumb_image_id">

                            </div>

                            <!-- ================= GALLERY IMAGES ================= -->
                            <div class="border p-3 rounded mb-4">
                                <h6>Product Gallery Images</h6>

                                <div class="input-group">
                                    <input type="file" class="form-control" id="gallery_images" multiple
                                        accept="image/*">
                                    <p class="btn btn-success" id="gallery_upload_btn">Upload</p>
                                </div>

                                <div id="gallery_preview" class="mt-2"></div>
                                <div id="gallery_container" class="row mt-3"></div>
                            </div>

                            <button type="submit" class="btn btn-primary d-none" id="save_btn">Save</button>

                        </div>
                    </form>
                </div>
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
