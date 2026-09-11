@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('orders.index') }}">Orders</a></li>
                    <li class="is-active">{{ $order->order_id }}</li>
                </ul>
                <h4 class="a-page-title">Order: {{ $order->order_id }}</h4>
                <p class="a-page-desc">Edit the order, its items and status, then save with one button.</p>
            </div>
            <div class="a-actions">
                <button type="button" class="btn btn-outline-secondary"
                    onclick="location.href='{{ route('orders.index') }}'">
                    <i class="ri-arrow-left-line"></i> Back
                </button>
                <button type="button" class="btn btn-theme"
                    onclick="location.href='{{ route('front.invoice', $order->order_id) }}'">
                    <i class="ri-file-list-3-line"></i> Invoice
                </button>
            </div>
        </div>

        <form id="orderForm">
            @csrf

            <div class="row g-3">
                <div class="col-xxl-9 col-xl-8 col-lg-7">
                    {{-- Customer Information --}}
                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Customer Information</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label a-required">Full Name</label>
                                        <input type="text" class="form-control" value="{{ $order->name }}"
                                            id="f_name" name="f_name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label a-required">Address</label>
                                        <textarea id="address" name="address" class="form-control" rows="3">{{ $order->address }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" value="{{ $order->phone }}" readonly>
                                    </div>

                                    <input type="hidden" value="{{ $order->user_id }}" name="cus_id" id="cus_id">
                                </div>

                                <div class="col-md-6">
                                    <div class="a-card h-100">
                                        <div class="a-card-head">
                                            <h5>Invoice Details</h5>
                                        </div>
                                        <div class="a-card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-3 d-flex justify-content-between">
                                                    <span class="text-muted">Invoice #</span>
                                                    <strong>{{ $order->order_id }}</strong>
                                                </li>
                                                <li class="mb-3 d-flex justify-content-between">
                                                    <span class="text-muted">Date</span>
                                                    <strong>{{ $order->created_at->format('Y-m-d') }}</strong>
                                                </li>
                                                <li class="mb-3 d-flex justify-content-between">
                                                    <span class="text-muted">Time</span>
                                                    <strong>{{ $order->created_at->format('h:i A') }}</strong>
                                                </li>
                                                <li class="mb-3 d-flex justify-content-between">
                                                    <span class="text-muted">Order ID</span>
                                                    <strong>{{ $order->id }}</strong>
                                                </li>
                                                <li class="mb-3 d-flex justify-content-between">
                                                    <span class="text-muted">Total</span>
                                                    <strong>Tk {{ number_format($order->grand_total, 2) }}</strong>
                                                </li>
                                                <li class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted">Status</span>
                                                    @if ($order->status == 'pending')
                                                        <span class="a-badge a-badge-danger"><span
                                                                class="dot"></span>Pending</span>
                                                    @elseif ($order->status == 'confirm')
                                                        <span class="a-badge a-badge-info"><span
                                                                class="dot"></span>Confirmed</span>
                                                    @elseif ($order->status == 'shipped')
                                                        <span class="a-badge a-badge-success"><span
                                                                class="dot"></span>Delivered</span>
                                                    @elseif ($order->status == 'cancell')
                                                        <span class="a-badge a-badge-secondary"><span
                                                                class="dot"></span>Cancelled</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="a-card">
                        <div class="a-card-head">
                            <h5>Order Items</h5>
                        </div>

                        <div class="a-card-body pb-0">
                            <div class="a-form-section mb-0">
                                <h6 class="a-section-title">Add Item</h6>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Product / Variant</label>
                                        <select id="new_variant" class="form-select">
                                            <option value="">Select a product…</option>
                                            @foreach ($variants as $v)
                                                <option value="{{ $v->id }}"
                                                    data-price="{{ $v->selling_price }}"
                                                    data-name="{{ $v->product->name ?? '' }}"
                                                    data-sku="{{ $v->sku }}">
                                                    {{ $v->sku }} — {{ $v->product->name ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Qty</label>
                                        <input type="number" id="new_item_qty" class="form-control" value="1"
                                            min="1">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Discount</label>
                                        <input type="number" id="new_item_discount" class="form-control" value="0"
                                            min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-primary w-100" id="addItemBtn">
                                            <i class="ri-add-line"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table all-package theme-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th width="100">Qty</th>
                                        <th width="120">Discount</th>
                                        <th>Delivery</th>
                                        <th>Total</th>
                                        <th width="110">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="order-items">
                                    @foreach ($orderedItems as $item)
                                        @php
                                            $variant_info = $item->variant;
                                            $product_info = $variant_info?->product;
                                        @endphp
                                        <tr class="item-row existing-row" data-price="{{ $item->price }}">
                                            <td>{{ $product_info->name ?? '—' }} ({{ $item->name }})</td>

                                            <td>Tk <span class="price-text">{{ number_format($item->price, 2) }}</span>
                                            </td>

                                            <td>
                                                <input type="number" class="form-control qty-input"
                                                    name="item_qty[{{ $item->id }}]" value="{{ $item->qty }}"
                                                    min="1">
                                            </td>

                                            <td>
                                                <input type="number" class="form-control discount-input"
                                                    name="item_discount[{{ $item->id }}]"
                                                    value="{{ $item->discount ?? 0 }}" min="0">
                                            </td>

                                            <td>
                                                @if ($item->free_delivery)
                                                    <span class="a-badge a-badge-success"><span
                                                            class="dot"></span>FREE</span>
                                                @else
                                                    <span class="a-badge a-badge-secondary"><span
                                                            class="dot"></span>Standard</span>
                                                @endif
                                            </td>

                                            <td>Tk <span
                                                    class="row-total-text">{{ number_format($item->total, 2) }}</span>
                                            </td>

                                            <td>
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm btn-cancel-item"
                                                    data-item-id="{{ $item->id }}">
                                                    <i class="ri-close-line"></i> Cancel
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">Subtotal:</th>
                                        <td colspan="2">Tk <span id="subtotal-text">{{ $order->subtotal }}</span></td>
                                    </tr>

                                    <tr>
                                        <th colspan="5" class="text-end">Shipping:</th>
                                        <td colspan="2">Tk <span id="shipping-text">{{ $order->shipping }}</span></td>
                                    </tr>

                                    <tr>
                                        <th colspan="5" class="text-end">Coupon Discount:</th>
                                        <td colspan="2">Tk - <span id="coupon-text">{{ $order->discount }}</span></td>
                                    </tr>

                                    <tr>
                                        <th colspan="5" class="text-end fw-bold">Grand Total:</th>
                                        <td colspan="2"><strong>Tk <span
                                                    id="grand-total-text">{{ $order->grand_total }}</span></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <input type="hidden" id="shipping" value="{{ $order->shipping }}">
                        <input type="hidden" id="coupon" value="{{ $order->discount }}">
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-5">
                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Order Status</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped
                                    </option>
                                    <option value="confirm" {{ $order->status == 'confirm' ? 'selected' : '' }}>
                                        Confirmed</option>
                                    <option value="cancell" {{ $order->status == 'cancell' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                                <p class="text-muted small mb-0 mt-2">
                                    Stock is reduced when set to <strong>Confirmed</strong> (or Shipped) and restored if a
                                    confirmed order is set to <strong>Cancelled</strong>.
                                </p>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Order Note</label>
                                <input type="text" name="admin_note" id="admin_note"
                                    value="{{ $order->admin_note }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="a-card">
                        <div class="a-card-head">
                            <h5>Notes from Customer</h5>
                        </div>
                        <div class="a-card-body">
                            {{ $order->notes }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="a-card mt-3">
                <div class="a-card-body d-flex justify-content-end">
                    <button type="submit" class="btn btn-theme btn-lg" id="updateOrderBtn">
                        <i class="ri-save-3-line"></i> Update Order
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        const orderItems = document.getElementById('order-items');
        const orderForm = document.getElementById('orderForm');

        /* ---------- Totals ---------- */
        function updateTotals() {
            let subtotal = 0;

            orderItems.querySelectorAll('.item-row').forEach(row => {
                const price = parseFloat(row.dataset.price) || 0;
                const qty = parseInt(row.querySelector('.qty-input').value) || 0;
                const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

                let total = (price * qty) - discount;
                if (total < 0) total = 0;

                subtotal += total;
                row.querySelector('.row-total-text').innerText = total.toFixed(2);
            });

            const shipping = parseFloat(document.getElementById('shipping').value) || 0;
            const coupon = parseFloat(document.getElementById('coupon').value) || 0;

            document.getElementById('subtotal-text').innerText = subtotal.toFixed(2);

            let grand = subtotal + shipping - coupon;
            if (grand < 0) grand = 0;
            document.getElementById('grand-total-text').innerText = grand.toFixed(2);
        }

        orderItems.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('discount-input')) {
                updateTotals();
            }
        });

        /* ---------- Add item ---------- */
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const sel = document.getElementById('new_variant');
            const opt = sel.options[sel.selectedIndex];

            if (!sel.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select a product first'
                });
                return;
            }

            const qty = parseInt(document.getElementById('new_item_qty').value) || 1;
            const discount = parseFloat(document.getElementById('new_item_discount').value) || 0;
            const price = parseFloat(opt.dataset.price) || 0;
            const name = opt.dataset.name || '';
            const sku = opt.dataset.sku || '';
            const total = Math.max(0, (price * qty) - discount);

            const tr = document.createElement('tr');
            tr.className = 'item-row new-row';
            tr.dataset.price = price;
            tr.innerHTML = `
                <td>${name} (${sku})<input type="hidden" name="new_variant_id[]" value="${sel.value}"></td>
                <td>Tk <span class="price-text">${price.toFixed(2)}</span></td>
                <td><input type="number" class="form-control qty-input" name="new_qty[]" value="${qty}" min="1"></td>
                <td><input type="number" class="form-control discount-input" name="new_discount[]" value="${discount}" min="0"></td>
                <td><span class="a-badge a-badge-secondary"><span class="dot"></span>—</span></td>
                <td>Tk <span class="row-total-text">${total.toFixed(2)}</span></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm btn-remove-new"><i class="ri-delete-bin-line"></i></button></td>
            `;

            orderItems.appendChild(tr);
            updateTotals();

            sel.value = '';
            document.getElementById('new_item_qty').value = 1;
            document.getElementById('new_item_discount').value = 0;
        });

        /* ---------- Cancel / remove item ---------- */
        orderItems.addEventListener('click', function(e) {
            const cancelBtn = e.target.closest('.btn-cancel-item');
            if (cancelBtn) {
                const row = cancelBtn.closest('tr');
                const itemId = cancelBtn.dataset.itemId;

                Swal.fire({
                    title: 'Cancel this item?',
                    text: 'The item will be removed from the order. If the order is confirmed, its stock is returned. Save with "Update Order" to apply.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Yes, cancel item',
                    cancelButtonText: 'Keep'
                }).then(res => {
                    if (!res.isConfirmed) return;

                    row.remove();

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'cancel_items[]';
                    input.value = itemId;
                    orderForm.appendChild(input);

                    updateTotals();
                });
                return;
            }

            const removeBtn = e.target.closest('.btn-remove-new');
            if (removeBtn) {
                removeBtn.closest('tr').remove();
                updateTotals();
            }
        });

        /* ---------- Save everything ---------- */
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('updateOrderBtn');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Updating…';

            $.ajax({
                url: '{{ route('orders.update_full', $order->id) }}',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.href = "{{ route('orders.details', $order->id) }}";
                        }, 1200);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Unable to update the order.'
                        });
                        btn.disabled = false;
                        btn.innerHTML = original;
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong.'
                    });
                    btn.disabled = false;
                    btn.innerHTML = original;
                }
            });
        });

        updateTotals();
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 1500,
                showConfirmButton: false
            });
        </script>
    @endif
@endsection
