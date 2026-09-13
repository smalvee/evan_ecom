@extends('admin.layouts.new_app')

@section('content')
<?php   

use App\Models\Category;
use App\Models\SubCategory;

?>
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="is-active">All Products</li>
                </ul>
                <h4 class="a-page-title">Products</h4>
                <p class="a-page-desc">Manage your product catalog.</p>
            </div>
            <div class="a-actions">
                <form action="" method="GET" class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control" style="width: auto;"
                        placeholder="Search products" value="{{ Request::get('keyword') }}" name="keyword">
                    <button type="submit" class="btn btn-outline-secondary"><i class="ri-search-line"></i></button>
                </form>
                <a href="{{ route('products.create') }}" class="btn btn-theme"><i class="ri-add-line"></i> Add Product</a>
            </div>
        </div>

        <div class="a-card">
            <div class="table-responsive">
                <table class="table all-package theme-table table-product" id="table_id">
                    <thead>
                        <tr>

                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Current Qty</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- @dd($products) --}}

                        @if (!empty($products))
                            @foreach ($products as $product_info)
                            @php
                            $cat_info = Category::where('id', $product_info->cat_id)->first();
                            $sub_cat_info = SubCategory::where('id', $product_info->sub_cat_id)->first();
                            @endphp
                                <tr>
                                    <td>
                                        <div class="a-cell-main">{{ $product_info->name }}</div>
                                    </td>
                                    <td>{{ $product_info->sku }}</td>

                                    <td>{{ $cat_info->name }}</td>
                                    <td>{{ $sub_cat_info->name }}</td>

                                    {{-- Total quantity of all variants --}}
                                    <td>{{ $product_info->product_variation->sum('qty') }}</td>

                                    {{-- Minimum selling price among variants --}}
                                    <td class="td-price">
                                        {{ $product_info->product_variation->min('selling_price') }}
                                    </td>

                                    <td>
                                        @if ($product_info->status == 1)
                                            <span class="a-badge a-badge-success"><span class="dot"></span>Active</span>
                                        @else
                                            <span class="a-badge a-badge-secondary"><span class="dot"></span>Inactive</span>
                                        @endif

                                    </td>

                                    <td>
                                        <div class="a-actions-cell">
                                            <a class="a-action-btn"
                                                href="{{ route('admin.pricing.index', ['product_id' => $product_info->id]) }}"
                                                title="Manage Pricing"><i class="ri-price-tag-3-line"></i></a>
                                            <a class="a-action-btn" href="{{ route('new_products.edit', $product_info->id) }}"><i
                                                class="ri-pencil-line"></i></a>

                                            <button type="button" class="a-action-btn a-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#exampleModalToggle"
                                                onclick="deleteNewProduct({{ $product_info->id }})"
                                                @if ($product_info->type == 1) disabled @endif>
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No products found</h5>
                                        <p>Start by adding your first product to the catalog.</p>
                                        <a href="{{ route('products.create') }}" class="btn btn-theme btn-sm">Add Product</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="a-card-footer clearfix">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        function deleteNewProduct(id) {

            var url = '{{ route('new_product.destroy', 'ID') }}';
            var newUrl = url.replace("ID", id)

            if (confirm("Are you sure to delete this product")) {
                $.ajax({
                    url: newUrl,
                    type: 'delete',
                    data: {},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response["status"] == true) {
                            window.location.href = "{{ route('products.index') }}";
                        }
                    }
                })
            }
        }
    </script>
@endsection
