<script>
    (function() {
        'use strict';

        const orderForm = document.getElementById('orderForm');
        const orderItems = document.getElementById('order-items');
        const updateBtn = document.getElementById('updateOrderBtn');
        const dirtyText = document.getElementById('dirtyText');
        const dirtyDot = document.getElementById('dirtyDot');

        let isDirty = false;
        let isSubmitting = false;
        let initialStatus = null;

        /* ---------- Helpers ---------- */
        function money(value) {
            return (Math.round((parseFloat(value) || 0) * 100) / 100).toFixed(2);
        }

        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) el.innerText = value;
        }

        function escapeHtml(str) {
            return String(str == null ? '' : str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        /* ---------- Calculations ---------- */
        function calculateRowTotal(row) {
            const price = parseFloat(row.dataset.price) || 0;
            const qty = parseInt(row.querySelector('.qty-input')?.value, 10) || 0;
            const discount = parseFloat(row.querySelector('.discount-input')?.value) || 0;
            const total = Math.max(0, (price * qty) - discount);
            const el = row.querySelector('.row-total-text');
            if (el) el.innerText = money(total);
            return total;
        }

        function calculateSubtotal() {
            let subtotal = 0;
            orderItems.querySelectorAll('.item-row').forEach(function(row) {
                subtotal += calculateRowTotal(row);
            });
            return subtotal;
        }

        function calculateGrandTotal(subtotal) {
            const shipping = parseFloat(document.getElementById('shipping')?.value) || 0;
            const coupon = parseFloat(document.getElementById('coupon')?.value) || 0;
            return Math.max(0, subtotal + shipping - coupon);
        }

        function refreshOrderSummary() {
            const subtotal = calculateSubtotal();
            const grand = calculateGrandTotal(subtotal);

            setText('subtotal-text', money(subtotal));
            setText('shipping-text', money(document.getElementById('shipping')?.value || 0));
            setText('coupon-text', money(document.getElementById('coupon')?.value || 0));
            setText('grand-total-text', money(grand));
            setText('summary-grand-total', money(grand));
            setText('summary-item-count', orderItems.querySelectorAll('.item-row').length);
        }

        /* ---------- Item calculator (event delegation) ---------- */
        function initItemCalculator() {
            orderItems.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('discount-input')) {
                    refreshOrderSummary();
                    markDirty();
                }
            });
        }

        /* ---------- Add product ---------- */
        function initAddItem() {
            const toggle = document.getElementById('toggleAddItem');
            const panel = document.getElementById('addProductPanel');
            const addBtn = document.getElementById('addItemBtn');
            const sel = document.getElementById('new_variant');

            if (toggle && panel) {
                toggle.addEventListener('click', function() {
                    const isHidden = panel.hasAttribute('hidden');

                    if (isHidden) {
                        panel.removeAttribute('hidden');
                        toggle.setAttribute('aria-expanded', 'true');

                        if (window.jQuery && jQuery.fn.select2 && !jQuery(sel).data('select2')) {
                            jQuery(sel).select2({
                                width: '100%',
                                placeholder: 'Search product or select variant…'
                            });
                        }

                        sel.focus();
                    } else {
                        panel.setAttribute('hidden', '');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            if (!addBtn) return;

            addBtn.addEventListener('click', function() {
                const opt = sel.options[sel.selectedIndex];

                if (!sel.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Select a product first'
                    });
                    return;
                }

                const qty = Math.max(1, parseInt(document.getElementById('new_item_qty').value, 10) || 1);
                const discount = Math.max(0, parseFloat(document.getElementById('new_item_discount').value) || 0);
                const price = parseFloat(opt.dataset.price) || 0;
                const name = opt.dataset.name || '';
                const sku = opt.dataset.sku || '';
                const total = Math.max(0, (price * qty) - discount);

                const emptyRow = orderItems.querySelector('.order-empty-row');
                if (emptyRow) emptyRow.remove();

                const tr = document.createElement('tr');
                tr.className = 'item-row new-row';
                tr.dataset.price = price;
                tr.innerHTML = `
                    <td>
                        <div class="oi-name">${escapeHtml(name)}</div>
                        <div class="oi-meta"><span>SKU: ${escapeHtml(sku)}</span></div>
                        <input type="hidden" name="new_variant_id[]" value="${sel.value}">
                    </td>
                    <td class="text-end">৳ <span class="price-text">${money(price)}</span></td>
                    <td><input type="number" class="form-control qty-input" name="new_qty[]" value="${qty}" min="1" aria-label="Quantity"></td>
                    <td><input type="number" class="form-control discount-input" name="new_discount[]" value="${discount}" min="0" aria-label="Discount"></td>
                    <td><span class="a-badge a-badge-secondary"><span class="dot"></span>Standard</span></td>
                    <td class="text-end">৳ <span class="row-total-text">${money(total)}</span></td>
                    <td class="text-end"><button type="button" class="btn btn-outline-danger btn-sm btn-remove-new" aria-label="Remove item"><i class="ri-delete-bin-line"></i></button></td>
                `;
                orderItems.appendChild(tr);

                sel.value = '';
                if (window.jQuery && jQuery.fn.select2) {
                    jQuery(sel).val('').trigger('change');
                }
                document.getElementById('new_item_qty').value = 1;
                document.getElementById('new_item_discount').value = 0;

                refreshOrderSummary();
                markDirty();
            });
        }

        /* ---------- Cancel / remove item ---------- */
        function initCancelItem() {
            orderItems.addEventListener('click', function(e) {
                const cancelBtn = e.target.closest('.btn-cancel-item');
                if (cancelBtn) {
                    const row = cancelBtn.closest('tr');
                    const itemId = cancelBtn.dataset.itemId;

                    Swal.fire({
                        title: 'Cancel this item?',
                        text: 'This item will be removed from the active order. Stock handling follows the existing cancellation rules.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Cancel Item',
                        cancelButtonText: 'Keep Item'
                    }).then(function(res) {
                        if (!res.isConfirmed) return;

                        row.remove();

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'cancel_items[]';
                        input.value = itemId;
                        orderForm.appendChild(input);

                        refreshOrderSummary();
                        markDirty();
                    });
                    return;
                }

                const removeBtn = e.target.closest('.btn-remove-new');
                if (removeBtn) {
                    removeBtn.closest('tr').remove();
                    refreshOrderSummary();
                    markDirty();
                }
            });
        }

        /* ---------- Payment toggle ---------- */
        function initPaymentToggle() {
            const btn = document.getElementById('paymentToggleBtn');
            if (!btn) return;

            btn.addEventListener('click', function() {
                const badge = document.getElementById('payment-badge');
                const isPaid = badge && badge.classList.contains('a-badge-success');
                const action = isPaid ? 'mark this order as UNPAID' : 'mark this order as PAID';

                Swal.fire({
                    title: 'Change payment status?',
                    text: 'You are about to ' + action + '.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0da487',
                    confirmButtonText: 'Yes, update',
                    cancelButtonText: 'Cancel'
                }).then(function(res) {
                    if (!res.isConfirmed) return;

                    jQuery.ajax({
                        url: '{{ route('orders.paymentStatus', $order->id) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (!response.status) return;

                            const paid = !!response.payment_status;
                            badge.className = 'a-badge ' + (paid ? 'a-badge-success' :
                                'a-badge-warning');
                            badge.innerHTML = '<span class="dot"></span>' + (paid ? 'Paid' :
                                'Unpaid');

                            const label = document.getElementById('paymentToggleLabel');
                            if (label) label.textContent = paid ? 'Mark as Unpaid' : 'Mark as Paid';

                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                timer: 1200,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Something went wrong'
                            });
                        }
                    });
                });
            });
        }

        /* ---------- Dirty state ---------- */
        function markDirty() {
            if (isDirty) return;
            isDirty = true;
            updateDirtyUi();
        }

        function updateDirtyUi() {
            if (!dirtyText) return;

            dirtyText.textContent = isDirty ? 'Unsaved changes' : 'No unsaved changes';
            if (dirtyDot) dirtyDot.classList.toggle('is-dirty', isDirty);
        }

        function initDirtyState() {
            orderForm.addEventListener('input', function(e) {
                if (e.target.matches('input, select, textarea')) markDirty();
            });
            orderForm.addEventListener('change', function(e) {
                if (e.target.matches('input, select, textarea')) markDirty();
            });

            window.addEventListener('beforeunload', function(e) {
                if (isDirty && !isSubmitting) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });

            const cancelBtn = document.getElementById('cancelChangesBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    if (!isDirty) {
                        window.location.href = "{{ route('orders.index') }}";
                        return;
                    }

                    Swal.fire({
                        title: 'Discard changes?',
                        text: 'You have unsaved changes. Are you sure you want to leave?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Discard & leave',
                        cancelButtonText: 'Stay'
                    }).then(function(res) {
                        if (res.isConfirmed) {
                            isSubmitting = true;
                            window.location.href = "{{ route('orders.index') }}";
                        }
                    });
                });
            }
        }

        /* ---------- Submit ---------- */
        function submitOrder() {
            if (isSubmitting) return;

            const name = document.getElementById('f_name');
            const address = document.getElementById('address');

            if (name && !name.value.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Full name is required'
                });
                name.focus();
                return;
            }
            if (address && !address.value.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Delivery address is required'
                });
                address.focus();
                return;
            }

            isSubmitting = true;
            const original = updateBtn.innerHTML;
            updateBtn.disabled = true;
            updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating order…';

            jQuery.ajax({
                url: '{{ route('orders.update_full', $order->id) }}',
                type: 'POST',
                data: jQuery(orderForm).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        isDirty = false;
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
                        isSubmitting = false;
                        updateBtn.disabled = false;
                        updateBtn.innerHTML = original;
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot update order',
                            text: response.message || 'Unable to update the order.'
                        });
                    }
                },
                error: function(xhr) {
                    isSubmitting = false;
                    updateBtn.disabled = false;
                    updateBtn.innerHTML = original;
                    Swal.fire({
                        icon: 'error',
                        title: 'Unable to update the order',
                        text: (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Please try again.'
                    });
                }
            });
        }

        function initOrderSubmit() {
            orderForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const statusEl = document.getElementById('status');
                const newStatus = statusEl ? statusEl.value : null;
                const stockAffecting = ['confirm', 'shipped', 'cancell'].indexOf(newStatus) !== -1;

                // Confirm only for stock-affecting status transitions.
                if (newStatus && newStatus !== initialStatus && stockAffecting) {
                    Swal.fire({
                        title: 'Update order status?',
                        text: 'Changing the status to "' + newStatus +
                            '" can affect stock. Continue?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0da487',
                        confirmButtonText: 'Yes, continue',
                        cancelButtonText: 'Cancel'
                    }).then(function(res) {
                        if (res.isConfirmed) submitOrder();
                    });
                    return;
                }

                submitOrder();
            });
        }

        /* ---------- Init ---------- */
        function initOrderPage() {
            if (!orderForm || !orderItems) return;

            initialStatus = document.getElementById('status') ? document.getElementById('status').value : null;

            initItemCalculator();
            initAddItem();
            initCancelItem();
            initPaymentToggle();
            initDirtyState();
            initOrderSubmit();

            refreshOrderSummary();
            updateDirtyUi();
        }

        document.addEventListener('DOMContentLoaded', initOrderPage);
    })();
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
