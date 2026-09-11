@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order: {{ $order->id }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header pt-3">
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    <h1 class="h5 mb-3">Shipping Address</h1>
                                    <address>
                                        <strong>{{ $order->name }}</strong><br>
                                        {{ $order->address }}<br>
                                        Phone: {{ $order->phone }}
                                    </address>
                                </div>



                                <div class="col-sm-4 invoice-col">
                                    <b>Invoice #{{ $order->id }}</b><br>
                                    <br>
                                    <b>Order ID:</b> {{ $order->id }}<br>
                                    <b>Total:</b> Tk: {{ $order->grand_total }}<br>
                                    <b>Status:</b>
                                    @if ($order->status == 'pending')
                                        <span class="text-danger">Pending</span>
                                    @elseif ($order->status == 'confirm')
                                        <span class="text-info">Confirm</span>
                                    @elseif ($order->status == 'shipped')
                                        <span class="text-success">Shipped</span>
                                    @elseif ($order->status == 'cancell')
                                        <span class="text-danger">Cancelled</span>
                                    @endif


                                    <br>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-3">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th width="100">Price</th>
                                        <th width="100">Qty</th>
                                        <th width="100">Discount</th>
                                        <th width="100">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($orderedItems))
                                        @foreach ($orderedItems as $orderedItem)
                                            <tr>
                                                <td>{{ $orderedItem->name }}</td>
                                                <td>Tk: {{ $orderedItem->price }}</td>
                                                <td>{{ $orderedItem->qty }}</td>
                                                <td>{{ $orderedItem->discount }}</td>
                                                <td>Tk: {{ $orderedItem->price * $orderedItem->qty }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="3" class="text-right">Subtotal:</th>
                                            <td>Tk: {{ $order->subtotal }}</td>
                                        </tr>

                                        <tr>
                                            <th colspan="3" class="text-right">Shipping:</th>
                                            <td>{{ $order->shipping }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">Discount:</th>
                                            <td>Tk: - {{ $order->discount }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">Grand Total:</th>
                                            <td>{{ $order->grand_total }}</td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Order Status</h2>
                            <form action="" method="post" name="statusForm" id="statusForm">
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>
                                            Shipped</option>
                                        <option value="confirm" {{ $order->status == 'confirm' ? 'selected' : '' }}>
                                            Confirmed</option>
                                        <option value="cancell" {{ $order->status == 'cancell' ? 'selected' : '' }}>
                                            Cancelled</option>

                                    </select>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="bg-danger" class="mb-3"> Notes from customer</h5>
                            <div class="mb-3">
                                <div>
                                    {{ $order->notes }}
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#statusForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);

            $.ajax({
                url: '{{ route('orders.changeStatus', $order->id) }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {



                    if (response["status"] == true) {

                        window.location.href = "{{ route('orders.details', $order->id) }}";

                    } 
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });
    </script>
@endsection
