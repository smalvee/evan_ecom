@extends('admin.layouts.new_app')

@section('content')
    @include('admin.partials.product-search')

    @php
        $productOptions = $variants
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'name' => $v->product?->name ?? '',
                    'sku' => $v->sku,
                    // Existing stock field: product_variants.qty (int-cast).
                    'stock' => (int) $v->qty,
                ];
            })
            ->values()
            ->all();
    @endphp

    <style>
        .adj-selected {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid var(--a-border);
            border-radius: var(--a-radius-sm);
            background: #fafbfc;
        }

        .adj-selected-name {
            font-weight: 600;
            color: var(--a-text);
        }

        .adj-selected-sku {
            font-size: 12px;
            color: var(--a-muted);
        }

        .adj-sum-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 0;
            font-size: 14px;
            color: var(--a-muted);
            border-bottom: 1px dashed var(--a-border);
        }

        .adj-sum-row strong {
            font-size: 16px;
            color: var(--a-text);
        }

        .adj-sum-row.is-total {
            border-bottom: 0;
            border-top: 2px solid var(--a-border);
            margin-top: 4px;
            padding-top: 12px;
        }

        .adj-sum-row.is-total strong {
            font-size: 20px;
            color: var(--a-primary);
        }
    </style>

    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.adjustments.index') }}">Stock Adjustments</a></li>
                    <li class="is-active">New</li>
                </ul>
                <h4 class="a-page-title">Product Adjustment</h4>
                <p class="a-page-desc">Increase stock, decrease stock, or correct the system stock to the actual physical stock.</p>
            </div>
        </div>

        <form action="{{ route('admin.adjustments.store') }}" method="POST" id="adjustmentForm">
            @csrf
            <input type="hidden" name="variant_id" id="variant_id" value="{{ old('variant_id') }}">

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Product</h5>
                        </div>
                        <div class="a-card-body">
                            <label class="form-label" for="product_search">Product / SKU</label>
                            <div class="pos-search">
                                <div class="pos-search-field">
                                    <i class="ri-search-line"></i>
                                    <input type="text" id="product_search" class="form-control"
                                        placeholder="Search product or SKU…" autocomplete="off">
                                </div>
                                <div class="pos-search-results" id="product_results" hidden></div>
                            </div>
                            @error('variant_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <div id="selectedProduct" class="adj-selected mt-3" hidden>
                                <div>
                                    <div class="adj-selected-name" data-name>—</div>
                                    <div class="adj-selected-sku">SKU: <span data-sku>—</span></div>
                                </div>
                                <div class="text-end">
                                    <div class="adj-selected-sku">Current Stock</div>
                                    <div class="fw-bold" data-current>—</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="a-card mb-3">
                        <div class="a-card-head">
                            <h5>Adjustment</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="adjustment_type">Adjustment Type</label>
                                    <select name="adjustment_type" id="adjustment_type" class="form-select">
                                        @foreach (\App\Models\ProductAdjustment::types() as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('adjustment_type', \App\Models\ProductAdjustment::TYPE_INCREASE) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6" id="quantityWrap">
                                    <label class="form-label" for="quantity"><span id="quantityLabel">Increase By</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1"
                                        value="{{ old('quantity') }}" placeholder="0">
                                    @error('quantity')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6" id="actualStockWrap" hidden>
                                    <label class="form-label" for="actual_stock">Actual Physical Stock</label>
                                    <input type="number" name="actual_stock" id="actual_stock" class="form-control"
                                        min="0" value="{{ old('actual_stock') }}" placeholder="0">
                                    @error('actual_stock')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="reason">Reason</label>
                                    <select name="reason" id="reason" class="form-select">
                                        @foreach (\App\Models\ProductAdjustment::REASONS as $reason)
                                            <option value="{{ $reason }}"
                                                {{ old('reason') === $reason ? 'selected' : '' }}>{{ $reason }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="note">Note</label>
                                    <textarea name="note" id="note" rows="3" class="form-control" placeholder="Optional">{{ old('note') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="a-card adj-summary mb-3">
                        <div class="a-card-head">
                            <h5>Preview</h5>
                        </div>
                        <div class="a-card-body">
                            <div class="adj-sum-row"><span id="labelBefore">Current Stock</span><strong
                                    id="valBefore">—</strong></div>
                            <div class="adj-sum-row"><span>Adjustment</span><strong id="valAdjustment">—</strong>
                            </div>
                            <div class="adj-sum-row is-total"><span id="labelAfter">New Stock</span><strong
                                    id="valAfter">—</strong></div>
                            <p class="text-muted small mb-0 mt-3">
                                <i class="ri-information-line"></i> Preview only. The server recalculates from the live
                                database stock when you save.
                            </p>
                        </div>
                    </div>

                    <div class="a-card">
                        <div class="a-card-body d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.adjustments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-theme" id="saveBtn"><i class="ri-save-3-line"></i> Save
                                Adjustment</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        const ADJ_PRODUCTS = @json($productOptions);
    </script>
    <script>
        (function() {
            'use strict';

            const products = ADJ_PRODUCTS;
            let currentStock = null;

            const input = document.getElementById('product_search');
            const results = document.getElementById('product_results');
            const hidden = document.getElementById('variant_id');
            const typeEl = document.getElementById('adjustment_type');
            const qtyEl = document.getElementById('quantity');
            const actualEl = document.getElementById('actual_stock');
            const qtyWrap = document.getElementById('quantityWrap');
            const actualWrap = document.getElementById('actualStockWrap');
            const reasonEl = document.getElementById('reason');
            const selectedBox = document.getElementById('selectedProduct');

            const autocomplete = ProductAutocomplete.create({
                input: input,
                results: results,
                items: products,
                onSelect: function(product) {
                    selectProduct(product);
                    autocomplete.close();
                }
            });

            function setText(id, value) {
                document.getElementById(id).textContent = value;
            }

            function signed(value) {
                return (value >= 0 ? '+' : '') + value;
            }

            function selectProduct(product) {
                currentStock = Number(product.stock) || 0;
                hidden.value = product.id;
                autocomplete.setValue(product.name + ' — ' + product.sku);

                selectedBox.hidden = false;
                selectedBox.querySelector('[data-name]').textContent = product.name;
                selectedBox.querySelector('[data-sku]').textContent = product.sku;
                selectedBox.querySelector('[data-current]').textContent = currentStock;

                recalc();
            }

            function recalc() {
                const type = typeEl.value;

                setText('valBefore', currentStock === null ? '—' : currentStock);

                if (type === 'correction') {
                    setText('labelBefore', 'System Stock');
                    setText('labelAfter', 'Final Stock');

                    const actual = actualEl.value === '' ? null : parseInt(actualEl.value, 10);
                    const adjustment = (actual === null || currentStock === null) ? null : actual - currentStock;

                    setText('valAdjustment', adjustment === null ? '—' : signed(adjustment));
                    setText('valAfter', actual === null ? '—' : actual);
                    return;
                }

                setText('labelBefore', 'Current Stock');
                setText('labelAfter', 'New Stock');
                setText('quantityLabel', type === 'increase' ? 'Increase By' : 'Decrease By');

                const qty = parseInt(qtyEl.value, 10);
                const valid = !isNaN(qty) && qty > 0 && currentStock !== null;
                const adjustment = valid ? (type === 'increase' ? qty : -qty) : null;
                const after = adjustment === null ? null : currentStock + adjustment;

                setText('valAdjustment', adjustment === null ? '—' : signed(adjustment));
                setText('valAfter', after === null ? '—' : after);
            }

            function toggleFields() {
                const isCorrection = typeEl.value === 'correction';

                qtyWrap.hidden = isCorrection;
                actualWrap.hidden = !isCorrection;

                if (isCorrection && !reasonEl.dataset.touched) {
                    reasonEl.value = 'Stock Correction';
                }

                recalc();
            }

            typeEl.addEventListener('change', toggleFields);
            qtyEl.addEventListener('input', recalc);
            actualEl.addEventListener('input', recalc);
            reasonEl.addEventListener('change', function() {
                reasonEl.dataset.touched = '1';
            });

            document.getElementById('adjustmentForm').addEventListener('submit', function(e) {
                if (!hidden.value) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Select a product first'
                    });
                    input.focus();
                }
            });

            // Restore selection after a validation redirect.
            if (hidden.value) {
                const found = products.filter(function(p) {
                    return String(p.id) === String(hidden.value);
                })[0];
                if (found) selectProduct(found);
            }

            toggleFields();
        })();
    </script>
@endsection
