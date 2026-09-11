@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Order List</h5>
                            <form action="" method="GET">
                                <div style="position: relative; display: inline-block;">
                                    <input type="text" class="form-control" {{-- style="border-radius: 5px; border-color: #028e84; padding-right: 35px;" --}} placeholder="search"
                                        value="{{ Request::get('keyword') }}" name="keyword">
                                    <button type="submit"
                                        style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>


                        </div>
                        <div>
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table class="table order-table theme-table"
                                    style="width:100%;font-size:13px;border-collapse:collapse;" id="table_id">

                                    <thead>
                                        <tr>
                                            <th style="padding:6px 8px;white-space:nowrap;">Date</th>
                                            <th style="padding:6px 8px;white-space:nowrap;">Order #</th>
                                            <th style="padding:6px 8px;text-align:left;white-space:nowrap;">Customer</th>
                                            <th style="padding:6px 8px;text-align:left;white-space:nowrap;">Phone</th>
                                            <th style="padding:6px 8px;text-align:right;white-space:nowrap;">Total</th>
                                            <th style="padding:6px 8px;text-align:right;white-space:nowrap;">Admin Note</th>
                                            <th style="padding:6px 8px;white-space:nowrap;">Status</th>

                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td style="padding:6px 8px;white-space:nowrap;">
                                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}
                                                </td>
                                                <td style="padding:6px 8px;white-space:nowrap;">
                                                    <a href="{{ route('orders.details', $order->id) }}"
                                                        style="text-decoration:none;">
                                                        {{ $order->order_id }}
                                                    </a>
                                                </td>



                                                <td
                                                    style="padding:6px 8px;text-align:left;
                               max-width:160px;
                               overflow:hidden;
                               text-overflow:ellipsis;
                               white-space:nowrap;">
                                                    {{ $order->name }}
                                                </td>

                                                <td style="padding:6px 8px;text-align:left;white-space:nowrap;">
                                                    {{ $order->phone }}
                                                </td>

                                                <td
                                                    style="padding:6px 8px;text-align:right;font-weight:600;white-space:nowrap;">
                                                    ৳ {{ number_format($order->grand_total) }}
                                                </td>
                                                
                                                <td style="padding:6px 8px;white-space:nowrap; text-align: right">
                                                    {{ $order->admin_note }}
                                                </td>

                                                <td style="padding:6px 8px;white-space:nowrap; text-align: right">
                                                    @if ($order->status == 'pending')
                                                        <span
                                                            style="font-size:11px;padding:4px 7px;
                                         background:#dc3545;color:#fff;
                                         border-radius:4px;">
                                                            Pending
                                                        </span>
                                                    @elseif ($order->status == 'confirm')
                                                        <span
                                                            style="font-size:11px;padding:4px 7px;
                                         background:#0dcaf0;color:#000;
                                         border-radius:4px;">
                                                            Confirm
                                                        </span>
                                                    @elseif ($order->status == 'shipped')
                                                        <span
                                                            style="font-size:11px;padding:4px 7px;
                                         background:#198754;color:#fff;
                                         border-radius:4px;">
                                                            Shipped
                                                        </span>
                                                    @elseif ($order->status == 'cancell')
                                                        <span
                                                            style="font-size:11px;padding:4px 7px;
                                         background:#6c757d;color:#fff;
                                         border-radius:4px;">
                                                            Cancelled
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- <td style="padding:6px 8px;white-space:nowrap;">
                                                   {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
@endsection
