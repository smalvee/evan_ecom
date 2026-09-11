@extends('admin.layouts.new_app')

@section('content')
    <?php
    use App\Models\NewProduct;
    use App\Models\Unit;
    use App\Models\Category;
    use App\Models\SubCategory;
    use App\Models\OrderItem;
    
    ?>
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h2>Item Wise Sales Report</h2>
                </div>
                <div class="col-sm-6 text-right">
                    <form action="{{ route('admin.reports.sales.export') }}" method="GET" class="d-inline">
                        <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                        <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        {{-- <button style="float: right" class="btn btn-success"><i class="fas fa-file-excel"></i> Export
                            Excel</button> --}}
                    </form>

                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Filter -->
            <div class="card mb-3">
                <div class="card-header">
                    <form method="GET" action="{{ route('sales.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-3" style="pointer-events: none">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value=""></option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                    Processing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Variant SKU</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Unit</th>
                                <th>Qty Sold</th>
                                <th>Rate</th>
                                <th>Gross Sales</th>
                                <th>Discount</th>
                                <th>Net Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($product_variant as $index => $variant)
                                @php
                                    $product_info = NewProduct::where('id', $variant->product_id)->first();
                                    $get_unit = Unit::where('id', $product_info->unit_id)->first();
                                    $get_cat = Category::where('id', $product_info->cat_id)->first();
                                    $get_sub_cat = SubCategory::where('id', $product_info->sub_cat_id)->first();

                                    $total_sold_qty = OrderItem::where('product_id', $variant->id)->sum('qty');
                                    $total_discount = OrderItem::where('product_id', $variant->id)->sum('discount');

                                    $gross_sale = $variant->selling_price * $total_sold_qty

                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product_info->name }}</td>
                                    <td>{{ $variant->sku }}</td>
                                    <td>{{ $get_cat->name }}</td>
                                    <td>{{ $get_sub_cat->name }}</td>
                                    <td>{{ $get_unit->name }}</td>
                                    <td>{{ $total_sold_qty }}</td>
                                    <td>{{ $variant->selling_price }}</td>
                                    <td>{{ $gross_sale }}</td>
                                    <td>{{ $total_discount }}</td>
                                    <td>{{ $gross_sale - $total_discount }}</td>


                                    {{-- <td>{{ number_format($variant->subtotal, 2) }} ৳</td>
                                    <td>{{ number_format($variant->shipping, 2) }} ৳</td>
                                    <td>{{ number_format($variant->discount, 2) }} ৳</td>
                                    <td><strong>{{ number_format($variant->grand_total, 2) }} ৳</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($variant->created_at)->format('d M Y') }}</td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                        {{-- @if ($orders->count() > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-right">Total Sales:</th>
                                    <th colspan="2">{{ number_format($orders->sum('grand_total'), 2) }} ৳</th>
                                </tr>
                            </tfoot>
                        @endif --}}
                    </table>

                    <div class="mt-3">
                        {{ $product_variant->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
@endsection
