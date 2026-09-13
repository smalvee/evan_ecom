@extends('admin.layouts.new_app')

@section('content')
    @include('admin.partials.product-search')

    <style>
        /* ---------- POS layout ---------- */
        .pos-summary {
            position: sticky;
            top: 80px;
        }

        @media (max-width: 991.98px) {
            .pos-summary {
                position: static;
            }
        }

        /* Search hero: make the product search the visual focus. */
        .pos-search-hero {
            background: #f1f7f6;
            border-bottom: 1px solid var(--a-border);
        }

        /* Compact customer information bar. */
        .pos-customer .a-card-body {
            padding: 14px 16px;
        }

        .pos-customer-title {
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--a-muted);
        }

        .pos-customer-title i {
            margin-right: 4px;
        }

        /* ---------- POS items table ---------- */
        .pos-items-table th,
        .pos-items-table td {
            vertical-align: middle;
        }

        .pos-item-name {
            font-weight: 600;
            color: var(--a-text);
        }

        .pos-item-sku {
            font-size: 12px;
            color: var(--a-muted);
        }

        .pos-price,
        .pos-subtotal {
            white-space: nowrap;
        }

        .pos-subtotal {
            font-weight: 600;
        }

        .pos-qty {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pos-qty .qty {
            width: 56px;
            text-align: center;
        }

        .pos-qty-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border: 1px solid var(--a-border);
            border-radius: 8px;
            background: #fff;
            color: var(--a-text);
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
        }

        .pos-qty-btn:hover {
            background: var(--a-primary);
            border-color: var(--a-primary);
            color: #fff;
        }

        /* ---------- Order summary ---------- */
        .pos-sum-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            color: var(--a-text);
        }

        .pos-sum-row + .pos-sum-row {
            border-top: 1px dashed var(--a-border);
        }

        .pos-sum-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            padding-top: 12px;
            border-top: 2px solid var(--a-border);
            font-size: 20px;
            font-weight: 700;
            color: var(--a-text);
        }

        .pos-sum-total span:last-child {
            color: var(--a-primary);
        }
    </style>

    @php
        $productOptions = $products
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'name' => $v->product?->name ?? '',
                    'sku' => $v->sku,
                    'price' => (float) $v->selling_price,
                    'free_delivery' => (bool) ($v->product?->free_delivery ?? false),
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
                    <li><a href="{{ route('orders.index') }}">Orders</a></li>
                    <li class="is-active">Create</li>
                </ul>
                <h4 class="a-page-title">Create Order</h4>
                <p class="a-page-desc">Search products, build the order and take payment details in one place.</p>
            </div>
        </div>

        <form id="createOrder" method="POST">
            @csrf

            {{-- Compact customer information (full width, minimal height) --}}
            <div class="a-card pos-customer mb-3">
                <div class="a-card-body">
                    <div class="pos-customer-title"><i class="ri-user-3-line"></i> Customer Information</div>
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label a-required mb-1" for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" autocomplete="off">
                            <p class="invalid-feedback"></p>
                        </div>

                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label a-required mb-1" for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control" autocomplete="off">
                            <p class="invalid-feedback"></p>
                        </div>

                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label a-required mb-1" for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-control" autocomplete="off">
                            <p class="invalid-feedback"></p>
                        </div>

                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label a-required mb-1" for="shipping_method">Delivery Charge</label>
                            <select class="form-select js-select2" id="shipping_method" name="shipping_method">
                                <option value="">-- Select Shipping --</option>
                                @foreach ($shippingCharge as $shipping)
                                    <option value="{{ $shipping->amount }}">
                                        {{ $shipping->location }} - ৳{{ $shipping->amount }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="invalid-feedback"></p>
                        </div>
                    </div>

                    <small id="freeDeliveryHint" class="text-success d-none mt-2">
                        <i class="ri-truck-line"></i> All selected items are free delivery — no delivery charge will be
                        applied.
                    </small>
                </div>
            </div>

            <div class="row g-3">
                {{-- Main area: product search + cart (visual focus) --}}
                <div class="col-lg-8 col-xxl-9">
                    <div class="a-card">
                        <div class="a-card-head">
                            <h5>Products</h5>
                            <span class="a-badge a-badge-neutral"><span class="dot"></span><span
                                    id="itemCount">0</span> items</span>
                        </div>

                        <div class="a-card-body pos-search-hero pb-3">
                            <div class="pos-search">
                                <div class="pos-search-field">
                                    <i class="ri-search-line"></i>
                                    <input type="text" id="productSearch" class="form-control"
                                        placeholder="Search product name or SKU…" autocomplete="off"
                                        aria-autocomplete="list" aria-controls="productSearchResults">
                                </div>
                                <div id="productSearchResults" class="pos-search-results" hidden></div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="ri-keyboard-line"></i> Press <kbd>/</kbd> to focus, <kbd>Enter</kbd> to add the
                                highlighted product, <kbd>Enter</kbd> in quantity to search again.
                            </small>
                        </div>

                        <div id="itemsTableWrap" class="table-responsive" hidden>
                            <table class="table all-package theme-table pos-items-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Price</th>
                                        <th style="width:150px;">Qty</th>
                                        <th style="width:130px;">Discount</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end" style="width:70px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="productRows"></tbody>
                            </table>
                        </div>

                        <div id="emptyItems" class="a-empty">
                            <div class="a-empty-icon"><i class="ri-shopping-bag-3-line"></i></div>
                            <h5>No products added</h5>
                            <p>Search above by product name or SKU to add items to the order.</p>
                        </div>
                    </div>
                </div>

                {{-- Right: sticky order summary --}}
                <div class="col-lg-4 col-xxl-3">
                    <div class="pos-summary">
                        <div class="a-card">
                            <div class="a-card-head">
                                <h5>Order Summary</h5>
                            </div>
                            <div class="a-card-body">
                                <div class="pos-sum-row">
                                    <span>Items</span>
                                    <span><span id="summaryItemCount">0</span></span>
                                </div>
                                <div class="pos-sum-row">
                                    <span>Subtotal</span>
                                    <span>৳ <span id="SubtotalText">0.00</span></span>
                                </div>
                                <div class="pos-sum-row">
                                    <span>Delivery Charge</span>
                                    <span>৳ <span id="ShippingText">0.00</span></span>
                                </div>
                                <div class="pos-sum-row">
                                    <span>Discount</span>
                                    <span>- ৳ <span id="DiscountText">0.00</span></span>
                                </div>
                                <div class="pos-sum-total">
                                    <span>Grand Total</span>
                                    <span>৳ <span id="GrandTotalText">0.00</span></span>
                                </div>

                                <input type="hidden" name="sum_subTotal" id="sumSubTotalAmount">
                                <input type="hidden" name="shipping_amount" id="ShippingAmount">
                                <input type="hidden" name="discount_amount" id="DiscountAmount">
                                <input type="hidden" name="total_amount" id="GrandTotal">
                            </div>
                            <div class="a-card-footer">
                                <button type="submit" class="btn btn-theme btn-lg w-100">
                                    <i class="ri-save-3-line"></i> Create Order
                                </button>
                                <a href="{{ route('orders.index') }}"
                                    class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        const PRODUCTS = @json($productOptions);
    </script>

    <script>
        (function() {
            'use strict';

            const form = document.getElementById('createOrder');
            const rowsBody = document.getElementById('productRows');
            const emptyState = document.getElementById('emptyItems');
            const tableWrap = document.getElementById('itemsTableWrap');
            const itemCount = document.getElementById('itemCount');
            const searchInput = document.getElementById('productSearch');
            const resultsBox = document.getElementById('productSearchResults');

            let rowCounter = 0;
            let autocomplete = null;

            const money = (value) => (Math.round((parseFloat(value) || 0) * 100) / 100).toFixed(2);
            const escapeHtml = (value) => String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            /* ---------- Rows ---------- */
            function findRowByVariant(id) {
                return Array.from(rowsBody.querySelectorAll('.item-row')).find(
                    (row) => row.dataset.variantId === String(id)
                );
            }

            function addProduct(product) {
                // Prevent duplicate rows: bump the quantity instead.
                const existing = findRowByVariant(product.id);

                if (existing) {
                    const qtyInput = existing.querySelector('.qty');
                    qtyInput.value = (parseInt(qtyInput.value, 10) || 0) + 1;
                    calculateRow(existing);
                    qtyInput.focus();
                    qtyInput.select();
                    return;
                }

                const index = rowCounter++;
                const row = document.createElement('tr');
                row.className = 'item-row';
                row.dataset.variantId = product.id;
                row.dataset.price = product.price;
                row.dataset.freeDelivery = product.free_delivery ? '1' : '0';
                row.dataset.stock = product.stock === null ? '' : product.stock;
                row.dataset.allowPreOrder = product.allow_pre_order ? '1' : '0';
                row.dataset.active = product.active === false ? '0' : '1';
                row.innerHTML = `
                    <td>
                        <div class="pos-item-name">${escapeHtml(product.name)}</div>
                        <div class="pos-item-sku">SKU: ${escapeHtml(product.sku)} ${ProductAutocomplete.badgeHtml(product)}</div>
                        <input type="hidden" name="products[${index}][id]" value="${product.id}">
                    </td>
                    <td class="pos-price text-end">৳ ${money(product.price)}</td>
                    <td>
                        <div class="pos-qty">
                            <button type="button" class="pos-qty-btn qty-minus" aria-label="Decrease quantity">−</button>
                            <input type="number" name="products[${index}][qty]" class="form-control qty" value="1" min="1" aria-label="Quantity">
                            <button type="button" class="pos-qty-btn qty-plus" aria-label="Increase quantity">+</button>
                        </div>
                    </td>
                    <td>
                        <input type="number" name="products[${index}][discount]" class="form-control dis" value="0" min="0" step="0.01" aria-label="Discount">
                    </td>
                    <td class="pos-subtotal text-end">৳ <span class="subtotal">${money(product.price)}</span></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove" aria-label="Remove item">
                            <i class="ri-delete-bin-6-line"></i>
                        </button>
                    </td>
                `;

                rowsBody.appendChild(row);
                updateEmptyState();
                calculateRow(row);

                // Focus the quantity field for fast entry.
                const qtyInput = row.querySelector('.qty');
                qtyInput.focus();
                qtyInput.select();
            }

            function updateEmptyState() {
                const count = rowsBody.querySelectorAll('.item-row').length;
                const hasRows = count > 0;

                tableWrap.hidden = !hasRows;
                emptyState.hidden = hasRows;

                if (itemCount) itemCount.innerText = count;

                const summaryCount = document.getElementById('summaryItemCount');
                if (summaryCount) summaryCount.innerText = count;
            }

            /* ---------- Calculations (unchanged rules) ---------- */
            function calculateRow(row) {
                const price = parseFloat(row.dataset.price) || 0;
                const qty = parseInt(row.querySelector('.qty').value, 10) || 0;
                const discount = parseFloat(row.querySelector('.dis').value) || 0;
                const total = Math.max(0, (price * qty) - discount);

                row.querySelector('.subtotal').innerText = money(total);
                calculateTotal();
                return total;
            }

            function isAllFreeDelivery() {
                const rows = Array.from(rowsBody.querySelectorAll('.item-row'));
                if (rows.length === 0) return false;
                return rows.every((row) => row.dataset.freeDelivery === '1');
            }

            function calculateTotal() {
                let subtotal = 0;
                let discount = 0;

                rowsBody.querySelectorAll('.item-row').forEach((row) => {
                    subtotal += parseFloat(row.querySelector('.subtotal').innerText) || 0;
                    discount += parseFloat(row.querySelector('.dis').value) || 0;
                });

                const freeDelivery = isAllFreeDelivery();
                const selectedShipping = parseFloat(document.getElementById('shipping_method').value) || 0;
                const shipping = freeDelivery ? 0 : selectedShipping;
                const grandTotal = subtotal + shipping;

                document.getElementById('SubtotalText').innerText = money(subtotal);
                document.getElementById('sumSubTotalAmount').value = money(subtotal);

                document.getElementById('DiscountText').innerText = money(discount);
                document.getElementById('DiscountAmount').value = money(discount);

                document.getElementById('ShippingText').innerText = money(shipping);
                document.getElementById('ShippingAmount').value = money(shipping);

                document.getElementById('GrandTotalText').innerText = money(grandTotal);
                document.getElementById('GrandTotal').value = money(grandTotal);

                const hint = document.getElementById('freeDeliveryHint');
                if (hint) hint.classList.toggle('d-none', !freeDelivery);
            }

            /* ---------- Product search (shared component) ---------- */
            autocomplete = ProductAutocomplete.create({
                input: searchInput,
                results: resultsBox,
                items: PRODUCTS,
                onSelect: function(product) {
                    addProduct(product);
                    autocomplete.setValue('');
                    autocomplete.close();
                }
            });

            /* ---------- Table events (event delegation) ---------- */
            rowsBody.addEventListener('click', function(e) {
                const minus = e.target.closest('.qty-minus');
                const plus = e.target.closest('.qty-plus');
                const remove = e.target.closest('.btn-remove');

                if (minus || plus) {
                    const row = e.target.closest('tr');
                    const input = row.querySelector('.qty');
                    let qty = parseInt(input.value, 10) || 1;
                    qty = minus ? Math.max(1, qty - 1) : qty + 1;
                    input.value = qty;
                    calculateRow(row);
                    return;
                }

                if (remove) {
                    remove.closest('tr').remove();
                    updateEmptyState();
                    calculateTotal();
                }
            });

            rowsBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty')) {
                    const row = e.target.closest('tr');
                    let qty = parseInt(e.target.value, 10);
                    if (isNaN(qty) || qty < 1) {
                        e.target.value = 1;
                    }
                    calculateRow(row);
                } else if (e.target.classList.contains('dis')) {
                    const row = e.target.closest('tr');
                    let value = parseFloat(e.target.value);
                    if (isNaN(value) || value < 0) {
                        e.target.value = 0;
                    }
                    calculateRow(row);
                }
            });

            $('#shipping_method').on('change', function() {
                calculateTotal();
            });

            // Pressing Enter in a quantity field jumps back to product search.
            rowsBody.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.classList.contains('qty')) {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                }
            });

            // Quick-focus the product search with "/" (when not already typing).
            document.addEventListener('keydown', function(e) {
                const tag = (e.target.tagName || '').toLowerCase();
                const isTyping = tag === 'input' || tag === 'textarea' || tag === 'select' ||
                    e.target.isContentEditable;

                if (e.key === '/' && !isTyping) {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                }
            });

            /* ---------- Submit (existing AJAX contract) ---------- */
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
                                var input = $('#' + key);
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').html(value);
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        console.log("Something went wrong");
                    }
                });
            });

            /* ---------- Init ---------- */
            updateEmptyState();
            calculateTotal();

            if (window.initSelect2) {
                window.initSelect2('.js-select2');
            }
        })();
    </script>
@endsection
