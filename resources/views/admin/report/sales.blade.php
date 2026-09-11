@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sales Report</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <form action="{{ route('admin.reports.sales.export') }}" method="GET" class="d-inline">
                        <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                        <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <button class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</button>
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
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
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
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Subtotal</th>
                                <th>Shipping</th>
                                <th>Discount</th>
                                <th>Grand Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $index => $order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>#{{ $order->order_id }}</td>
                                    <td>{{ $order->name }}</td>
                                    <td>{{ $order->phone }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                        @if ($order->status == 'completed') bg-success 
                                        @elseif($order->status == 'pending') bg-warning 
                                        @elseif($order->status == 'cancelled') bg-danger 
                                        @else bg-secondary @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($order->subtotal, 2) }} ৳</td>
                                    <td>{{ number_format($order->shipping, 2) }} ৳</td>
                                    <td>{{ number_format($order->discount, 2) }} ৳</td>
                                    <td><strong>{{ number_format($order->grand_total, 2) }} ৳</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if ($orders->count() > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-right">Total Sales:</th>
                                    <th colspan="2">{{ number_format($orders->sum('grand_total'), 2) }} ৳</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>

                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        // Optional: add JS for exporting or additional filtering
    </script>
@endsection
