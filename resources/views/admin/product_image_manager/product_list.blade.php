@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title d-sm-flex d-block">
                            <h5>Products List</h5>
                            <form action="" method="GET">
                                <div style="position: relative; display: inline-block;">
                                    <input type="text" class=""
                                        style="border-radius: 5px; border-color: #028e84; padding-right: 35px;"
                                        placeholder="search" value="{{ Request::get('keyword') }}" name="keyword">
                                    <button type="submit"
                                        style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div>
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
                                                    <td>{{ $product_info->product->name }}</td>
                                                    <td>{{ $product_info->sku }}</td>

                                                    <td>{{ $product_info->product->cat_id }}</td>
                                                    <td>{{ $product_info->product->sub_cat_id }}</td>






                                                    <td>
                                                        <ul>
                                                            <li>
                                                                <a
                                                                    href="{{ route('image_edit.edit', $product_info->id) }}">
                                                                    <button class="btn btn-primary">Add Or Edit</button>
                                                                </a>
                                                            </li>



                                                            {{-- <li>
                                                                <button type="button" class="btn btn-link p-0"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#exampleModalToggle"
                                                                    onclick="deleteNewProduct({{ $product_info->id }})"
                                                                    @if ($product_info->type == 1) disabled @endif>
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </li> --}}

                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif



                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer clearfix">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                </div>
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
