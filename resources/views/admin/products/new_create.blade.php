@extends('admin.layouts.new_app')

@section('content')
    @include('admin.products.partials.form-styles')

    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="is-active">Add Product</li>
                </ul>
                <h4 class="a-page-title">Add Product</h4>
                <p class="a-page-desc">Create a product and configure its basic information, variations and selling
                    options.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line"></i> Back to Products
                </a>
            </div>
        </div>

        <form id="save_product" method="POST">
            @csrf

            <div class="pf-grid">
                {{-- MAIN --}}
                <div class="pf-stack">
                    <div class="a-card">
                        <div class="a-card-body">
                            <div class="pf-head">
                                <span class="pf-ico"><i class="ri-information-line"></i></span>
                                <div>
                                    <h6>Basic Information</h6>
                                    <p>Set the product identity and category.</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label a-required" for="name">Product Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="e.g. Premium Cotton T-Shirt" autocomplete="off">
                                    <p class="invalid-feedback"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label a-required" for="product_sku">SKU</label>
                                    <input type="text" class="form-control" id="product_sku" name="product_sku"
                                        placeholder="e.g. TS-001" autocomplete="off">
                                    <small class="text-muted">Must be unique.</small>
                                    <p class="invalid-feedback"></p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="slug">Slug</label>
                                    <input type="text" class="form-control" id="slug" name="slug" readonly>
                                    <small class="text-muted">Generated automatically from the product name.</small>
                                    <p class="invalid-feedback"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label a-required" for="units_id">Unit</label>
                                    <select class="form-control" name="units_id" id="units_id">
                                        <option value="">Select a Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="invalid-feedback"></p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label a-required" for="brand_id">Brand</label>
                                    <select class="form-control js-select2" name="brand_id" id="brand_id">
                                        <option value="">Select a Brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="invalid-feedback"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label a-required" for="category">Category</label>
                                    <select class="form-control js-select2" name="category" id="category">
                                        <option value="">Select a Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="invalid-feedback"></p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label a-required" for="sub_category">Sub Category</label>
                                    <select name="sub_category" id="sub_category" class="form-control js-select2" disabled>
                                        <option value="">Select a Sub Category</option>
                                    </select>
                                    <p class="invalid-feedback"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="a-card">
                        <div class="a-card-body">
                            <div class="pf-head">
                                <span class="pf-ico"><i class="ri-file-text-line"></i></span>
                                <div>
                                    <h6>Description</h6>
                                    <p>Add product details customers should know.</p>
                                </div>
                            </div>
                            <textarea name="description" id="description" rows="8" class="summernote" placeholder="Description"></textarea>
                        </div>
                    </div>

                    <div class="a-card">
                        <div class="a-card-body">
                            <div class="pf-head">
                                <span class="pf-ico"><i class="ri-layout-grid-line"></i></span>
                                <div>
                                    <h6>Product Type</h6>
                                    <p>Choose how this product is configured.</p>
                                </div>
                            </div>

                            <div class="pf-types">
                                <div>
                                    <input type="radio" class="pf-type-input" name="product_type" id="type_single"
                                        value="0" checked>
                                    <label class="pf-type-card" for="type_single">
                                        <strong>Single Product</strong>
                                        <small>One SKU / product configuration.</small>
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" class="pf-type-input" name="product_type" id="type_variable"
                                        value="1">
                                    <label class="pf-type-card" for="type_variable">
                                        <strong>Variable Product</strong>
                                        <small>Multiple variants such as size, color, etc.</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="a-card" id="variationsSection" hidden>
                        <div class="a-card-body">
                            <div class="pf-head">
                                <span class="pf-ico"><i class="ri-list-check-2"></i></span>
                                <div>
                                    <h6>Product Variations</h6>
                                    <p>Configure options such as Color, Size, Storage, etc.</p>
                                </div>
                            </div>

                            <div id="variationBuilder"></div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="addVariationBtn">
                                <i class="ri-add-line"></i> Add variation
                            </button>
                        </div>
                    </div>

                    <div class="a-card" id="variantsCard" hidden>
                        <div class="a-card-body">
                            <div class="pf-head">
                                <span class="pf-ico"><i class="ri-price-tag-3-line"></i></span>
                                <div>
                                    <h6>Generated Variants <span class="text-muted">(<span
                                                id="variantCount">0</span>)</span></h6>
                                    <p>Each combination below becomes a sellable variant.</p>
                                </div>
                            </div>

                            <div class="table-responsive" id="variantTableWrap" hidden>
                                <table class="table all-package theme-table pf-variant-table">
                                    <thead>
                                        <tr>
                                            <th>Variant</th>
                                            <th>SKU</th>
                                            <th>Pre Order</th>
                                            <th>Status / Stock</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variantTableBody"></tbody>
                                </table>
                            </div>

                            <div id="variantEmpty" class="a-empty">
                                <div class="a-empty-icon"><i class="ri-price-tag-3-line"></i></div>
                                <h5>No variants yet</h5>
                                <p>Add a variation and select its values to generate variants.</p>
                            </div>

                            <p class="text-muted small mb-0 mt-3">
                                <i class="ri-information-line"></i> Price and stock are managed separately from this page.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR --}}
                <div class="pf-side">
                    <div class="a-card mb-3">
                        <div class="a-card-body">
                            <div class="pf-head">
                                <span class="pf-ico"><i class="ri-file-list-3-line"></i></span>
                                <div>
                                    <h6>Product Setup</h6>
                                    <p>Complete these steps to publish.</p>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0 small">
                                <li class="d-flex justify-content-between py-1"><span class="text-muted">Basic
                                        information</span><i class="ri-checkbox-circle-line text-success"></i></li>
                                <li class="d-flex justify-content-between py-1"><span class="text-muted">Product
                                        type</span><i class="ri-checkbox-circle-line text-success"></i></li>
                                <li class="d-flex justify-content-between py-1"><span
                                        class="text-muted">Variations</span><span id="summaryVariantCount">0</span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-body">
                            <div class="pf-head">
                                <span class="pf-ico"><i class="ri-settings-3-line"></i></span>
                                <div>
                                    <h6>Selling Options</h6>
                                    <p>Delivery and availability settings.</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="free_delivery"
                                        id="free_delivery" value="1">
                                    <label class="form-check-label" for="free_delivery">Free Delivery</label>
                                </div>
                                <small class="text-muted">Offer free delivery for this product.</small>
                            </div>

                            <div class="mb-3" id="singlePreorderWrap">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="allow_pre_order"
                                        id="allow_pre_order" value="1">
                                    <label class="form-check-label" for="allow_pre_order">Pre Order</label>
                                </div>
                                <small class="text-muted">Allow customers to order when stock is unavailable.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="status">Product Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label" for="hot_products">In Hot Products</label>
                                <select name="hot_products" id="hot_products" class="form-control">
                                    <option value="0" selected>Inactive</option>
                                    <option value="1">Active</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pf-savebar" id="productSaveBar">
                        <span class="pf-dirty" id="pfDirty"><span class="pf-dot"></span><span id="pfDirtyText">No
                                unsaved changes</span></span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="pfCancelBtn">Cancel</button>
                            <button type="submit" class="btn btn-theme" id="pfSaveBtn">
                                <i class="ri-save-3-line"></i> Create Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        window.PRODUCT_FORM_CONFIG = {
            mode: 'create',
            formId: 'save_product',
            type: '0',
            initialType: '0',
            variations: @json($variationData),
            existingVariants: [],
            urls: {
                store: '{{ route('new_product.store') }}',
                update: '',
                redirect: '{{ route('products.index') }}',
                subCategory: '{{ route('pruducts-sub-category.index') }}',
                getSlug: '{{ route('getSlug') }}',
            },
        };
    </script>

    @include('admin.products.partials.form-scripts')
@endsection
