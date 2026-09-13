@extends('front.layouts.new_app')

@section('content')
    <section class="breadcrumb-section pt-0">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-contain">
                        <h2>Track Order</h2>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('front.home') }}"><i class="fa-solid fa-house"></i></a>
                                </li>
                                <li class="breadcrumb-item active">Track Order</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-lg-space">
        <div class="container-fluid-lg">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3 text-center">Find Your Order</h5>
                            <form action="" method="post" id="ge_order_status" name="ge_order_status">
                                @csrf
                                <div class="mb-3">
                                    <label for="order_id" class="form-label">Order ID</label>
                                    <input type="text" name="order_id" id="order_id" class="form-control"
                                        placeholder="Enter your order ID" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        placeholder="Enter the phone used for the order" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Track Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="customer-info" class="mb-3 mt-4 text-center" style="display:none;">
                <h6>Customer Info</h6>
                <p class="mb-0">Name: <span id="customer-name"></span></p>
                <p>Phone: <span id="customer-phone"></span></p>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Order Status</h5>

                            <div class="track">
                                <div class="step" data-status="OrderPlaced">
                                    <span class="icon"><i class="fas fa-receipt"></i></span>
                                    <span class="text">Order Placed</span>
                                </div>
                                <div class="step" data-status="Confirm">
                                    <span class="icon"><i class="fas fa-cogs"></i></span>
                                    <span class="text">Confirm</span>
                                </div>
                                <div class="step" data-status="Shipped">
                                    <span class="icon"><i class="fas fa-truck"></i></span>
                                    <span class="text">Shipped</span>
                                </div>
                                <div class="step" data-status="Delivered">
                                    <span class="icon"><i class="fas fa-box-open"></i></span>
                                    <span class="text">Delivered</span>
                                </div>
                            </div>

                            <div id="order-message" class="mt-3 text-center text-danger"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <style>
        .track {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            position: relative;
        }

        .track .step {
            text-align: center;
            width: 25%;
            position: relative;
        }

        .track .step .icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #ddd;
            margin-bottom: 10px;
            font-size: 20px;
            color: #fff;
        }

        .track .step.active .icon {
            background: #0da487;
        }

        .track .step .text {
            font-size: 14px;
            font-weight: 600;
            color: #555;
        }

        .track:before {
            content: "";
            position: absolute;
            top: 25px;
            left: 0;
            width: 100%;
            height: 4px;
            background: #ddd;
            z-index: -1;
        }

        .track .step.active:before {
            content: "";
            position: absolute;
            top: 25px;
            left: 0;
            width: 100%;
            height: 4px;
            background: #0da487;
            z-index: -1;
        }
    </style>

    <script>
        $("#ge_order_status").submit(function(event) {
            event.preventDefault();
            var element = $(this);

            $.ajax({
                url: '{{ route('track.order') }}',
                type: 'post',
                data: element.serialize(),
                dataType: 'json',
                success: function(response) {
                    $("#order-message").text('');
                    $("#customer-info").hide();
                    $(".track .step").removeClass("active");

                    if (response.status === 'success') {
                        $("#customer-name").text(response.name);
                        $("#customer-phone").text(response.phone);
                        $("#customer-info").show();

                        let status = response.order_status;

                        $(".track .step[data-status='OrderPlaced']").addClass("active");

                        if (status === 'confirm' || status === 'shipped') {
                            $(".track .step[data-status='Confirm']").addClass("active");
                        }
                        if (status === 'shipped') {
                            $(".track .step[data-status='Shipped']").addClass("active");
                            $(".track .step[data-status='Delivered']").addClass("active");
                        }
                    } else {
                        $("#order-message").text(response.message);
                    }
                },
                error: function() {
                    $("#order-message").text('Something went wrong, please try again.');
                }
            });
        });
    </script>
@endsection
