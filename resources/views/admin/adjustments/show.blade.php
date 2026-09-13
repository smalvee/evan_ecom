@extends('admin.layouts.new_app')

@php
    $badge = $adjustment->isIncrease()
        ? 'a-badge-success'
        : ($adjustment->isDecrease()
            ? 'a-badge-danger'
            : 'a-badge-info');
@endphp

@section('content')
    <style>
        .adj-info-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px dashed var(--a-border);
            font-size: 14px;
        }

        .adj-info-row:last-child {
            border-bottom: 0;
        }

        .adj-info-row > span {
            color: var(--a-muted);
        }

        .adj-info-row > strong {
            color: var(--a-text);
            text-align: right;
            word-break: break-word;
        }

        .adj-move-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed var(--a-border);
        }

        .adj-move-row:last-child {
            border-bottom: 0;
        }

        .adj-move-row > span {
            color: var(--a-muted);
            font-size: 14px;
        }

        .adj-move-row > strong {
            font-size: 20px;
            color: var(--a-text);
        }

        .adj-move-row.is-final {
            border-top: 2px solid var(--a-border);
            margin-top: 4px;
            padding-top: 14px;
        }

        .adj-move-row.is-final > strong {
            color: var(--a-primary);
            font-size: 24px;
        }

        .adj-delta-pos {
            color: #0f7b43 !important;
        }

        .adj-delta-neg {
            color: #b42318 !important;
        }
    </style>

    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.adjustments.index') }}">Stock Adjustments</a></li>
                    <li class="is-active">{{ $adjustment->adjustment_no }}</li>
                </ul>
                <h4 class="a-page-title">Adjustment #{{ $adjustment->adjustment_no }}</h4>
                <p class="a-page-desc">{{ optional($adjustment->created_at)->format('d M Y, h:i A') }}</p>
            </div>
            <div class="a-actions">
                <span class="a-badge {{ $badge }}"><span
                        class="dot"></span>{{ $adjustment->typeLabel() }}</span>
                <a href="{{ route('admin.adjustments.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line"></i> Back
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="a-card">
                    <div class="a-card-head">
                        <h5>Details</h5>
                    </div>
                    <div class="a-card-body">
                        <div class="adj-info-row">
                            <span>Product</span>
                            <strong>{{ $adjustment->product?->name ?? '—' }}</strong>
                        </div>
                        <div class="adj-info-row">
                            <span>SKU</span>
                            <strong>{{ $adjustment->variant?->sku ?? '—' }}</strong>
                        </div>
                        <div class="adj-info-row">
                            <span>Adjustment Type</span>
                            <strong>{{ $adjustment->typeLabel() }}</strong>
                        </div>
                        <div class="adj-info-row">
                            <span>Reason</span>
                            <strong>{{ $adjustment->reason ?: '—' }}</strong>
                        </div>
                        <div class="adj-info-row">
                            <span>Note</span>
                            <strong>{{ $adjustment->note ?: '—' }}</strong>
                        </div>
                        <div class="adj-info-row">
                            <span>Created By</span>
                            <strong>{{ $adjustment->creator?->name ?? 'System' }}</strong>
                        </div>
                        <div class="adj-info-row">
                            <span>Date</span>
                            <strong>{{ optional($adjustment->created_at)->format('d M Y, h:i A') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="a-card">
                    <div class="a-card-head">
                        <h5>Stock Movement</h5>
                    </div>
                    <div class="a-card-body">
                        @if ($adjustment->isCorrection())
                            <div class="adj-move-row">
                                <span>System Stock</span>
                                <strong>{{ $adjustment->stock_before }}</strong>
                            </div>
                            <div class="adj-move-row">
                                <span>Actual Stock</span>
                                <strong>{{ $adjustment->actual_stock }}</strong>
                            </div>
                            <div class="adj-move-row">
                                <span>Adjustment</span>
                                <strong
                                    class="{{ $adjustment->quantity >= 0 ? 'adj-delta-pos' : 'adj-delta-neg' }}">{{ $adjustment->signedQuantity() }}</strong>
                            </div>
                            <div class="adj-move-row is-final">
                                <span>Final Stock</span>
                                <strong>{{ $adjustment->stock_after }}</strong>
                            </div>
                        @else
                            <div class="adj-move-row">
                                <span>Stock Before</span>
                                <strong>{{ $adjustment->stock_before }}</strong>
                            </div>
                            <div class="adj-move-row">
                                <span>Adjustment</span>
                                <strong
                                    class="{{ $adjustment->quantity >= 0 ? 'adj-delta-pos' : 'adj-delta-neg' }}">{{ $adjustment->signedQuantity() }}</strong>
                            </div>
                            <div class="adj-move-row is-final">
                                <span>Stock After</span>
                                <strong>{{ $adjustment->stock_after }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
