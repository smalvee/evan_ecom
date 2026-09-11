@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('orders.index') }}">Orders</a></li>
                    <li class="is-active">Create</li>
                </ul>
                <h4 class="a-page-title">Create Order</h4>
                <p class="a-page-desc">Create a new customer order.</p>
            </div>
        </div>

        <form id="createOrder" method="POST">
            @csrf

            <div class="a-card">
                <div class="a-card-head">
                    <h5>Customer Information</h5>
                </div>
                <div class="a-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label a-required">Name</label>
                                <input type="text" id="name" name="name" class="form-control">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label a-required">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label a-required">Address</label>
                                <input type="text" id="address" name="address" class="form-control">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label a-required">Delivery Charge</label>
                                <select class="form-select" id="shipping_method" name="shipping_method">
                                    <option value="">-- Select Shipping --</option>
                                    @foreach ($shippingCharge as $shipping)
                                        <option value="{{ $shipping->amount }}">
                                            {{ $shipping->location }} - ৳{{ $shipping->amount }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="a-card mt-3">
                <div class="a-card-head">
                    <h5>Products</h5>
                </div>
                <div class="table-responsive">
                    <table class="table all-package theme-table">
                        <thead>
                            <tr>
                                <th width="30%">Product</th>
                                <th width="15%">Price</th>
                                <th width="15%">Qty</th>
                                <th width="15%">Discount</th>
                                <th width="15%">Subtotal</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            <tr>
                                <td>
                                    <select id="products[0][id]" name="products[0][id]"
                                        class="form-control product-select">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product_info)
                                            <option value="{{ $product_info->id }}"
                                                data-price="{{ $product_info->selling_price }}">
                                                {{ $product_info->product->name }} ({{ $product_info->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="invalid-feedback"></p>
                                </td>
                                <td><input type="text" class="form-control price" readonly></td>
                                <td><input type="number" name="products[0][qty]" class="form-control qty"
                                        value="1" min="1">
                                    <p class="invalid-feedback"></p>
                                </td>
                                <td><input type="number" name="products[0][discount]" class="form-control dis"
                                        value="0" min="0" step="0.01">
                                </td>
                                <td><input type="text" class="form-control subtotal" readonly></td>
                                <td><button type="button" class="btn btn-danger btn-sm btn-remove"
                                        onclick="removeRow(this)"><i class="ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="a-card-body pt-0">
                    <button type="button" class="btn btn-success" onclick="addRow()"><i class="ri-add-line"></i>
                        Add Product</button>
                </div>
            </div>

            <div class="a-card mt-3">
                <div class="a-card-head">
                    <h5>Pricing Summary</h5>
                </div>
                <div class="a-card-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6 col-xl-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Sub Total</span>
                                <strong>৳ <span id="SubtotalText">0.00</span></strong>
                            </div>
                            <input type="hidden" name="sum_subTotal" id="sumSubTotalAmount">

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping</span>
                                <strong>৳ <span id="ShippingText">0.00</span></strong>
                            </div>
                            <input type="hidden" name="shipping_amount" id="ShippingAmount">

                            <div class="d-flex justify-content-between mb-3" style="visibility: hidden;">
                                <span class="text-muted">Discount</span>
                                <strong>৳ <span id="DiscountText">0.00</span></strong>
                            </div>
                            <input type="hidden" name="discount_amount" id="DiscountAmount">

                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong>৳ <span id="GrandTotalText">0.00</span></strong>
                            </div>
                            <input type="hidden" name="total_amount" id="GrandTotal">
                        </div>
                    </div>
                </div>
                <div class="a-card-footer text-end">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Create Order</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('dis')) {
                let value = parseFloat(e.target.value);

                // ❌ negative or empty → 0
                if (isNaN(value) || value < 0) {
                    e.target.value = 0;
                }

                calculateRow(e.target.closest('tr'));
            }

            if (e.target.classList.contains('qty')) {
                calculateRow(e.target.closest('tr'));
            }
        });
    </script>
    <script>
        let rowIndex = 1;

        function addRow() {
            let row = `
    <tr>
        <td>
            <select name="products[${rowIndex}][id]" class="form-control product-select">
                <option value="">Select Product</option>
                @foreach ($products as $product_info)
                    <option value="{{ $product_info->id }}" data-price="{{ $product_info->selling_price }}">
                        {{ $product_info->product->name }} ({{ $product_info->sku }})
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="text" class="form-control price" readonly></td>
        <td><input type="number" name="products[${rowIndex}][qty]" class="form-control qty" value="1"></td>
        <td><input type="number" name="products[${rowIndex}][discount]" class="form-control dis" value="0"></td>
        <td><input type="text" class="form-control subtotal" readonly></td>
        <td>
            <button type="button" class="btn btn-danger btn-remove" onclick="removeRow(this)">X</button>
        </td>
    </tr>`;

            document.getElementById('productRows')
                .insertAdjacentHTML('afterbegin', row);

            rowIndex++;
            updateRemoveButtons(); // ✅
        }

        /* Remove row */
        function removeRow(btn) {
            btn.closest('tr').remove();
            calculateTotal();
        }

        /* Product select */
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                let row = e.target.closest('tr');
                let price = e.target.options[e.target.selectedIndex].dataset.price || 0;

                row.querySelector('.price').value = price;
                calculateRow(row);
                updateRemoveButtons(); // ✅ IMPORTANT
            }
        });

        /* Qty or Discount change */
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty') || e.target.classList.contains('dis')) {
                calculateRow(e.target.closest('tr'));
            }
        });

        /* Shipping change */
        document.getElementById('shipping_method').addEventListener('change', function() {
            let shipping = parseFloat(this.value) || 0;
            document.getElementById('ShippingText').innerText = shipping.toFixed(2);
            document.getElementById('ShippingAmount').value = shipping.toFixed(2);
            calculateTotal();
        });

        /* Row calculation */
        function calculateRow(row) {
            let price = parseFloat(row.querySelector('.price').value) || 0;
            let qty = parseInt(row.querySelector('.qty').value) || 1;
            let discount = parseFloat(row.querySelector('.dis').value) || 0;

            let subtotal = (price * qty) - discount;
            if (subtotal < 0) subtotal = 0;

            row.querySelector('.subtotal').value = subtotal.toFixed(2);
            calculateTotal();
        }

        /* Grand total */
        function calculateTotal() {
            let subtotal = 0;
            let discount = 0;

            document.querySelectorAll('#productRows tr').forEach(row => {
                subtotal += parseFloat(row.querySelector('.subtotal').value) || 0;
                discount += parseFloat(row.querySelector('.dis').value) || 0;
            });

            let shipping = parseFloat(document.getElementById('ShippingAmount').value) || 0;
            let grandTotal = subtotal + shipping;

            // Update subtotal sum
            document.getElementById('SubtotalText').innerText = subtotal.toFixed(2);
            document.getElementById('sumSubTotalAmount').value = subtotal.toFixed(2);

            // Update discount
            document.getElementById('DiscountText').innerText = discount.toFixed(2);
            document.getElementById('DiscountAmount').value = discount.toFixed(2);

            // Update grand total
            document.getElementById('GrandTotalText').innerText = grandTotal.toFixed(2);
            document.getElementById('GrandTotal').value = grandTotal.toFixed(2);
        }
    </script>

    <script>
        function updateRemoveButtons() {
            document.querySelectorAll('#productRows tr').forEach(row => {
                const productSelect = row.querySelector('.product-select');
                const removeBtn = row.querySelector('.btn-remove');

                if (!productSelect || !removeBtn) return;

                // ❌ যদি product select না করা থাকে → remove hide
                if (productSelect.value === "") {
                    removeBtn.style.display = 'none';
                }
                // ✅ যদি product select করা থাকে → remove show
                else {
                    removeBtn.style.display = 'inline-block';
                }
            });
        }
    </script>
    <script>
        $("#createOrder").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            $.ajax({
                url: '{{ route('orders.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response["status"] == true) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Order Created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        location.reload();


                    } else {
                        var errors = response['errors'] || {};

                        // Clear old validation states
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').html('');

                        // Loop through errors and display them
                        $.each(errors, function(key, value) {
                            var input = $('#' + key); // Correct ID selector
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').html(
                                value); // Works if feedback div is right after input
                        });

                    }

                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });
    </script>
@endsection
