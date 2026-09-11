@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="is-active">Image Manager</li>
                </ul>
                <h4 class="a-page-title">Product Image Manager</h4>
                <p class="a-page-desc">Manage product variant images.</p>
            </div>
            <div class="a-actions">
                <form action="" method="GET" class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control" style="width: auto;"
                        placeholder="Search products" value="{{ Request::get('keyword') }}" name="keyword">
                    <button type="submit" class="btn btn-outline-secondary"><i class="ri-search-line"></i></button>
                </form>
            </div>
        </div>

        <div class="a-card">
            <div class="table-responsive">
                <table class="table all-package theme-table table-product" id="table_id">
                    <thead>
                        <tr>

                            <th>Product Name</th>
                            <th>Variant Sku</th>
                            <th>Category</th>
                            <th>Sub Category</th>

                            <th>Option</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- @dd($products) --}}

                        @if (!empty($products))
                            @foreach ($products as $product_info)
                                <tr>
                                    <td>
                                        <div class="a-cell-main">{{ $product_info->product->name }}</div>
                                    </td>
                                    <td>{{ $product_info->sku }}</td>

                                    <td>{{ $product_info->product->cat_id }}</td>
                                    <td>{{ $product_info->product->sub_cat_id }}</td>

                                    <td>
                                        <div class="a-actions-cell">
                                            <a class="btn btn-theme btn-sm"
                                                href="{{ route('image_edit.edit', $product_info->id) }}">
                                                <i class="ri-image-edit-line"></i> Add Or Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No products found</h5>
                                        <p>No product variants available for image management.</p>
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
