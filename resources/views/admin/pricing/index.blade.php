@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Pricing</li>
                </ul>
                <h4 class="a-page-title">Product Pricing</h4>
                <p class="a-page-desc">Manage MRP and selling price per variant. Purchase cost is read-only.</p>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Variants</h5>
                <form action="{{ route('admin.pricing.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    @if (request('product_id'))
                        <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                    @endif
                    <input type="text" class="form-control" style="width: 240px;" placeholder="Search product or SKU"
                        value="{{ request('keyword') }}" name="keyword">
                    <button type="submit" class="btn btn-theme btn-sm"><i class="ri-search-line"></i> Search</button>
                    @if (request('keyword') || request('product_id'))
                        <a href="{{ route('admin.pricing.index') }}" class="btn btn-outline-secondary btn-sm"><i
                                class="ri-refresh-line"></i> Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Variant</th>
                            <th class="a-table-num">Stock</th>
                            <th class="a-table-num">Purchase Cost</th>
                            <th class="a-table-num">MRP</th>
                            <th class="a-table-num">Selling Price</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($variants as $variant)
                            <tr>
                                <td class="a-cell-main">{{ $variant->product->name ?? '—' }}</td>
                                <td>{{ $variant->sku ?? '—' }}</td>
                                <td>
                                    {{ $variant->variation_sku ?: 'Default' }}
                                    @if ($variant->price_manually_managed)
                                        <span class="a-badge a-badge-info ms-1"><span class="dot"></span>Manual</span>
                                    @endif
                                </td>
                                <td class="a-table-num">{{ (int) $variant->qty }}</td>
                                <td class="a-table-num">৳ {{ number_format((float) $variant->purchase_price, 2) }}</td>
                                <td class="a-table-num">৳ {{ number_format((float) $variant->compare_price, 2) }}</td>
                                <td class="a-table-num a-cell-main">৳
                                    {{ number_format((float) $variant->selling_price, 2) }}</td>
                                <td class="text-end">
                                    <div class="a-actions-cell justify-content-end">
                                        <button type="button" class="a-action-btn edit-price-btn" title="Edit Price"
                                            data-variant="{{ $variant->id }}"
                                            data-product="{{ $variant->product->name ?? '' }}"
                                            data-sku="{{ $variant->sku }}"
                                            data-cost="{{ (float) $variant->purchase_price }}"
                                            data-compare="{{ (float) $variant->compare_price }}"
                                            data-selling="{{ (float) $variant->selling_price }}">
                                            <i class="ri-price-tag-3-line"></i>
                                        </button>
                                        <button type="button" class="a-action-btn history-btn" title="Price History"
                                            data-variant="{{ $variant->id }}" data-sku="{{ $variant->sku }}">
                                            <i class="ri-history-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No variants found</h5>
                                        <p>No product variants match your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($variants->hasPages())
                <div class="a-card-footer">{{ $variants->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Edit Price modal --}}
    <div class="modal fade" id="editPriceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editPriceForm">
                    @csrf
                    <input type="hidden" id="ep_variant_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Price — <span id="ep_title"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Purchase Cost (read only)</label>
                            <input type="text" id="ep_cost" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label a-required">MRP</label>
                            <input type="number" step="0.01" min="0" name="compare_price" id="ep_compare"
                                class="form-control">
                            <p class="invalid-feedback" id="ep_compare_error"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label a-required">Selling Price</label>
                            <input type="number" step="0.01" min="0" name="selling_price" id="ep_selling"
                                class="form-control">
                            <p class="invalid-feedback" id="ep_selling_error"></p>
                        </div>
                        <div class="mb-0">
                            <label class="form-label a-required">Reason</label>
                            <input type="text" name="reason" id="ep_reason" class="form-control"
                                placeholder="e.g. Supplier price increased">
                            <p class="invalid-feedback" id="ep_reason_error"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-theme" id="ep_submit">Save Price</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Price History modal --}}
    <div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Price History — <span id="history_sku"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="a-table-num">Old MRP</th>
                                    <th class="a-table-num">New MRP</th>
                                    <th class="a-table-num">Old Selling</th>
                                    <th class="a-table-num">New Selling</th>
                                    <th>Reason</th>
                                    <th>Changed By</th>
                                </tr>
                            </thead>
                            <tbody id="historyBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = new bootstrap.Modal(document.getElementById('editPriceModal'));
            const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));

            /* ----- Edit price ----- */
            document.querySelectorAll('.edit-price-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const d = this.dataset;
                    document.getElementById('ep_variant_id').value = d.variant;
                    document.getElementById('ep_title').innerText = (d.product || '') + ' (' + (d.sku || '') +
                        ')';
                    document.getElementById('ep_cost').value = d.cost;
                    document.getElementById('ep_compare').value = d.compare;
                    document.getElementById('ep_selling').value = d.selling;
                    document.getElementById('ep_reason').value = '';
                    clearPriceErrors();
                    editModal.show();
                });
            });

            function clearPriceErrors() {
                ['ep_compare', 'ep_selling', 'ep_reason'].forEach(id => document.getElementById(id).classList
                    .remove('is-invalid'));
                ['ep_compare_error', 'ep_selling_error', 'ep_reason_error'].forEach(id => document.getElementById(id)
                    .innerHTML = '');
            }

            document.getElementById('editPriceForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('ep_submit');
                const original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Saving…';

                const variantId = document.getElementById('ep_variant_id').value;

                $.ajax({
                    url: `{{ route('admin.pricing.update', 'VID') }}`.replace('VID', variantId),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            editModal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: response.changed ? 'Updated!' : 'No change',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            clearPriceErrors();
                            const errors = response.errors || {};
                            const map = {
                                compare_price: ['ep_compare', 'ep_compare_error'],
                                selling_price: ['ep_selling', 'ep_selling_error'],
                                reason: ['ep_reason', 'ep_reason_error']
                            };
                            Object.keys(errors).forEach(function(key) {
                                if (map[key]) {
                                    document.getElementById(map[key][0]).classList.add('is-invalid');
                                    document.getElementById(map[key][1]).innerHTML = Array.isArray(
                                            errors[key]) ? errors[key][0] : errors[key];
                                }
                            });
                            btn.disabled = false;
                            btn.innerHTML = original;
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong'
                        });
                        btn.disabled = false;
                        btn.innerHTML = original;
                    }
                });
            });

            /* ----- History ----- */
            document.querySelectorAll('.history-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const variantId = this.dataset.variant;
                    document.getElementById('history_sku').innerText = this.dataset.sku || '';
                    document.getElementById('historyBody').innerHTML =
                        '<tr><td colspan="7" class="text-center text-muted">Loading…</td></tr>';
                    historyModal.show();

                    $.get(`{{ route('admin.pricing.history', 'VID') }}`.replace('VID', variantId), function(
                        response) {
                        let rows = '';
                        if (response.status && response.history.length) {
                            response.history.forEach(function(h) {
                                rows += `<tr>
                                    <td>${h.date ?? ''}</td>
                                    <td class="a-table-num">৳ ${h.old_compare_price}</td>
                                    <td class="a-table-num">৳ ${h.new_compare_price}</td>
                                    <td class="a-table-num">৳ ${h.old_selling_price}</td>
                                    <td class="a-table-num">৳ ${h.new_selling_price}</td>
                                    <td>${h.reason ?? ''}</td>
                                    <td>${h.changed_by}</td>
                                </tr>`;
                            });
                        } else {
                            rows =
                                '<tr><td colspan="7" class="text-center text-muted">No price history yet.</td></tr>';
                        }
                        document.getElementById('historyBody').innerHTML = rows;
                    });
                });
            });
        });
    </script>
@endsection
