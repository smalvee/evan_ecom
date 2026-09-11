@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('purchase.index') }}">Purchases</a></li>
                    <li><a href="{{ route('purchase.return_list') }}">Returns</a></li>
                    <li class="is-active">View</li>
                </ul>
                <h4 class="a-page-title">Return Details</h4>
                <p class="a-page-desc">Items included in this purchase return.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('purchase.return_list') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Return Items</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="purchaseTable">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase_return_list as $item)
                            @php $variant_info = $item->variant; @endphp
                            <tr>
                                <td class="a-cell-main">{{ $variant_info->sku ?? '—' }}</td>
                                <td>{{ $variant_info->product->name ?? '—' }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ number_format((float) $item->unit_cost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('customJs')



@endsection
