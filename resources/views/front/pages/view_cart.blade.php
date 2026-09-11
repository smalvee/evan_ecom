@extends('front.layouts.new_app')

@section('content')
    <?php
    
    use App\Models\ProductImage;
    use App\Models\ProductVariant;
    use App\Models\NewProduct;
    use Gloudemans\Shoppingcart\Facades\Cart;
    use App\Models\VariationValues;
    use App\Models\Variation;
    
    $districts = ['Bagerhat', 'Bandarban', 'Barguna', 'Barishal', 'Bhola', 'Bogura', 'Brahmanbaria', 'Chandpur', 'Chattogram', 'Chuadanga', 'Cox’s Bazar', 'Cumilla', 'Dhaka', 'Dinajpur', 'Faridpur', 'Feni', 'Gaibandha', 'Gazipur', 'Gopalganj', 'Habiganj', 'Jamalpur', 'Jashore', 'Jhalokathi', 'Jhenaidah', 'Joypurhat', 'Khagrachhari', 'Khulna', 'Kishoreganj', 'Kurigram', 'Kushtia', 'Lakshmipur', 'Lalmonirhat', 'Madaripur', 'Magura', 'Manikganj', 'Meherpur', 'Moulvibazar', 'Munshiganj', 'Mymensingh', 'Naogaon', 'Narail', 'Narayanganj', 'Narsingdi', 'Natore', 'Netrakona', 'Nilphamari', 'Noakhali', 'Pabna', 'Panchagarh', 'Patuakhali', 'Pirojpur', 'Rajbari', 'Rajshahi', 'Rangamati', 'Rangpur', 'Satkhira', 'Shariatpur', 'Sherpur', 'Sirajganj', 'Sunamganj', 'Sylhet', 'Tangail', 'Thakurgaon'];
    
    ?>
    <!-- Breadcrumb Section Start -->
    <section class="breadcrumb-section pt-0">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-contain">
                        <h2>Cart</h2>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">
                                        <i class="fa-solid fa-house"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">Cart</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Cart Section Start -->
    <section class="cart-section section-b-space">
        <div class="container-fluid-lg">
            <div class="row g-sm-5 g-3">



                <div class="col-xxl-9">
                    <div class="cart-table">
                        <div class="table-responsive-xl">
                            <table class="table">
                                <tbody>

                                    @if (!empty($cartContent) && count($cartContent) > 0)
                                        @foreach ($cartContent as $item)
                                            @php
                                                $product_image = ProductImage::where('product_id', $item->id)
                                                    ->where('is_thumb', 0)
                                                    ->first();
                                                $get_product_id = ProductVariant::where('id', $item->id)->first();
                                                $get_product_info = NewProduct::where(
                                                    'id',
                                                    $get_product_id->product_id,
                                                )->first();
                                            @endphp

                                            <tr class="product-box-contain">
                                                <td class="product-detail">
                                                    <div class="product border-0">
                                                        <a href="product-left-thumbnail.html" class="product-image">
                                                            <img src="{{ asset('uploads/products/small/' . $product_image->image) }}"
                                                                class="img-fluid blur-up lazyload" alt="">
                                                        </a>
                                                        <div class="product-detail">
                                                            <ul>
                                                                <li class="name">
                                                                    {{ \Illuminate\Support\Str::limit($get_product_info->name, 25) }}
                                                                    <br>{{ $get_product_id->sku }}

                                                                </li>

                                                                {{-- <ul>
                                                                    @foreach ($get_product_id->variation_values as $items)
                                                                        @php
                                                                            $variation = Variation::find(
                                                                                $items['variation_id'],
                                                                            );
                                                                            $value = VariationValues::find(
                                                                                $items['value_id'],
                                                                            );
                                                                        @endphp

                                                                        @if ($variation && $value)
                                                                            <li>
                                                                                {{ $variation->variations }}: {{ $value->value }}
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>   --}}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="price">
                                                    <h4 class="table-title text-content">Price</h4>
                                                    <h5>{{ number_format($item->price, 2) }} Tk</h5>

                                                </td>

                                                <td class="quantity">
                                                    <h4 class="table-title text-content">Qty</h4>
                                                    <div class="quantity-price">
                                                        <div class="cart_qty">
                                                            <div class="input-group">
                                                                <div>
                                                                    <button class="minus-btn sub"
                                                                        data-rowid="{{ $item->rowId }}">-</button>
                                                                </div>

                                                                <input class="qty-input" type="number"
                                                                    value="{{ $item->qty }}" min="1"
                                                                    max="10" readonly>

                                                                <button class="plus-btn add"
                                                                    data-rowid="{{ $item->rowId }}">+</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>


                                                <td class="subtotal">
                                                    <h4 class="table-title text-content">Total</h4>
                                                    <h5 class="item-subtotal" data-price="{{ $item->price }}">
                                                        {{ number_format($item->price * $item->qty, 2) }} Tk
                                                    </h5>
                                                </td>


                                                <td class="save-remove">
                                                    <h4 class="table-title text-content">Action</h4>



                                                    <a class="remove close_button" data-rowid="{{ $item->rowId }}"
                                                        href="javascript:void(0)">Remove</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3">
                    <div class="summery-box p-sticky">
                        <div class="summery-header">
                            <h3>Cart Total</h3>
                        </div>

                        <div class="summery-contain">
                            <ul>
                                <li>
                                    <h4>Subtotal</h4>
                                    <h4 class="price">{{ cart::subtotal() }} tK</h4>
                                </li>
                            </ul>
                        </div>



                        <div class="button-group cart-button">
                            <ul>
                                <li>
                                    <button class="btn btn-animation proceed-btn fw-bold" data-bs-toggle="modal"
                                        data-bs-target="#checkoutModal">Process To Checkout</button>
                                </li>

                                <li>
                                    <a href="{{ route('front.home') }}"><button
                                            class="btn btn-light shopping-button text-dark">
                                            <i class="fa-solid fa-arrow-left-long"></i>Return To Shopping</button></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </section>
    <!-- Cart Section End -->

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.3);">

                <!-- Header -->
                <div class="modal-header text-white" style="background:linear-gradient(135deg,#FC8934,#FF4D4D);">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cash-stack me-2"></i> Cash On Delevery
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <p class="mb-4 text-muted">Prode Your Information to Order</p>

                    <form action="" method="post" name="order_form" id="order_form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person-fill me-1 text-warning"></i> Name*
                                </label>
                                <input type="text"
                                    value="{{ !empty($customerAddress->name) ? $customerAddress->name : '' }}"
                                    class="form-control" placeholder="আপনার নাম" id="name" name="name">
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-telephone-fill me-1 text-warning"></i> Number*
                                </label>
                                <input type="tel"
                                    value="{{ !empty($customerAddress->phone) ? $customerAddress->phone : '' }}"
                                    class="form-control" placeholder="ফোন নাম্বার" id="phone" name="phone">
                                <p class="invalid-feedback"></p>
                            </div>
                             <div class="col-12">
                                <label class="form-label"><i class="bi bi-truck me-1 text-warning"></i> Select
                                    District</label>
                                <select name="district" id="district" class="form-select">
                                    <option value="">-- Select District --</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district }}">{{ $district }}</option>
                                    @endforeach
                                </select>
                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bi bi-geo-alt-fill me-1 text-warning"></i> Address*
                                </label>
                                <textarea class="form-control" rows="2" placeholder="এড্রেস" id="address" name="address">{{ !empty($customerAddress->address) ? $customerAddress->address : '' }}</textarea>
                                <p class="invalid-feedback"></p>
                            </div>

                           

                            {{-- <div class="col-12">
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
                            </div> --}}

                            <div class="col-12">
                                <label class="form-label"><i class="bi bi-truck me-1 text-warning"></i> Delivery
                                    Charge</label>
                                <input type="text" class="form-control" id="shipping_charge" name="shipping_charge"
                                    value="0" readonly>

                                <p class="invalid-feedback"></p>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class="bi bi-ticket-perforated-fill me-1 text-warning"></i>
                                    কুপন কোড</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="কুপন কোড" name="coupon"
                                        id="coupon">
                                    <button class="btn" type="button" name="apply_discount" id="apply_discount"
                                        style="background:#FC8934; color:#fff;">Apply</button>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div
                            style="margin-top:25px; padding:20px; border-radius:12px; background:#fff7f0; border:1px solid #ffd9b3;">
                            @if (!empty($cartContent) && count($cartContent) > 0)
                                @foreach ($cartContent as $item)
                                    @php
                                        $product_variuant = ProductVariant::where('sku', $item->name)->first();
                                        $product_info = NewProduct::where('id', $product_variuant->product_id)->first();
                                    @endphp
                                    <p style="margin:0 0 8px; font-weight:600; color:#444;">
                                        <i class="bi bi-basket-fill me-1"></i>{{ $product_info->name }}
                                        ({{ $item->name }})
                                        x {{ $item->qty }}
                                        - <strong style="color:#FC8934;">Tk
                                            {{ number_format($item->price * $item->qty, 2) }}</strong>
                                    </p>
                                @endforeach
                            @else
                                <div
                                    style="padding:10px; background:#fff3cd; border:1px solid #ffeeba; border-radius:8px; color:#856404;">
                                    Your cart is empty!
                                </div>
                            @endif

                            <div style="display:flex; justify-content:space-between; margin-top:10px; font-size:15px;">
                                <span>Subtotal</span>
                                <span id="subtotal" style="font-weight:bold;">Tk {{ Cart::subtotal() }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:5px; font-size:15px;">
                                <span>Delivery Charge</span>
                                <span id="shipping_charge2" style="font-weight:bold;">Tk 0.00</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:5px; font-size:15px;">
                                <span>Discount</span>
                                <span id="discount_amount" style="font-weight:bold;">Tk 0.00</span>
                            </div>
                            <div
                                style="display:flex; justify-content:space-between; margin-top:10px; font-size:18px; font-weight:bold; color:#d9534f; border-top:1px solid #ddd; padding-top:10px;">
                                <span>Grand Total</span>
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
                $("#shipping_charge").val(shipping.toFixed(2));
                $("#shipping_charge2").text("Tk " + shipping.toFixed(2));

                $("#discount_amount").text("- Tk " + discount.toFixed(2));
                $("#grand_total").text("Tk " + grandTotal.toFixed(2));

                // Keep coupon code in input
                $("#coupon").val(appliedCoupon);
            }

            // Shipping change
            // $("#shipping_method").on("change", function() {
            //     shipping = parseFloat($(this).val()) || 0;
            //     updateTotals();
            // });

            $("#district").on("change", function() {
                let district = $(this).val();

                if (district === "Dhaka") {
                    shipping = 70;
                } else if (district !== "") {
                    shipping = 130;
                } else {
                    shipping = 0;
                }

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

    <script>
        $(document).on('click', '.add', function() {
            let input = $(this).closest('.input-group').find('.qty-input');
            let qty = parseInt(input.val());

            if (qty < 10) {
                qty++;
                input.val(qty);

                let rowId = $(this).data('rowid');
                updateSubtotal($(this), qty);
                updateCart(rowId, qty);
            }
        });

        $(document).on('click', '.sub', function() {
            let input = $(this).closest('.input-group').find('.qty-input');
            let qty = parseInt(input.val());

            if (qty > 1) {
                qty--;
                input.val(qty);

                let rowId = $(this).data('rowid');
                updateSubtotal($(this), qty);
                updateCart(rowId, qty);
            }
        });

        function updateSubtotal(button, qty) {
            let row = button.closest('tr');
            let price = parseFloat(row.find('.item-subtotal').data('price'));
            let total = (price * qty).toFixed(2);

            row.find('.item-subtotal').text(total + ' Tk');
        }

        function updateCart(rowId, qty) {
            $.ajax({
                url: '{{ route('front.updateCart') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    rowId: rowId,
                    qty: qty
                },
                dataType: 'json',
                success: function(response) {

                    window.location.href = '{{ route('front.cart') }}'

                }

            });
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
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);

                        if (response["status"] == true) {
                            $("#name").removeClass('is-invalid').siblings('.invalid-feedback')
                                .html('');
                            $("#phone").removeClass('is-invalid').siblings('.invalid-feedback')
                                .html('');
                            $("#address").removeClass('is-invalid').siblings(
                                '.invalid-feedback').html('');
                            $("#district").removeClass('is-invalid').siblings(
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

                            if (errors['district']) {
                                $("#district").addClass('is-invalid').siblings(
                                    '.invalid-feedback').html(errors['district']);
                            } else {
                                $("#district").removeClass('is-invalid').siblings(
                                    '.invalid-feedback').html('');
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
