@extends('admin.layouts.new_app')

@section('content')
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
                    <form id="createPurchasereturn">
                        @csrf
                        <div class="card-body">

                            <h5>Purchase Information</h5>

                            <div class="row mb-3 align-items-end">
                                <div class="col-md-5">
                                    <label for="po_number" class="form-label">PO Number</label>
                                    <input type="number" id="purchase_id" name="purchase_id" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <button id="searchPurchase" class="btn btn-primary w-100">Search</button>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-end">
                                <div class="col-md-5">
                                    <label for="po_number" class="form-label">Supplier</label>
                                    <input type="text" id="supplier" class="form-control" readonly>
                                </div>
                                <div class="col-md-5">
                                    <label for="po_number" class="form-label">Date</label>
                                    <input type="date" id="purchase_date" class="form-control" readonly>
                                </div>
                            </div>

                            {{-- Product Table --}}
                            <table id="purchaseTable">
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

                            <button type="submit" class="btn btn-primary mt-4">
                                Save
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
                        let stock = item.variant ? item.variant.qty : 0;

                        $('#purchaseTable tbody').append(`
                    <tr>
                        <td>${item.variant?.sku ?? ''}
                            <input type="text" value="${item.variant_id}">
                            
                            </td>
                        <td>${item.p_name}</td>
                        <td>${item.qty}</td>
                        <td>${item.unit_cost}</td>
                        <td>${stock}</td>
                        <td>
                            <input 
                                type="number"
                                name="return_qty[${item.variant_id}]"
                                class="form-control"
                                min="0"
                                max="${stock}"
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
