@extends('front.layouts.app')

@section('content')
    <!-- Checkout Section -->
    <div class="checkout-section container my-4" style="max-width:800px; margin:auto;">
        <div class="card" style="border-radius:12px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.3);">

            <!-- Header -->
            <div class="card-header text-white" style="background:linear-gradient(135deg,#FC8934,#FF4D4D);">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-cash-stack me-2"></i> ক্যাশ অন ডেলিভারিতে
                </h5>
            </div>

            <!-- Body -->
            <div class="card-body p-4">
                <p class="mb-4 text-muted">অর্ডার করতে আপনার তথ্য দিন</p>

                <!-- Order Form -->
                <form action="" method="post" name="order_form" id="order_form">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-person-fill me-1 text-warning"></i> আপনার নাম*
                            </label>
                            <input type="text" value="{{ !empty($customerAddress->name) ? $customerAddress->name : '' }}"
                                class="form-control" placeholder="আপনার নাম" id="name" name="name">
                            <p class="invalid-feedback"></p>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-telephone-fill me-1 text-warning"></i> ফোন নাম্বার*
                            </label>
                            <input type="tel"
                                value="{{ !empty($customerAddress->phone) ? $customerAddress->phone : '' }}"
                                class="form-control" placeholder="ফোন নাম্বার" id="phone" name="phone">
                            <p class="invalid-feedback"></p>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label class="form-label">
                                <i class="bi bi-geo-alt-fill me-1 text-warning"></i> এড্রেস*
                            </label>
                            <textarea class="form-control" rows="2" placeholder="এড্রেস" id="address" name="address">{{ !empty($customerAddress->address) ? $customerAddress->address : '' }}</textarea>
                            <p class="invalid-feedback"></p>
                        </div>

                        <!-- Shipping Method -->
                        <div class="col-12">
                            <label class="form-label"><i class="bi bi-truck me-1 text-warning"></i> শিপিং মেথড</label>
                            <select class="form-select" id="shipping_method" name="shipping_method">
                                <option value="">-- Select Shipping --</option>
                                @if (!empty($shippingCharge))
                                    @foreach ($shippingCharge as $shipping)
                                        <option value="{{ $shipping->amount }}">{{ $shipping->location }} - ৳
                                            {{ $shipping->amount }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <p class="invalid-feedback"></p>
                        </div>

                        <!-- Coupon Code -->
                        <div class="col-12">
                            <label class="form-label"><i class="bi bi-ticket-perforated-fill me-1 text-warning"></i> কুপন
                                কোড</label>
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
                        @if (!empty($selected_products))
                            @foreach ($selected_products as $item)
                                <p style="margin:0 0 8px; font-weight:600; color:#444;">
                                    <i class="bi bi-basket-fill me-1"></i> {{ $item->title }}
                                    - <strong style="color:#FC8934;">Tk {{ number_format($item->price, 2) }}</strong>
                                    <strong style="color:#000000;">x 1</strong>
                                </p>
                                <input type="hidden" name="product_title" value="{{ $item->title - $item->sku }}">

                                <input type="hidden" name="product_id" value="{{ $item->id }}">
                            @endforeach
                        @else
                            <div
                                style="padding:10px; background:#fff3cd; border:1px solid #ffeeba; border-radius:8px; color:#856404;">
                                Your cart is empty!
                            </div>
                        @endif

                        <!-- Subtotal -->
                        <div style="display:flex; justify-content:space-between; margin-top:10px; font-size:15px;">
                            <span>সাব টোটাল</span>
                            <input type="text" id="subtotal" name="subtotal" value="{{ $item->price }}" readonly
                                style="width:100px; font-weight:bold; text-align:right; border:none; background:transparent;">
                        </div>

                        <!-- Shipping -->
                        <div style="display:flex; justify-content:space-between; margin-top:5px; font-size:15px;">
                            <span>ডেলিভারি চার্জ</span>
                            <input type="text" id="shipping_charge" name="shipping_charge" value="0.00" readonly
                                style="width:100px; font-weight:bold; text-align:right; border:none; background:transparent;">
                        </div>

                        <!-- Discount -->
                        <div style="display:flex; justify-content:space-between; margin-top:5px; font-size:15px;">
                            <span>ডিসকাউন্ট</span>
                            <input type="text" id="discount_amount" name="discount_amount" value="0.00" readonly
                                style="width:100px; font-weight:bold; text-align:right; border:none; background:transparent;">
                        </div>

                        <!-- Grand Total -->
                        <div
                            style="display:flex; justify-content:space-between; margin-top:10px; font-size:18px; font-weight:bold; color:#d9534f; border-top:1px solid #ddd; padding-top:10px;">
                            <span>সর্বমোট</span>
                            <input type="text" id="grand_total" name="grand_total" value="{{ $item->price }}" readonly
                                style="width:100px; font-weight:bold; text-align:right; border:none; background:transparent;">
                        </div>
                    </div>

                    <!-- Order Note -->
                    <div class="mt-3">
                        <label class="form-label"><i class="bi bi-journal-text me-1 text-warning"></i> Order note</label>
                        <textarea class="form-control" rows="2" placeholder="Order note" name="order_note"></textarea>
                    </div>

                    <p class="small text-muted mt-3">
                        আপনার অর্ডার কনফার্ম করতে ক্লিক করুন। উপরের বাটনে ক্লিক করলে আপনার অর্ডারটি সাথে সাথে কনফার্ম হয়ে
                        যাবে!
                    </p>

                    <button type="submit" class="btn w-100 mt-2"
                        style="background:#FC8934; color:#fff; font-weight:bold;">
                        অর্ডার কনফার্ম করুন
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('customJs')
    <script>
        $(document).ready(function() {
            let subtotal = parseFloat("{{ $item->price }}") || 0;
            let shipping = 0;
            let discount = 0;
            let appliedCoupon = "";

            function updateTotals() {
                let grandTotal = subtotal + shipping - discount;
                if (grandTotal < 0) grandTotal = 0;

                $("#subtotal").val(subtotal.toFixed(2));
                $("#shipping_charge").val(shipping.toFixed(2));
                $("#discount_amount").val(discount.toFixed(2));
                $("#grand_total").val(grandTotal.toFixed(2));

                $("#coupon").val(appliedCoupon);
            }

            // Update shipping when changed
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
                            appliedCoupon = couponCode;
                            alert(response.message);
                        }
                        updateTotals();
                    },
                    error: function() {
                        alert("Something went wrong while applying coupon.");
                    }
                });
            });

            updateTotals();
        });



        // Checkout 
        $(document).ready(function() {


            // Submit order form
            $("#order_form").submit(function(event) {
                event.preventDefault();
                $("button[type=submit]").prop('disabled', true);

                $.ajax({
                    url: '{{ route('front.singCheckout', $item->slug) }}',
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
@endsection
