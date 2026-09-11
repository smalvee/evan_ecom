@extends('admin.layouts.new_app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .delete-btn {
            cursor: pointer;
            color: var(--a-danger);
            font-weight: bold;
        }
    </style>

    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('purchase.index') }}">Purchases</a></li>
                    <li class="is-active">Create</li>
                </ul>
                <h4 class="a-page-title">Create Purchase</h4>
                <p class="a-page-desc">Record a new purchase order and its products.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <form id="createPurchase">
                @csrf
                <div class="a-card-head">
                    <h5>Purchase Information</h5>
                </div>
                <div class="a-card-body">
                    <div class="a-form-section">
                        <h6 class="a-section-title">Supplier & Date</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_name" class="form-label a-required">Supplier</label>
                                    <input list="supplier_list" id="supplier_name" class="form-control">
                                    <input type="hidden" name="supplier_id" id="supplier_id">
                                    <datalist id="supplier_list">
                                        @foreach ($supplier as $s)
                                            <option value="{{ $s->name }}" data-id="{{ $s->id }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label a-required">Date</label>
                                    <input type="date" name="date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="a-form-section">
                        <h6 class="a-section-title">Product Selection</h6>
                        <div class="mb-3">
                            <label for="product_input" class="form-label">Product SKU</label>
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

                        <div id="purchase_info"
                            style="display:none; border:1px solid var(--a-border); border-radius: var(--a-radius-sm); padding:15px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Product</label>
                                    <input type="text" id="p_name" class="form-control" readonly>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Purchase Qty</label>
                                    <input type="number" id="qty" class="form-control calc" value="1" min="1">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Unit Cost</label>
                                    <input type="number" id="unit_cost" class="form-control calc">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Profit Amount</label>
                                    <input type="number" id="profit_amount" class="form-control calc" value="0">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Discount</label>
                                    <input type="number" id="discount" class="form-control calc" value="0">
                                </div>

                                <div class="col-md-2 mt-2">
                                    <label class="form-label">MRP</label>
                                    <input type="number" id="mrp" class="form-control" readonly>
                                </div>

                                <div class="col-md-2 mt-2">
                                    <label class="form-label">Selling Price</label>
                                    <input type="number" id="selling_price" class="form-control" readonly>
                                </div>
                            </div>

                            <button type="button" class="btn btn-theme mt-3" id="addProduct">
                                <i class="ri-add-line"></i> Add Product
                            </button>
                        </div>
                    </div>

                    <div class="a-form-section">
                        <h6 class="a-section-title">Products</h6>
                        <div class="table-responsive">
                            <table class="table all-package theme-table" id="purchaseTable">
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
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4 offset-md-8">
                                <label class="form-label"><strong>Total Purchase Amount</strong></label>
                                <input type="number" id="total_purchase" name="total_purchase" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="btn btn-theme">
                            Save Purchase
                        </button>
                        <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </form>
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
