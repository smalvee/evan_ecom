@extends('admin.layouts.new_app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .delete-btn {
            cursor: pointer;
            color: red;
            font-weight: bold;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-10 m-auto">
                <div class="card">
                    <form id="createPurchase">
                        @csrf
                        <div class="card-body">

                            <h5>Purchase Information</h5>

                            {{-- Supplier --}}
                            <div class="mb-3">
                                <label>Supplier</label>
                                <input list="supplier_list" id="supplier_name" class="form-control">
                                <input type="hidden" name="supplier_id" id="supplier_id">
                                <datalist id="supplier_list">
                                    @foreach ($supplier as $s)
                                        <option value="{{ $s->name }}" data-id="{{ $s->id }}"></option>
                                    @endforeach
                                </datalist>
                            </div>

                            {{-- Date --}}
                            <div class="mb-3">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control" style="max-width: 250px">
                            </div>

                            {{-- Product SKU --}}
                            <div class="mb-3">
                                <label>Product SKU</label>
                                <input list="product_list" id="product_input" class="form-control">
                                <input type="hidden" id="variant_id">
                                <datalist id="product_list">
                                    @foreach ($product_sku as $p)
                                        <option value="{{ $p->sku }}" data-id="{{ $p->id }}"
                                            data-name="{{ $p->product->name }}" data-cost="0">
                                        </option>
                                    @endforeach
                                </datalist>
                            </div>

                            {{-- Product Entry --}}
                            <div id="purchase_info" style="display:none;border:1px solid #ddd;padding:15px;">
                                <div class="row">

                                    <div class="col-md-3">
                                        <label>Product</label>
                                        <input type="text" id="p_name" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Purchase Qty</label>
                                        <input type="number" id="qty" class="form-control calc" value="1"
                                            min="1">
                                    </div>

                                    <div class="col-md-2">
                                        <label>Unit Cost</label>
                                        <input type="number" id="unit_cost" class="form-control calc">
                                    </div>

                                    <div class="col-md-2">
                                        <label>Profit Amount</label>
                                        <input type="number" id="profit_amount" class="form-control calc" value="0">
                                    </div>

                                    <div class="col-md-2">
                                        <label>Discount</label>
                                        <input type="number" id="discount" class="form-control calc" value="0">
                                    </div>

                                    <div class="col-md-2 mt-2">
                                        <label>MRP</label>
                                        <input type="number" id="mrp" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-2 mt-2">
                                        <label>Selling Price</label>
                                        <input type="number" id="selling_price" class="form-control" readonly>
                                    </div>

                                </div>

                                <button type="button" class="btn btn-success mt-3" id="addProduct">
                                    Add Product
                                </button>
                            </div>


                            {{-- Product Table --}}
                            <table id="purchaseTable">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Unit Cost</th>
                                        <th>Profit</th>
                                        <th>Discount</th>
                                        <th>MRP</th>
                                        <th>Selling</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div class="row mt-3">
                                <div class="col-md-4 offset-md-8">
                                    <label><strong>Total Purchase Amount</strong></label>
                                    <input type="number" id="total_purchase" name="total_purchase" class="form-control" readonly>
                                </div>
                            </div>


                            <button type="submit" class="btn btn-primary mt-4">
                                Save Purchase
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ================== ELEMENTS ================== */
            const productInput = document.getElementById('product_input');
            const productList = document.getElementById('product_list');
            const purchaseInfo = document.getElementById('purchase_info');

            const pName = document.getElementById('p_name');
            const qty = document.getElementById('qty');
            const cost = document.getElementById('unit_cost');
            const profit = document.getElementById('profit_amount');
            const discount = document.getElementById('discount');
            const mrp = document.getElementById('mrp');
            const selling = document.getElementById('selling_price');
            const variantId = document.getElementById('variant_id');

            const tableBody = document.querySelector('#purchaseTable tbody');
            const totalField = document.getElementById('total_purchase');

            /* ================== PRODUCT SELECT ================== */
            productInput.addEventListener('input', function() {

                const value = this.value.trim();
                const option = [...productList.options].find(o => o.value === value);

                if (!option) {
                    purchaseInfo.style.display = 'none';
                    variantId.value = '';
                    return;
                }

                purchaseInfo.style.display = 'block';

                pName.value = option.dataset.name;
                cost.value = option.dataset.cost || 0;
                variantId.value = option.dataset.id;

                calculatePrices();
            });

            /* ================== CALCULATION ================== */
            document.querySelectorAll('.calc').forEach(el => {
                el.addEventListener('input', calculatePrices);
            });

            function calculatePrices() {
                let q = +qty.value || 0;
                let c = +cost.value || 0;
                let pr = +profit.value || 0;
                let d = +discount.value || 0;

                let base = c;
                let m = base + pr;
                let s = base + pr - d;

                if (s < 0) s = 0;

                mrp.value = Math.round(m);
                selling.value = Math.round(s);
            }

            /* ================== ADD PRODUCT ================== */
            document.getElementById('addProduct').addEventListener('click', function() {

                if (!variantId.value) {
                    alert('Select valid product');
                    return;
                }

                let row = `
        <tr>
            <td>${productInput.value}</td>

            <td>
                <input type="text" name="p_name[]" value="${pName.value}" class="form-control" readonly>
            </td>

            <td>
                <input type="number" name="qty[]" value="${qty.value}" class="form-control" readonly>
            </td>

            <td>
                <input type="number" name="unit_cost[]" value="${cost.value}" class="form-control" readonly>
            </td>

            <td>
                <input type="number" name="profit_amount[]" value="${profit.value}" class="form-control" readonly>
            </td>

            <td>
                <input type="number" name="discount[]" value="${discount.value}" class="form-control" readonly>
            </td>

            <td>
                <input type="number" name="mrp[]" value="${mrp.value}" class="form-control" readonly>
            </td>

            <td>
                <input type="number" name="selling_price[]" value="${selling.value}" class="form-control" readonly>
            </td>

            <td>
                <input type="hidden" name="variant_id[]" value="${variantId.value}">
                <span class="delete-btn">✖</span>
            </td>
        </tr>`;

                tableBody.insertAdjacentHTML('beforeend', row);

                calculateTotalPurchase(); // ✅ update total

                // reset
                purchaseInfo.style.display = 'none';
                productInput.value = '';
                variantId.value = '';
            });

            /* ================== DELETE ROW ================== */
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-btn')) {
                    e.target.closest('tr').remove();
                    calculateTotalPurchase(); // ✅ update total
                }
            });

            /* ================== TOTAL PURCHASE ================== */
            function calculateTotalPurchase() {
                let total = 0;

                document.querySelectorAll('#purchaseTable tbody tr').forEach(row => {
                    let q = +row.querySelector('input[name="qty[]"]').value || 0;
                    let c = +row.querySelector('input[name="unit_cost[]"]').value || 0;
                    total += q * c;
                });

                if (totalField) {
                    totalField.value = Math.round(total);
                }
            }

        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const supplierInput = document.getElementById('supplier_name');
            const supplierList = document.getElementById('supplier_list');
            const supplierId = document.getElementById('supplier_id');

            supplierInput.addEventListener('input', function() {

                const value = this.value.trim();
                const option = [...supplierList.options].find(o => o.value === value);

                if (!option) {
                    supplierId.value = '';
                    return;
                }

                supplierId.value = option.dataset.id; // ✅ FIXED
            });

        });
    </script>



    <script>
        document.getElementById('createPurchase').addEventListener('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('purchase.store') }}',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').html('');

                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Purchase created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        const errors = response.errors || {};
                        for (let key in errors) {
                            const input = $('#' + key);
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').html(errors[key][0]);
                        }
                    }
                },
                error: function(err) {
                    console.error("Form submit error:", err);
                }
            });
        });
    </script>
@endsection
