@extends('admin.layouts.new_app')

@section('content')
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
                    <li><a href="{{ route('purchase.return_list') }}">Returns</a></li>
                    <li class="is-active">Create Return</li>
                </ul>
                <h4 class="a-page-title">Create Purchase Return</h4>
                <p class="a-page-desc">Select a purchase and enter the quantities to return.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('purchase.return_list') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <div class="a-card">
            <form id="createPurchasereturn">
                @csrf
                <div class="a-card-head">
                    <h5>Purchase Return</h5>
                </div>
                <div class="a-card-body">
                    <div class="a-form-section">
                        <h6 class="a-section-title">1. Select Purchase</h6>
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <label for="purchase_id" class="form-label">PO Number</label>
                                <input type="number" id="purchase_id" name="purchase_id" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <button id="searchPurchase" class="btn btn-theme w-100">Search</button>
                            </div>
                        </div>
                    </div>

                    <div class="a-form-section">
                        <h6 class="a-section-title">2. Purchase Details</h6>
                        <div class="row">
                            <div class="col-md-5">
                                <label for="supplier" class="form-label">Supplier</label>
                                <input type="text" id="supplier" class="form-control" readonly>
                            </div>
                            <div class="col-md-5">
                                <label for="purchase_date" class="form-label">Date</label>
                                <input type="date" id="purchase_date" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="a-form-section">
                        <h6 class="a-section-title">3. Return Items</h6>
                        <div class="table-responsive">
                            <table class="table all-package theme-table" id="purchaseTable">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Product</th>
                                        <th>Purchased Qty</th>
                                        <th>Unit Price</th>
                                        <th>Current Stock</th>
                                        <th>Return Qty</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="btn btn-theme">
                            Save
                        </button>
                        <a href="{{ route('purchase.return_list') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $('#searchPurchase').on('click', function(e) {
            e.preventDefault();

            let purchaseId = $('#purchase_id').val();

            $.ajax({
                url: "{{ route('purchase.search') }}",
                type: "GET",
                data: {
                    purchase_id: purchaseId
                },
                success: function(res) {

                    if (!res.status) {
                        alert(res.message);
                        return;
                    }

                    $('#supplier').val(res.purchase.supplier_id);
                    $('#purchase_date').val(res.purchase.date);

                    $('#purchaseTable tbody').empty();

                    res.purchase.items.forEach(item => {
                        let stock = item.variant ? Number(item.variant.qty) : 0;
                        let remaining = (item.remaining_qty !== undefined) ? Number(item.remaining_qty) : Number(item.qty);
                        let maxReturn = Math.max(0, Math.min(stock, remaining));

                        $('#purchaseTable tbody').append(`
                    <tr>
                        <td>${item.variant?.sku ?? ''}
                            <input type="hidden" value="${item.variant_id}">
                            </td>
                        <td>${item.p_name}</td>
                        <td>${item.qty}${remaining < Number(item.qty) ? ` <small class="text-muted">(remaining ${remaining})</small>` : ''}</td>
                        <td>${item.unit_cost}</td>
                        <td>${stock}</td>
                        <td>
                            <input 
                                type="number"
                                name="return_qty[${item.variant_id}]"
                                class="form-control"
                                min="0"
                                max="${maxReturn}"
                            >
                        </td>
                    </tr>
                `);
                    });
                }
            });
        });
    </script>






    <script>
        document.getElementById('createPurchasereturn').addEventListener('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('purchase.return_store') }}',
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
                            text: 'Purchase return saved successfully.',
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
