@extends('admin.layouts.new_app')

@section('content')
    <?php
    
    use App\Models\ProductVariant;
    
    ?>
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
                    <li class="is-active">Edit</li>
                </ul>
                <h4 class="a-page-title">Edit Purchase</h4>
                <p class="a-page-desc">Update the purchase order details.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <form method="post" id="updatePurchase" name="updatePurchase">
                @csrf
                <div class="a-card-head">
                    <h5>Edit Purchase</h5>
                </div>
                <div class="a-card-body">
                    <div class="a-form-section">
                        <h6 class="a-section-title">Supplier & Date</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_name" class="form-label a-required">Supplier</label>
                                    <input list="supplier_list" id="supplier_name" class="form-control"
                                        value="{{ $supplier_info->name }}">
                                    <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $supplier_info->id }}">
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
                                    <input type="date" name="date" class="form-control"
                                        value="{{ $purchase_info->date }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="a-form-section">
                        <h6 class="a-section-title">Product Selection</h6>
                        <div id="purchase_info"
                            style="display:none; border:1px solid var(--a-border); border-radius: var(--a-radius-sm); padding:15px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Product</label>
                                    <input type="text" id="p_name" class="form-control" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Qty</label>
                                    <input type="number" id="qty" class="form-control calc" value="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Unit Cost</label>
                                    <input type="number" id="unit_cost" class="form-control calc">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Profit</label>
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
                                    <label class="form-label">Selling</label>
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
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Cost</th>
                                        <th>Profit</th>
                                        <th>Discount</th>
                                        <th>MRP</th>
                                        <th>Selling</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Existing Items --}}
                                    @foreach ($purchaseItems as $item)
                                        @php $variant_info = $item->variant; @endphp
                                        <tr>
                                            <td>
                                                {{ $variant_info->sku }}
                                                <br>
                                                <input type="hidden" name="variant_id[]" value="{{ $variant_info->id }}">
                                                <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                                <strong>{{ $variant_info->product->name }}</strong>
                                            </td>
                                            <td><input type="number" name="qty[]" value="{{ $item->qty }}"
                                                    class="form-control row-calc" readonly></td>
                                            <td><input type="number" name="unit_cost[]" value="{{ $item->unit_cost }}"
                                                    class="form-control row-calc"></td>
                                            <td><input type="number" name="profit_amount[]"
                                                    value="{{ $item->profit_margin }}" class="form-control row-calc"></td>
                                            <td><input type="number" name="discount[]" value="{{ $item->discount }}"
                                                    class="form-control row-calc"></td>
                                            <td><input type="number" name="compare_price[]"
                                                    value="{{ $variant_info->compare_price }}" class="form-control"
                                                    readonly></td>
                                            <td><input type="number" name="selling_price[]"
                                                    value="{{ $item->selling_price }}" class="form-control" readonly></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4 offset-md-8">
                                <label class="form-label"><strong>Total Purchase Amount</strong></label>
                                <input type="number" id="total_purchase" name="total_purchase"
                                    value="{{ $purchase_info->total }}" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="btn btn-theme">
                            Update Purchase
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

            const totalField = document.getElementById('total_purchase');

            function recalcRow(row) {
                let q = +row.querySelector('[name="qty[]"]').value || 0;
                let c = +row.querySelector('[name="unit_cost[]"]').value || 0;
                let p = +row.querySelector('[name="profit_amount[]"]').value || 0;
                let d = +row.querySelector('[name="discount[]"]').value || 0;

                let base = c;
                let mrp = base + p;
                let sell = base + p - d;

                row.querySelector('[name="compare_price[]"]').value = Math.round(mrp);
                row.querySelector('[name="selling_price[]"]').value = Math.round(sell);

                calculateTotalPurchase(); // Update total after recalculating row
            }

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

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('row-calc')) {
                    recalcRow(e.target.closest('tr'));
                }
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-btn')) {
                    e.target.closest('tr').remove();
                    calculateTotalPurchase(); // Update total after deleting row
                }
            });

            $('#updatePurchase').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('purchase.update', $purchase_info->id) }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Purchase updated successfully!',
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
                    }
                });
            });

            // Initial total calculation on page load
            calculateTotalPurchase();

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
@endsection
