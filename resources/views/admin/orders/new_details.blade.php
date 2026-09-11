@extends('admin.layouts.new_app')

@section('content')
    <?php
    
    use App\Models\ProductVariant;
    use App\Models\NewProduct;
    use App\Models\Order;
    
    $order_info = Order::where('id', $order->id)->first();
    
    ?>


    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Order: {{ $order_info->order_id }}</h3>
                </div>
                <div class="col-sm-2 text-right">


                    <button class="btn btn-primary" style="float:right;"
                        onclick="location.href='{{ route('front.invoice', $order_info->order_id) }}'">
                        Invoice
                    </button>


                </div>
                <div class="col-sm-2 text-right">


                    <button class="btn btn-primary" style="float:right;"
                        onclick="location.href='{{ route('orders.index') }}'">
                        Back
                    </button>

                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header pt-3">
                            <div class="row invoice-info g-4">

                                <!-- Shipping Address -->
                                <div class="col-md-6">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-header">
                                            <h6 class="mb-0 fw-semibold">Shipping Address</h6>
                                        </div>

                                        <div class="card-body">
                                            <form action="" method="post" name="addressForm" id="addressForm">
                                                @csrf

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Full Name</label>
                                                    <input type="text" class="form-control" value="{{ $order->name }}"
                                                        id="f_name" name="f_name" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Address</label>
                                                    <textarea id="address" name="address" class="form-control" rows="3">{{ $order->address }}</textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Phone</label>
                                                    <input type="text" class="form-control" value="{{ $order->phone }}"
                                                        readonly>
                                                </div>

                                                <input type="hidden" value="{{ $order->user_id }}" name="cus_id"
                                                    id="cus_id">

                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-dark px-4">
                                                        Update Address
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Invoice Info -->
                                <div class="col-md-6">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-header">
                                            <h6 class="mb-0 fw-semibold">Invoice Details</h6>
                                        </div>

                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <strong>Invoice #:</strong> {{ $order_info->order_id }}
                                                </li>
                                                <li class="mb-2">
                                                    <strong>Date:</strong> {{ $order_info->created_at->format('Y-m-d') }}
                                                </li>

                                                <li class="mb-2">
                                                    <strong>Time:</strong> {{ $order_info->created_at->format('h:i A') }}
                                                </li>
                                                <li class="mb-2">
                                                    <strong>Order ID:</strong> {{ $order->id }}
                                                </li>
                                                <li class="mb-2">
                                                    <strong>Total:</strong>
                                                    <span class="fw-semibold">Tk
                                                        {{ number_format($order->grand_total, 2) }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <strong>Status:</strong>
                                                    @if ($order->status == 'pending')
                                                        <span class="badge bg-danger">Pending</span>
                                                    @elseif ($order->status == 'confirm')
                                                        <span class="badge bg-info">Confirmed</span>
                                                    @elseif ($order->status == 'shipped')
                                                        <span class="badge bg-success">Shipped</span>
                                                    @elseif ($order->status == 'cancell')
                                                        <span class="badge bg-secondary">Cancelled</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="card-body table-responsive p-3">
                            <form action="" method="post" name="orderUpdate" id="orderUpdate">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="120">Product</th>
                                            <th width="120">Price</th>
                                            <th width="100">Qty</th>
                                            <th width="120">Discount</th>
                                            <th width="120">Delivery</th>
                                            <th width="120">Total</th>
                                        </tr>
                                    </thead>

                                    <tbody id="order-items">
                                        @foreach ($orderedItems as $item)
                                            @php
                                                $variant_info = ProductVariant::where('id', $item->product_id)->first();
                                                $product_info = NewProduct::where(
                                                    'id',
                                                    $variant_info->product_id,
                                                )->first();
                                            @endphp

                                            <tr class="item-row" data-price="{{ $item->price }}"
                                                data-discount="{{ $item->discount ?? 0 }}">

                                                <td>{{ $product_info->name }} ({{ $item->name }}) <input type="hidden"
                                                        value="{{ $item->id }}"></td>

                                                <td>
                                                    Tk <span class="price-text">{{ $item->price }}</span>
                                                    <input type="hidden" class="price-input" value="{{ $item->price }}">
                                                </td>

                                                <td>
                                                    <input type="number" class="qty-input" style="width: 40px; border-radius: 5px;"
                                                        name="item_qty[{{ $item->id }}]" value="{{ $item->qty }}"
                                                        min="1">
                                                </td>

                                                <td>
                                                    Tk <span class="discount-text">{{ $item->discount ?? 0 }}</span>
                                                    <input type="hidden" class="discount-input"
                                                        value="{{ $item->discount ?? 0 }}">
                                                </td>

                                                <td>
                                                    @if ($item->free_delivery)
                                                        <span class="badge bg-success">FREE</span>
                                                    @else
                                                        <span class="badge bg-secondary">Standard</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    Tk <span class="row-total-text">{{ $item->total }}</span>
                                                    <input type="hidden" class="row-total-input"
                                                        value="{{ $item->total }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-end">Subtotal:</th>
                                            <td>
                                                Tk <span id="subtotal-text">{{ $order->subtotal }}</span>
                                                <input type="hidden" id="subtotal" name="subtotal"
                                                    value="{{ $order->subtotal }}">
                                            </td>
                                        </tr>

                                        <tr>
                                            <th colspan="5" class="text-end">Shipping:</th>
                                            <td>
                                                Tk <span id="shipping-text">{{ $order->shipping }}</span>
                                                <input type="hidden" id="shipping" value="{{ $order->shipping }}">
                                            </td>
                                        </tr>

                                        <tr>
                                            <th colspan="5" class="text-end">Coupon Discount:</th>
                                            <td>
                                                Tk - <span id="coupon-text">{{ $order->discount }}</span>
                                                <input type="hidden" id="coupon" value="{{ $order->discount }}">
                                            </td>
                                        </tr>

                                        <tr>
                                            <th colspan="5" class="text-end fw-bold">Grand Total:</th>
                                            <td>
                                                Tk <span id="grand-total-text">{{ $order->grand_total }}</span>
                                                <input type="hidden" id="grand-total"
                                                    value="{{ $order->grand_total }}">
                                            </td>
                                        </tr>
                                    </tfoot>


                                </table>

                                <button type="submit" class="btn btn-dark px-4">Update Order</button>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="col-md-2">
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
                                    <h2 class="h4 mb-3">Orde Note</h2>
                                    <input type="text" name="admin_note" id="admin_note" value="{{ $order->admin_note }}" class="form-control">
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

    <script>
        $("#addressForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);

            $.ajax({
                url: '{{ route('orders.address_update', $order->id) }}',
                type: 'POST',
                data: element.serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === true) {
                        window.location.href = "{{ route('orders.details', $order->id) }}";
                    }
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong", jqXHR.responseText);
                }
            });

        });
    </script>

    <script>
        $("#orderUpdate").submit(function(event) {
            event.preventDefault();
            var element = $(this);

            $.ajax({
                url: '{{ route('orders.order_update', $order->id) }}',
                type: 'POST',
                data: element.serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === true) {
                        window.location.href = "{{ route('orders.details', $order->id) }}";
                    }
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong", jqXHR.responseText);
                }
            });

        });
    </script>

    <script>
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', updateTotals);
        });

        function updateTotals() {
            let subtotal = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                let price = parseFloat(row.dataset.price);
                let discount = parseFloat(row.dataset.discount);
                let qty = parseInt(row.querySelector('.qty-input').value) || 1;

                let rowTotal = (price * qty) - discount;
                subtotal += rowTotal;

                row.querySelector('.row-total-text').innerText = rowTotal.toFixed(2);
                row.querySelector('.row-total-input').value = rowTotal.toFixed(2);
            });

            let shipping = parseFloat(document.getElementById('shipping').value) || 0;
            let coupon = parseFloat(document.getElementById('coupon').value) || 0;

            document.getElementById('subtotal-text').innerText = subtotal.toFixed(2);
            document.getElementById('subtotal').value = subtotal.toFixed(2);

            let grandTotal = subtotal + shipping - coupon;

            document.getElementById('grand-total-text').innerText = grandTotal.toFixed(2);
            document.getElementById('grand-total').value = grandTotal.toFixed(2);
        }
    </script>




    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 1500,
                showConfirmButton: false
            });
        </script>
    @endif
@endsection
