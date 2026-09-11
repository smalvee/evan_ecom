@php
    use Gloudemans\Shoppingcart\Facades\Cart;
@endphp

@extends('front.layouts.app')

@section('content')
    <section style="background:#FC8934;color:#fff;" class="py-3">
        <div class="container text-center">
            <h1 class="fw-bold mb-1">Shopping Cart</h1>
        </div>
    </section>

    <!-- Cart Details -->
    <section class="py-5">
        <div class="container">
            @if (Session::has('success'))
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if (Session::has('error'))
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ Session::get('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            <!-- Table headings -->
            <div class="row fw-bold border-bottom pb-2 mb-3 align-items-center">
                <div class="col-md-5">Product</div>
                <div class="col-md-2 text-center">Price</div>
                <div class="col-md-3 text-center">Quantity</div>
                <div class="col-md-2 text-end">Total</div>
            </div>

            <!-- Cart Items -->
            @if (!empty($cartContent) && count($cartContent) > 0)
                @foreach ($cartContent as $item)
                    <div class="row align-items-center mb-3 cart-row" data-id="{{ $item->id }}">
                        <div class="col-md-5 d-flex align-items-center">


                            @if (!empty($item->options->productImage->image))
                                <img style="width: 100%; max-width: 150px;"
                                    src="{{ asset('uploads/products/small/' . $item->options->productImage->image) }}"
                                    class="img-thumbnail">
                            @else
                                <img style="width: 100%; max-width: 150px;"
                                    src="{{ asset('admin-assets/img/default-150x150.png') }}" class="img-thumbnail">
                            @endif

                            <div style="padding-left: 1.5rem">
                                <h6 class="mb-1">{{ $item->name }}</h6>
                                <button onclick="deleteItem('{{ $item->rowId }}');"
                                    class="btn btn-link p-0 text-danger remove-btn" type="button">Remove</button>
                                {{-- <div class="small text-muted">Regular price <span class="price-txt">Tk
                                        {{ number_format($item->price, 2) }}</span></div> --}}
                            </div>
                        </div>

                        <div class="col-md-2 text-center">
                            <div class="price" data-price="{{ $item->price }}">Tk {{ number_format($item->price, 2) }}
                            </div>
                        </div>

                        <div class="col-md-3 text-center">
                            <div class="d-inline-flex align-items-center border rounded" style="background:#f1f1f1;">
                                <div>
                                    <button class="btn btn-sm px-3 minus-btn sub" data-id="{{ $item->rowId }}"
                                        type="button">-</button>
                                </div>

                                <input type="number" class="form-control form-control-sm qty-input text-center"
                                    value="{{ $item->qty }}" min="0" style="width:72px;" readonly>
                                <div>
                                    <button class="btn btn-sm px-3 plus-btn add" data-id="{{ $item->rowId }}"
                                        type="button">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 text-end">
                            <div class="row-total fw-bold">Tk {{ number_format($item->price * $item->qty, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-warning">Your cart is empty!</div>
            @endif

            <!-- Note / Coupon / Summary -->
            <div class="row mt-4 gx-4">
                <div class="col-md-6">
                    {{-- <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-sticky me-1"></i> Note</label>
                        <textarea class="form-control" rows="3" placeholder="Add a note..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-ticket-perforated me-1"></i> Coupon</label>
                        <div class="input-group" style="max-width:360px;">
                            <input type="text" class="form-control" placeholder="Coupon code" id="couponCode">
                            <button class="btn btn-outline-secondary" id="applyCouponBtn"
                                style="background-color: #FC8934; color: #fff;">Apply</button>
                        </div>
                    </div> --}}
                </div>

                <div class="col-md-6">
                    <div class="card p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <div>Subtotal</div>
                            <div id="cartSubtotal" class="fw-bold">
                                Tk {{ cart::subtotal() }}
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <!-- keep modal and routes as is -->
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                data-bs-target="#checkoutModal">
                                Proceed to Checkout
                            </button>
                            <a href="{{ route('front.home') }}" class="btn text-white" style="background:#FC8934;">Continue
                                Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Modal (unchanged) -->
    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.3);">

                <!-- Header -->
                <div class="modal-header text-white" style="background:linear-gradient(135deg,#FC8934,#FF4D4D);">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cash-stack me-2"></i> ক্যাশ অন ডেলিভারিতে
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <p class="mb-4 text-muted">অর্ডার করতে আপনার তথ্য দিন</p>

                    <form action="" method="post" name="order_form" id="order_form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person-fill me-1 text-warning"></i> আপনার নাম*
                                </label>
                                <input type="text"
                                    value="{{ !empty($customerAddress->name) ? $customerAddress->name : '' }}"
                                    class="form-control" placeholder="আপনার নাম" id="name" name="name">
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-telephone-fill me-1 text-warning"></i> ফোন নাম্বার*
                                </label>
                                <input type="tel"
                                    value="{{ !empty($customerAddress->phone) ? $customerAddress->phone : '' }}"
                                    class="form-control" placeholder="ফোন নাম্বার" id="phone" name="phone">
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bi bi-geo-alt-fill me-1 text-warning"></i> এড্রেস*
                                </label>
                                <textarea class="form-control" rows="2" placeholder="এড্রেস" id="address" name="address">{{ !empty($customerAddress->address) ? $customerAddress->address : '' }}</textarea>
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class="bi bi-truck me-1 text-warning"></i> শিপিং মেথড</label>
                                <select class="form-select" id="shipping_method" name="shipping_method">
                                    <option value="">-- Select Shipping --</option>
                                    @if (!empty($shippingCharge))
                                        @foreach ($shippingCharge as $shipping)
                                            <option value="{{ $shipping->amount }}">{{ $shipping->location }} - Tk
                                                {{ $shipping->amount }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class="bi bi-ticket-perforated-fill me-1 text-warning"></i>
                                    কুপন কোড</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="কুপন কোড" name="coupon"
                                        id="coupon">
                                    <button class="btn" type="button" name="apply_discount" id="apply_discount"
                                        style="background:#FC8934; color:#fff;">এপ্লাই</button>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div
                            style="margin-top:25px; padding:20px; border-radius:12px; background:#fff7f0; border:1px solid #ffd9b3;">
                            @if (!empty($cartContent) && count($cartContent) > 0)
                                @foreach ($cartContent as $item)
                                    <p style="margin:0 0 8px; font-weight:600; color:#444;">
                                        <i class="bi bi-basket-fill me-1"></i> {{ $item->name }}
                                        - <strong style="color:#FC8934;">Tk {{ number_format($item->price, 2) }}</strong>
                                    </p>
                                @endforeach
                            @else
                                <div
                                    style="padding:10px; background:#fff3cd; border:1px solid #ffeeba; border-radius:8px; color:#856404;">
                                    Your cart is empty!
                                </div>
                            @endif

                            <div style="display:flex; justify-content:space-between; margin-top:10px; font-size:15px;">
                                <span>সাব টোটাল</span>
                                <span id="subtotal" style="font-weight:bold;">Tk {{ Cart::subtotal() }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:5px; font-size:15px;">
                                <span>ডেলিভারি চার্জ</span>
                                <span id="shipping_charge" style="font-weight:bold;">Tk 0.00</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:5px; font-size:15px;">
                                <span>ডিসকাউন্ট</span>
                                <span id="discount_amount" style="font-weight:bold;">Tk 0.00</span>
                            </div>
                            <div
                                style="display:flex; justify-content:space-between; margin-top:10px; font-size:18px; font-weight:bold; color:#d9534f; border-top:1px solid #ddd; padding-top:10px;">
                                <span>সর্বমোট</span>
                                <span id="grand_total">Tk {{ Cart::subtotal() }}</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label"><i class="bi bi-journal-text me-1 text-warning"></i> Order
                                note</label>
                            <textarea class="form-control" rows="2" placeholder="Order note" name="order_note"></textarea>
                        </div>

                        <p class="small text-muted mt-3">
                            আপনার অর্ডার কনফার্ম করতে ক্লিক করুন। উপরের বাটনে ক্লিক করলে আপনার অর্ডারটি সাথে সাথে কনফার্ম
                            হয়ে যাবে!
                        </p>

                        <button type="submit" class="btn w-100 mt-2"
                            style="background:#FC8934; color:#fff; font-weight:bold;">
                            অর্ডার কনফার্ম করুন
                        </button>
                    </form>
                    <br>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('customJs')
    <script>
        $('.add').click(function() {
            var qtyElement = $(this).parent().prev(); // Qty Input
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue < 10) {
                qtyElement.val(qtyValue + 1);

                var rowId = $(this).data('id');
                var newQty = qtyElement.val();
                updateCart(rowId, newQty);
            }
        });

        $('.sub').click(function() {
            var qtyElement = $(this).parent().next();
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue > 1) {
                qtyElement.val(qtyValue - 1);

                var rowId = $(this).data('id');
                var newQty = qtyElement.val();
                updateCart(rowId, newQty);
            }
        });

        function updateCart(rowId, qty) {
            $.ajax({
                url: '{{ route('front.updateCart') }}',
                type: 'post',
                data: {
                    rowId: rowId,
                    qty: qty
                },
                dataType: 'json',
                success: function(response) {

                    window.location.href = '{{ route('front.cart') }}'

                }
            });
        }

        function deleteItem(rowId) {

            if (confirm("Are you want to delete?")) {


                $.ajax({
                    url: '{{ route('front.deleteItem.cart') }}',
                    type: 'post',
                    data: {
                        rowId: rowId
                    },
                    dataType: 'json',
                    success: function(response) {

                        window.location.href = '{{ route('front.cart') }}'

                    }
                });

            }


        }


 

        // Checkout 
        $(document).ready(function() {


            // Submit order form
            $("#order_form").submit(function(event) {
                event.preventDefault();
                $("button[type=submit]").prop('disabled', true);

                $.ajax({
                    url: '{{ route('front.checkout') }}',
                    type: 'post',
                    data: $(this).serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);

                        if (response["status"] == true) {
                            $("#name").removeClass('is-invalid').siblings('.invalid-feedback')
                                .html('');
                            $("#phone").removeClass('is-invalid').siblings('.invalid-feedback')
                                .html('');
                            $("#address").removeClass('is-invalid').siblings(
                                '.invalid-feedback').html('');
                            $("#shipping_method").removeClass('is-invalid').siblings(
                                '.invalid-feedback').html('');

                            // Redirect after success
                            window.location.href = "{{ url('thanks/') }}/" + response.orderId;
                        } else {
                            var errors = response['errors'];

                            if (errors['name']) {
                                $("#name").addClass('is-invalid').siblings('.invalid-feedback')
                                    .html(errors['name']);
                            } else {
                                $("#name").removeClass('is-invalid').siblings(
                                    '.invalid-feedback').html('');
                            }

                            if (errors['phone']) {
                                $("#phone").addClass('is-invalid').siblings('.invalid-feedback')
                                    .html(errors['phone']);
                            } else {
                                $("#phone").removeClass('is-invalid').siblings(
                                    '.invalid-feedback').html('');
                            }

                            if (errors['address']) {
                                $("#address").addClass('is-invalid').siblings(
                                    '.invalid-feedback').html(errors['address']);
                            } else {
                                $("#address").removeClass('is-invalid').siblings(
                                    '.invalid-feedback').html('');
                            }

                            if (errors['shipping_method']) {
                                $("#shipping_method").addClass('is-invalid').siblings(
                                    '.invalid-feedback').html(errors['shipping_method']);
                            } else {
                                $("#shipping_method").removeClass('is-invalid').siblings(
                                    '.invalid-feedback').html('');
                            }
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            let subtotal = parseFloat("{{ Cart::subtotal(2, '.', '') }}") || 0;
            let shipping = 0;
            let discount = 0;
            let appliedCoupon = ""; // store coupon code

            // Function to update totals
            function updateTotals() {
                let grandTotal = subtotal + shipping - discount;
                if (grandTotal < 0) grandTotal = 0;

                $("#subtotal").text("Tk " + subtotal.toFixed(2));
                $("#shipping_charge").text("Tk " + shipping.toFixed(2));
                $("#discount_amount").text("- Tk " + discount.toFixed(2));
                $("#grand_total").text("Tk " + grandTotal.toFixed(2));

                // Keep coupon code in input
                $("#coupon").val(appliedCoupon);
            }

            // Shipping change
            $("#shipping_method").on("change", function() {
                shipping = parseFloat($(this).val()) || 0;
                updateTotals();
            });

            // Apply coupon
            $("#apply_discount").on("click", function() {
                let couponCode = $("#coupon").val().trim();
                if (couponCode === "") {
                    alert("Please enter a coupon code.");
                    return;
                }

                $.ajax({
                    url: "{{ route('coupon.apply') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        coupon: couponCode,
                        subtotal: subtotal
                    },
                    success: function(response) {
                        if (response.status) {
                            discount = parseFloat(response.discount) || 0;
                            appliedCoupon = couponCode;
                            alert("Coupon applied! Discount: Tk " + discount.toFixed(2));
                        } else {
                            discount = 0;
                            appliedCoupon = couponCode; // keep invalid coupon in input
                            alert(response.message);
                        }
                        updateTotals();
                    },
                    error: function() {
                        alert("Something went wrong while applying coupon.");
                    }
                });
            });

            // Initial calculation
            updateTotals();
        });
    </script>
@endsection
