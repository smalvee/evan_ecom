@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="is-active">Edit Product</li>
                </ul>
                <h4 class="a-page-title">Edit Product</h4>
                <p class="a-page-desc">Update product information.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <form action="" method="post" name="createProducts" id="createProducts">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>General</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="mb-3">
                                <label for="title" class="form-label a-required">Title</label>
                                <input value="{{ $product->title }}" type="text" name="title"
                                    id="title" class="form-control" placeholder="Title" required>
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input value="{{ $product->slug }}" type="text" readonly name="slug"
                                    id="slug" class="form-control" placeholder="Slug" required>
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" cols="30" rows="10"
                                    class="summernote" placeholder="Description">{{ $product->description }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Media</h5>
                        </div>
                        <div class="a-card-body">
                            <div id="image" class="dropzone dz-clickable"
                                style="border: 2px dashed #6c757d; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer;">
                                <div class="dz-message needsclick">Drop files here or click to upload.</div>
                            </div>

                            <div class="row g-3 mt-1" id="product-gallery">
                                @if ($productImages->isNotEmpty())
                                    @foreach ($productImages as $productImage)
                                        <div class="col-6 col-md-3" id="image-row-{{ $productImage->id }}">
                                            <div class="card shadow-sm">
                                                <input type="hidden" name="image_array[]"
                                                    value="{{ $productImage->id }}">
                                                <img class="card-img-top"
                                                    src="{{ asset('uploads/products/small/' . $productImage->image) }}"
                                                    alt="image" style="height:150px;object-fit:cover;">
                                                <div class="card-body text-center">
                                                    <a href="javascript:void(0)"
                                                        onclick="deleteImage({{ $productImage->id }})"
                                                        class="btn btn-danger btn-sm">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Pricing</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="mb-3">
                                <label for="price" class="form-label a-required">Price</label>
                                <input value="{{ $product->price }}" type="text" name="price"
                                    id="price" class="form-control" placeholder="Price" required>
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="mb-3">
                                <label for="compare_price" class="form-label">Compare at Price</label>
                                <input value="{{ $product->compare_price }}" type="text"
                                    name="compare_price" id="compare_price" class="form-control"
                                    placeholder="Compare Price">
                                <p class="text-muted mt-3 mb-0">
                                    To show a reduced price, move the product's original price into
                                    Compare at price. Enter a lower value into Price.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Inventory</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label a-required">SKU (Stock Keeping Unit)</label>
                                        <input value="{{ $product->sku }}" type="text" name="sku"
                                            id="sku" class="form-control" placeholder="sku" required>
                                        <p class="invalid-feedback"></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="barcode" class="form-label">Barcode</label>
                                        <input value="{{ $product->barcode }}" type="text"
                                            name="barcode" id="barcode" class="form-control"
                                            placeholder="Barcode">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="track_qty" value="No">
                                            <input class="form-check-input" type="checkbox" id="track_qty"
                                                name="track_qty" value="Yes"
                                                {{ $product->track_qty == 'Yes' ? 'checked' : '' }}>
                                            <label for="track_qty" class="form-check-label">Track
                                                Quantity</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="qty" class="form-label a-required">Quantity</label>
                                        <input value="{{ $product->qty }}" type="number" min="0"
                                            name="qty" id="qty" class="form-control"
                                            placeholder="Qty" required>
                                        <p class="invalid-feedback"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Product status</h5>
                        </div>
                        <div class="a-card-body">
                            <select name="status" id="status" class="form-select">
                                <option {{ $product->status == 1 ? 'selected' : '' }} value="1">Active
                                </option>
                                <option {{ $product->status == 0 ? 'selected' : '' }} value="0">Block
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Place In Offer Zone</h5>
                        </div>
                        <div class="a-card-body">
                            <select name="is_offered" id="is_offered" class="form-select">
                                <option value="">Select One</option>
                                <option {{ $product->is_offered == 1 ? 'selected' : '' }} value="1">Yes
                                </option>
                                <option {{ $product->is_offered == 0 ? 'selected' : '' }} value="0">No
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Product category</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="mb-3">
                                <label for="category" class="form-label a-required">Category</label>
                                <select name="category" id="category" class="form-select" required>
                                    <option value="">Select a Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="mb-3">
                                <label for="sub_category" class="form-label">Sub category</label>
                                <select name="sub_category" id="sub_category" class="form-select">
                                    <option value="">Select a Sub Category</option>
                                    @foreach ($subCategories as $subCategory)
                                        <option value="{{ $subCategory->id }}"
                                            {{ $product->sub_category_id == $subCategory->id ? 'selected' : '' }}>
                                            {{ $subCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Product brand</h5>
                        </div>
                        <div class="a-card-body">
                            <select name="brand" id="brand" class="form-select">
                                <option value="">Select a Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Featured product</h5>
                        </div>
                        <div class="a-card-body">
                            <select name="is_featured" id="is_featured" class="form-select">
                                <option value="No"
                                    {{ $product->is_featured == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes"
                                    {{ $product->is_featured == 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="btn btn-theme">Save Changes</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        $("#createProducts").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);

            $.ajax({
                url: '{{ route('products.update', $product->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Product updated successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.href = "{{ route('products.index') }}";
                        }, 1500);
                    } else {
                        var errors = response.errors || {};
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').html('');
                        $.each(errors, function(key, value) {
                            var input = $('#' + key);
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').html(value);
                        });
                    }
                },
                error: function() {
                    console.log("Something went wrong");
                },
                complete: function() {
                    $("button[type=submit]").prop('disabled', false);
                }
            });
        });

        $('#category').change(function() {
            var category_id = $(this).val();
            $.ajax({
                url: '{{ route('pruducts-sub-category.index') }}',
                type: 'get',
                data: { category_id: category_id },
                dataType: 'json',
                success: function(response) {
                    $('#sub_category').find("option").not(":first").remove();
                    if (response.subCategory && response.subCategory.length > 0) {
                        $.each(response.subCategory, function(key, item) {
                            $('#sub_category').append($('<option>', {
                                value: item.id,
                                text: item.name
                            }));
                        });
                    }
                },
                error: function() {
                    console.log('Something went wrong');
                }
            });
        });

        Dropzone.autoDiscover = false;
        const dropzone = new Dropzone("#image", {
            url: "{{ route('product-images.update') }}",
            maxFiles: 10,
            paramName: 'image',
            params: { 'product_id': '{{ $product->id }}' },
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                if (response && response.ImagePath) {
                    let html = `
                    <div class="col-6 col-md-3" id="image-row-${response.image_id}">
                        <div class="card shadow-sm">
                            <input type="hidden" name="image_array[]" value="${response.image_id}">
                            <img class="card-img-top" src="${response.ImagePath}" alt="image" style="height:150px;object-fit:cover;">
                            <div class="card-body text-center">
                                <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </div>
                    </div>`;
                    $("#product-gallery").append(html);
                }
            },
            complete: function(file) {
                this.removeFile(file);
            }
        });

        function deleteImage(id) {
            $("#image-row-" + id).remove();
            if (confirm("Are you sure to delete Image")) {
                $.ajax({
                    url: '{{ route('product-images.delete') }}',
                    type: 'delete',
                    data: { id: id },
                    success: function(response) {
                        alert(response.message);
                    }
                });
            }
        }

        $("#title").change(function() {
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('getSlug') }}',
                type: 'get',
                data: { title: element.val() },
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response.status === true) {
                        $("#slug").val(response.slug);
                    }
                }
            });
        });
    </script>
@endsection
