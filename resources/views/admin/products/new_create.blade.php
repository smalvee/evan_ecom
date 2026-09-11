@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-sm-8 m-auto">




                        <form action="" method="POST" id="save_product" name="save_product">
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-header-2">
                                        <h5>Product Information</h5>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="">Product Name</label>
                                        <input type="text" class="form-control" id="name" name="name">
                                        <p class="invalid-feedback"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Product slug</label>
                                        <input type="text" class="form-control" id="slug" name="slug" readonly>
                                        <p class="invalid-feedback"></p>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="">SKU</label>
                                        <input type="text" class="form-control" id="product_sku" name="product_sku">
                                        <p class="invalid-feedback"></p>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="">Unit</label>
                                        <select class="form-control" name="units_id" id="units_id">
                                            <option value="">Select a Units</option>

                                            @if ($units->isNotEmpty())
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <p class="invalid-feedback"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Brand</label>
                                        <select class="form-control" name="brand_id" id="brand_id">
                                            <option value="">Select a Brand</option>

                                            @if ($brands->isNotEmpty())
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <p class="invalid-feedback"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Category</label>
                                        <select class="form-control" name="category" id="category">
                                            <option value="">Select a Category</option>

                                            @if ($categories->isNotEmpty())
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <p class="invalid-feedback"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Sub Category</label>
                                        <select name="sub_category" id="sub_category" class="form-control">
                                            <option value="">Select a Sub Category</option>
                                        </select>
                                        <p class="invalid-feedback"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="card-header-2">
                                        <h5>Description</h5>
                                    </div>
                                    <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                        placeholder="Description"></textarea>
                                </div>
                            </div>


                            <div class="card">
                                <div class="card-body">
                                    <div class="card-header-2">
                                        <h5>Product variations</h5>
                                    </div>
                                    <div class="mb-4 row align-items-center">
                                        <label class="col-sm-3 col-form-label form-label-title">Product Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" id="product_type" name="product_type">
                                                <option value="0">Single</option>
                                                <option value="1">Variable</option>
                                            </select>
                                            <p class="invalid-feedback"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="card" id="product-variations-card" style="display:none;">
                                <div class="card-body">
                                    <div class="card-header-2">
                                        <h5>Product variations</h5>
                                    </div>
                                    @foreach ($variations as $variation)
                                        <div class="mb-4 row align-items-center variation-row">
                                            <label
                                                class="form-label-title col-sm-3 mb-0">{{ $variation->variations }}</label>
                                            <div class="col-sm-9">
                                                @php
                                                    $values = App\Models\VariationValues::where(
                                                        'variation_id',
                                                        $variation->id,
                                                    )->get();
                                                @endphp
                                                <input type="hidden" class="variation-id" value="{{ $variation->id }}">
                                                <input type="hidden" class="variation-name"
                                                    value="{{ $variation->variations }}">
                                                <select class="form-control variation-value">
                                                    <option value="">Select Value</option>
                                                    @foreach ($values as $value)
                                                        <option value="{{ $value->id }}"
                                                            data-value-name="{{ $value->value }}">
                                                            {{ $value->value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                    <a href="#" class="add-option btn btn-primary mt-3">Add Another Option</a>
                                </div>
                            </div>


                            <div class="card">
                                <div class="card-body">
                                    <div class="card-header-2">
                                        <h5>variation Table</h5>
                                    </div>
                                    <table class="table table-bordered" id="variation-table">
                                        <thead>
                                            <tr>
                                                <th>Product SKU</th>
                                                <th>Variation Values</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">Save Product</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        // get sub category base on category selected
        $('#category').change(function() {
            var category_id = $(this).val();

            $.ajax({
                url: '{{ route('pruducts-sub-category.index') }}',
                type: 'get',
                data: {
                    category_id: category_id
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response); // ✅ shows the object

                    // Clear old options except the first
                    $('#sub_category').find("option").not(":first").remove();

                    // Use correct key: subCategory (not subCategories)
                    if (response.subCategory && response.subCategory.length > 0) {
                        $.each(response.subCategory, function(key, item) {
                            $('#sub_category').append(
                                $('<option>', {
                                    value: item.id,
                                    text: item.name
                                })
                            );
                        });
                    } else {
                        console.log("No subcategories found");
                    }
                },
                error: function() {
                    console.log('Something went wrong');
                }
            });
        });
    </script>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <script>
        const productType = document.getElementById('product_type');
        const variationsCard = document.getElementById('product-variations-card');

        productType.addEventListener('change', function() {
            if (this.value === '1') {
                variationsCard.style.display = 'block';
            } else {
                variationsCard.style.display = 'none';
            }
        });

        // Optional: trigger change on page load to set initial state
        productType.dispatchEvent(new Event('change'));
    </script>

    <script>
        $(document).ready(function() {
            // Show/hide variations card based on product type
            $('#product_type').change(function() {
                if ($(this).val() == 1) {
                    $('#product-variations-card').show();
                } else {
                    $('#product-variations-card').hide();
                    $('#variation-table tbody').empty(); // clear table
                }
            });

            // Add variation option to table
            $(".add-option").click(function(e) {
                e.preventDefault();

                let productSku = $("#product_sku").val().trim();
                if (!productSku) {
                    alert("Please enter the product SKU!");
                    return;
                }

                let variationData = [];
                let variationJson = [];
                let skuParts = [productSku];

                $(".variation-row").each(function() {
                    let variationId = $(this).find(".variation-id").val();
                    let variationName = $(this).find(".variation-name").val();
                    let selectedOption = $(this).find(".variation-value option:selected");
                    let valueId = selectedOption.val();
                    let valueName = selectedOption.data("value-name");

                    if (valueId) {
                        variationData.push(`${variationName}->${valueName}`);
                        variationJson.push({
                            variation_id: variationId,
                            value_id: valueId
                        });
                        skuParts.push(valueName);
                    }
                });

                if (variationData.length === 0) {
                    alert("Please select at least one variation value!");
                    return;
                }

                let autoSku = skuParts.join('-');

                $("#variation-table tbody").append(`
            <tr>
                <td>
                    ${autoSku}
                    <input type="hidden" name="variation_sku[]" value="${autoSku}">
                </td>
                <td>
                    ${variationData.join(', ')}
                    <input type="hidden" name="variation_values[]" value='${encodeURIComponent(JSON.stringify(variationJson))}'>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm delete-row">Delete</button></td>
            </tr>
        `);

                $(".variation-value").val(""); // reset selects
            });

            // Delete variation row
            $(document).on("click", ".delete-row", function() {
                $(this).closest("tr").remove();
            });

            // Auto-generate slug from name
            $('#name').on('keyup', function() {
                let slug = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                $('#slug').val(slug);
            });
        });
    </script>



    <script>
        // for slug 
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






        $("#save_product").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            // $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('new_product.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    // $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {

                         Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Product created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('products.index') }}";
                        }, 2000);

                        
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
