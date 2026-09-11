@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Product</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="" method="post" name="createProducts" id="createProducts">
                @csrf
                <div class="row">
                    <!-- LEFT SIDE -->
                    <div class="col-md-8">
                        <!-- Product Info -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title">Title</label>
                                    <input value="{{ $product->title }}" type="text" name="title" id="title"
                                        class="form-control" placeholder="Title" required>
                                    <p class="invalid-feedback"></p>
                                </div>

                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input value="{{ $product->slug }}" type="text" readonly name="slug"
                                        id="slug" class="form-control" placeholder="Slug" required>
                                    <p class="invalid-feedback"></p>
                                </div>

                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                        placeholder="Description">{{ $product->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Media -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Media</h2>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-gallery">
                            @if ($productImages->isNotEmpty())
                                @foreach ($productImages as $productImage)
                                    <div class="col-md-3" id="image-row-{{ $productImage->id }}">
                                        <div class="card">
                                            <input type="hidden" name="image_array[]" value="{{ $productImage->id }}}">
                                            <img class="card-img-top"
                                                src="{{ asset('uploads/products/small/' . $productImage->image) }}"
                                                alt="image">
                                            <div class="card-body">
                                                <a href="javascript:void(0)" onclick="deleteImage({{ $productImage->id }})"
                                                    class="btn btn-danger">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Pricing -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Pricing</h2>
                                <div class="mb-3">
                                    <label for="price">Price</label>
                                    <input value="{{ $product->price }}" type="text" name="price" id="price"
                                        class="form-control" placeholder="Price" required>
                                    <p class="invalid-feedback"></p>
                                </div>

                                <div class="mb-3">
                                    <label for="compare_price">Compare at Price</label>
                                    <input value="{{ $product->compare_price }}" type="text" name="compare_price"
                                        id="compare_price" class="form-control" placeholder="Compare Price">
                                    <p class="text-muted mt-3">
                                        To show a reduced price, move the product’s original price into Compare at price.
                                        Enter a lower value into Price.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Inventory</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku">SKU (Stock Keeping Unit)</label>
                                            <input value="{{ $product->sku }}" type="text" name="sku" id="sku"
                                                class="form-control" placeholder="sku" required>
                                            <p class="invalid-feedback"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode">Barcode</label>
                                            <input value="{{ $product->barcode }}" type="text" name="barcode"
                                                id="barcode" class="form-control" placeholder="Barcode">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="track_qty" value="No">
                                                <input class="custom-control-input" type="checkbox" id="track_qty"
                                                    name="track_qty" value="Yes"
                                                    {{ $product->track_qty == 'Yes' ? 'checked' : '' }}>
                                                <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <input value="{{ $product->qty }}" type="number" min="0"
                                                name="qty" id="qty" class="form-control" placeholder="Qty" required>
                                            <p class="invalid-feedback"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="col-md-4">
                        <!-- Status -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product status</h2>
                                <select name="status" id="status" class="form-control">
                                    <option {{ $product->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ $product->status == 0 ? 'selected' : '' }} value="0">Block</option>
                                </select>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Place In Offer Zone</h2>
                                <select name="is_offered" id="is_offered" class="form-control">
                                    <option>Select One</option>
                                    <option {{ $product->is_offered == 1 ? 'selected' : '' }} value="1">Yes
                                    </option>
                                    <option {{ $product->is_offered == 0 ? 'selected' : '' }} value="0">No
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="card">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product category</h2>
                                <div class="mb-3">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control" required>
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
                                    <label for="sub_category">Sub category</label>
                                    <select name="sub_category" id="sub_category" class="form-control">
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

                        <!-- Brand -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product brand</h2>
                                <select name="brand" id="brand" class="form-control">
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

                        <!-- Featured -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Featured product</h2>
                                <select name="is_featured" id="is_featured" class="form-control">
                                    <option value="No" {{ $product->is_featured == 'No' ? 'selected' : '' }}>No
                                    </option>
                                    <option value="Yes" {{ $product->is_featured == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        // Ajax form submit
        $("#createProducts").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);

            $.ajax({
                url: '{{ route('products.update', $product->id) }}', // kept unchanged
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === true) {
                        window.location.href = "{{ route('products.index') }}";
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

        // Subcategory based on category
        $('#category').change(function() {
            var category_id = $(this).val();
            $.ajax({
                url: '{{ route('pruducts-sub-category.index') }}', // kept unchanged
                type: 'get',
                data: {
                    category_id: category_id
                },
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

        // Dropzone
        Dropzone.autoDiscover = false;
        const dropzone = new Dropzone("#image", {
            url: "{{ route('product-images.update') }}", // kept unchanged
            maxFiles: 10,
            paramName: 'image',
            params: {
                'product_id': '{{ $product->id }}'
            },
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                if (response && response.ImagePath) {
                    let html = `
                    <div class="col-md-3" id="image-row-${response.image_id}">
                        <div class="card">
                            <input type="hidden" name="image_array[]" value="${response.image_id}">
                            <img class="card-img-top" src="${response.ImagePath}" alt="image">
                            <div class="card-body">
                                <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger">Delete</a>
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
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status == true) {
                            alert(response.message);
                        } else {
                            alert(response.message)
                        }

                    }
                });
            }
        }

        // Auto-generate slug
        $("#title").change(function() {
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('getSlug') }}', // kept unchanged
                type: 'get',
                data: {
                    title: element.val()
                },
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
