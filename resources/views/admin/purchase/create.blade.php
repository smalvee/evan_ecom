@extends('admin.layouts.new_app')

@section('content')
    @include('admin.partials.product-search')

    <style>
        .purchase-items-table th,
        .purchase-items-table td {
            vertical-align: middle;
        }

        .purchase-product-cell {
            min-width: 260px;
        }

        .purchase-item-sku {
            font-size: 12px;
            color: var(--a-muted);
            white-space: nowrap;
        }

        .purchase-items-table .form-control {
            min-width: 78px;
        }

        /* Summary */
        .purchase-summary {
            padding: 14px 16px;
            border: 1px solid var(--a-border);
            border-radius: var(--a-radius-sm);
            background: #fff;
        }

        .purchase-sum-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            color: var(--a-text);
        }

        .purchase-sum-row + .purchase-sum-row {
            border-top: 1px dashed var(--a-border);
        }

        .purchase-sum-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
            padding-top: 10px;
            border-top: 2px solid var(--a-border);
            font-size: 18px;
            font-weight: 700;
            color: var(--a-text);
        }

        .purchase-sum-total span:last-child {
            color: var(--a-primary);
        }
    </style>

    @php
        $productOptions = $product_sku
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'name' => $v->product?->name ?? '',
                    'sku' => $v->sku,
                    // For purchasing, show/prefill the current purchase cost.
                    'price' => (float) $v->purchase_price,
                    'stock' => $v->stock(),
                    'allow_pre_order' => (bool) $v->allow_pre_order,
                    'active' => (bool) ($v->product?->status ?? 1),
                ];
            })
            ->values()
            ->all();
    @endphp

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
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary"><i
                        class="ri-arrow-left-line"></i> Back</a>
            </div>
        </div>

        <form id="createPurchase">
            @csrf

            {{-- Compact purchase information --}}
            <div class="a-card mb-3">
                <div class="a-card-head">
                    <h5>Purchase Information</h5>
                </div>
                <div class="a-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label a-required" for="supplier_id">Supplier</label>
                            <select id="supplier_id" name="supplier_id" class="form-select js-select2">
                                <option value="">Select Supplier</option>
                                @foreach ($supplier as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <p class="invalid-feedback"></p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label a-required" for="date">Purchase Date</label>
                            <input type="date" id="date" name="date" class="form-control"
                                value="{{ now()->toDateString() }}">
                            <p class="invalid-feedback"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Purchase items (main section) --}}
            <div class="a-card">
                <div class="a-card-head">
                    <h5>Purchase Items</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="a-badge a-badge-neutral"><span class="dot"></span><span id="itemCount">0</span>
                            items</span>
                        <button type="button" class="btn btn-theme btn-sm" id="addProductBtn">
                            <i class="ri-add-line"></i> Add Product
                        </button>
                    </div>
                </div>

                <div id="itemsTableWrap" class="table-responsive">
                    <table class="table all-package theme-table purchase-items-table">
                        <thead>
                            <tr>
                                <th>Product / Variant</th>
                                <th>SKU</th>
                                <th style="width:130px;">Unit Cost</th>
                                <th style="width:100px;">Qty</th>
                                <th style="width:120px;">Discount</th>
                                <th style="width:120px;">Profit</th>
                                <th class="text-end">Total</th>
                                <th class="text-end" style="width:70px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseRows"></tbody>
                    </table>
                </div>

                <div id="emptyItems" class="a-empty" hidden>
                    <div class="a-empty-icon"><i class="ri-shopping-cart-2-line"></i></div>
                    <h5>No products added</h5>
                    <p>Click “Add Product” to start adding items to this purchase.</p>
                    <button type="button" class="btn btn-theme btn-sm" id="addProductEmpty">
                        <i class="ri-add-line"></i> Add Product
                    </button>
                </div>

                <div class="a-card-body pt-3">
                    <div class="row">
                        <div class="col-md-5 offset-md-7">
                            <div class="purchase-summary">
                                <div class="purchase-sum-row">
                                    <span>Subtotal</span>
                                    <span>৳ <span id="subtotalText">0.00</span></span>
                                </div>
                                <div class="purchase-sum-row">
                                    <span>Item Discount</span>
                                    <span>৳ <span id="discountText">0.00</span></span>
                                </div>
                                <div class="purchase-sum-total">
                                    <span>Grand Total</span>
                                    <span>৳ <span id="grandTotalText">0.00</span></span>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Item discount affects the selling price, not the purchase amount.
                                </small>

                                <input type="hidden" id="total_purchase" name="total_purchase">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="submit" class="btn btn-theme">
                            <i class="ri-save-3-line"></i> Save Purchase
                        </button>
                        <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        const PURCHASE_PRODUCTS = @json($productOptions);
    </script>

    <script>
        (function() {
            'use strict';

            const rowsBody = document.getElementById('purchaseRows');
            const tableWrap = document.getElementById('itemsTableWrap');
            const emptyState = document.getElementById('emptyItems');
            const itemCount = document.getElementById('itemCount');
            const addButtons = [
                document.getElementById('addProductBtn'),
                document.getElementById('addProductEmpty')
            ];

            const money = (value) => (Math.round((parseFloat(value) || 0) * 100) / 100).toFixed(2);
            const round2 = (value) => Math.round((parseFloat(value) || 0) * 100) / 100;
            const escapeHtml = (value) => String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            /* ---------- Rows ---------- */
            function findRowByVariant(id, exceptRow) {
                return Array.from(rowsBody.querySelectorAll('.purchase-row')).find(
                    (row) => row !== exceptRow && row.dataset.variantId === String(id)
                );
            }

            function recalcRow(row) {
                const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
                const cost = parseFloat(row.querySelector('.row-cost').value) || 0;
                const profit = parseFloat(row.querySelector('.row-profit').value) || 0;
                const discount = parseFloat(row.querySelector('.row-discount').value) || 0;

                const total = qty * cost;
                const mrp = cost + profit;
                const selling = Math.max(0, cost + profit - discount);

                row.querySelector('.row-total').innerText = '৳ ' + money(total);
                row.querySelector('.row-mrp').value = round2(mrp);
                row.querySelector('.row-selling').value = round2(selling);

                return total;
            }

            function recalcAll() {
                let subtotal = 0;
                let discount = 0;
                let count = 0;

                rowsBody.querySelectorAll('.purchase-row').forEach(function(row) {
                    subtotal += recalcRow(row);
                    discount += parseFloat(row.querySelector('.row-discount').value) || 0;
                    count++;
                });

                document.getElementById('subtotalText').innerText = money(subtotal);
                document.getElementById('discountText').innerText = money(discount);
                document.getElementById('grandTotalText').innerText = money(subtotal);
                document.getElementById('total_purchase').value = money(subtotal);

                itemCount.innerText = count;

                const hasRows = count > 0;
                tableWrap.hidden = !hasRows;
                emptyState.hidden = hasRows;
            }

            function clearRowSelection(row) {
                row.dataset.variantId = '';
                row.dataset.selectedName = '';
                row.querySelector('.row-variant-id').value = '';
                row.querySelector('.row-p-name').value = '';
                row.querySelector('.row-sku').textContent = '—';
            }

            function selectProduct(row, product) {
                // Variants must be distinct (backend rule): merge into the existing row.
                const duplicate = findRowByVariant(product.id, row);
                if (duplicate) {
                    const qtyInput = duplicate.querySelector('.row-qty');
                    qtyInput.value = (parseInt(qtyInput.value, 10) || 0) +
                        (parseInt(row.querySelector('.row-qty').value, 10) || 1);

                    if (row._autocomplete && row._autocomplete.destroy) row._autocomplete.destroy();
                    row.remove();
                    recalcAll();

                    duplicate.scrollIntoView({
                        block: 'nearest'
                    });
                    duplicate.querySelector('.row-qty').focus();
                    return;
                }

                row.dataset.variantId = product.id;
                row.dataset.selectedName = product.name;
                row.querySelector('.row-variant-id').value = product.id;
                row.querySelector('.row-p-name').value = product.name;
                row.querySelector('.row-sku').textContent = product.sku || '—';
                row.querySelector('.purchase-product-input').value = product.name;
                row.querySelector('.row-cost').value = product.price || 0;

                recalcAll();
                row.querySelector('.row-qty').focus();
                row.querySelector('.row-qty').select();
            }

            function addRow() {
                const row = document.createElement('tr');
                row.className = 'purchase-row';
                row.innerHTML = `
                    <td class="purchase-product-cell">
                        <div class="pos-search pos-search-sm">
                            <div class="pos-search-field">
                                <i class="ri-search-line"></i>
                                <input type="text" class="form-control purchase-product-input"
                                    placeholder="Search product name or SKU…" autocomplete="off"
                                    aria-label="Search product">
                            </div>
                            <div class="pos-search-results purchase-product-results" hidden></div>
                        </div>
                        <input type="hidden" name="p_name[]" class="row-p-name">
                        <input type="hidden" name="variant_id[]" class="row-variant-id">
                        <input type="hidden" name="mrp[]" class="row-mrp" value="0">
                        <input type="hidden" name="selling_price[]" class="row-selling" value="0">
                    </td>
                    <td class="purchase-item-sku row-sku">—</td>
                    <td>
                        <input type="number" name="unit_cost[]" class="form-control row-cost" value="0" min="0" step="0.01" aria-label="Unit cost">
                    </td>
                    <td>
                        <input type="number" name="qty[]" class="form-control row-qty" value="1" min="1" aria-label="Quantity">
                    </td>
                    <td>
                        <input type="number" name="discount[]" class="form-control row-discount" value="0" min="0" step="0.01" aria-label="Discount">
                    </td>
                    <td>
                        <input type="number" name="profit_amount[]" class="form-control row-profit" value="0" min="0" step="0.01" aria-label="Profit">
                    </td>
                    <td class="text-end row-total">৳ 0.00</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm row-remove" aria-label="Remove item">
                            <i class="ri-delete-bin-6-line"></i>
                        </button>
                    </td>
                `;

                rowsBody.appendChild(row);
                recalcAll();

                const input = row.querySelector('.purchase-product-input');
                const results = row.querySelector('.purchase-product-results');

                // If the admin edits the text after picking, the selection is cleared.
                input.addEventListener('input', function() {
                    if (row.dataset.selectedName && input.value !== row.dataset.selectedName) {
                        clearRowSelection(row);
                    }
                });

                const autocomplete = ProductAutocomplete.create({
                    input: input,
                    results: results,
                    items: PURCHASE_PRODUCTS,
                    minWidth: 320,
                    onSelect: function(product) {
                        selectProduct(row, product);
                        autocomplete.close();
                    }
                });

                row._autocomplete = autocomplete;
                input.focus();
            }

            /* ---------- Row input + remove (event delegation) ---------- */
            rowsBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('row-qty') || e.target.classList.contains('row-cost') ||
                    e.target.classList.contains('row-profit') || e.target.classList.contains('row-discount')) {
                    recalcAll();
                }
            });

            rowsBody.addEventListener('click', function(e) {
                const remove = e.target.closest('.row-remove');
                if (remove) {
                    const row = remove.closest('tr');
                    if (row._autocomplete && row._autocomplete.destroy) row._autocomplete.destroy();
                    row.remove();
                    recalcAll();
                }
            });

            addButtons.forEach(function(btn) {
                if (btn) btn.addEventListener('click', addRow);
            });

            /* ---------- Submit (existing AJAX contract) ---------- */
            document.getElementById('createPurchase').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const rows = Array.from(rowsBody.querySelectorAll('.purchase-row'));

                if (!rows.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Add at least one product'
                    });
                    return;
                }

                const emptyRow = rows.find((row) => !row.dataset.variantId);
                if (emptyRow) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Select a product for every row'
                    });
                    emptyRow.querySelector('.purchase-product-input').focus();
                    return;
                }

                $.ajax({
                    url: '{{ route('purchase.store') }}',
                    type: 'POST',
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $('.form-control, .form-select').removeClass('is-invalid');
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
                            let firstMessage = '';

                            for (const key in errors) {
                                if (!firstMessage) firstMessage = errors[key][0];

                                let input = $('#' + key);
                                if (!input.length) input = $('[name="' + key + '"]');
                                if (!input.length) input = $('[name="' + key + '[]"]');

                                input.first().addClass('is-invalid');
                                input.first().next('.invalid-feedback').html(errors[key][0]);
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Please fix the errors',
                                text: firstMessage || 'Some fields need your attention.'
                            });
                        }
                    },
                    error: function(err) {
                        console.error('Form submit error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            text: 'Unable to save the purchase. Please try again.'
                        });
                    }
                });
            });

            /* ---------- Init ---------- */
            addRow();

            if (window.initSelect2) {
                window.initSelect2('.js-select2');
            }
        })();
    </script>
@endsection
