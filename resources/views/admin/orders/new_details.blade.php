@extends('admin.layouts.new_app')

@section('content')
    <style>
        /* ---------- Order summary bar ---------- */
        .order-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            align-items: center;
        }

        .order-summary-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--a-muted);
            font-weight: 600;
        }

        .order-summary-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--a-text);
            line-height: 1.3;
        }

        .order-summary-sub {
            font-size: 12px;
            color: var(--a-muted);
        }

        .order-summary-total {
            font-size: 22px;
            color: var(--a-primary);
        }

        .order-summary-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        @media (max-width: 767.98px) {
            .order-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .order-summary-badges {
                justify-content: flex-start;
            }
        }

        /* ---------- Status timeline ---------- */
        .order-timeline {
            display: flex;
            align-items: flex-start;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .order-timeline li {
            position: relative;
            flex: 1;
            text-align: center;
            font-size: 12px;
            color: var(--a-secondary);
        }

        .order-timeline li::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--a-border);
            z-index: 0;
        }

        .order-timeline li:first-child::before {
            left: 50%;
        }

        .order-timeline li:last-child::before {
            right: 50%;
        }

        .order-timeline .tl-dot {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid var(--a-border);
            color: var(--a-secondary);
            margin-bottom: 6px;
        }

        .order-timeline li.is-done .tl-dot {
            border-color: var(--a-primary);
            color: var(--a-primary);
        }

        .order-timeline li.is-done::before {
            background: var(--a-primary);
        }

        .order-timeline li.is-active .tl-dot {
            background: var(--a-primary);
            border-color: var(--a-primary);
            color: #fff;
        }

        .order-timeline li.is-active {
            color: var(--a-primary);
            font-weight: 700;
        }

        .tl-label {
            display: block;
        }

        .order-cancelled-note {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: var(--a-radius-sm);
            background: #fef2f2;
            color: #b91c1c;
            font-size: 13px;
        }

        /* ---------- Order items ---------- */
        .order-items-table th,
        .order-items-table td {
            vertical-align: middle;
        }

        .order-items-table .oi-name {
            font-weight: 600;
            color: var(--a-text);
        }

        .order-items-table .oi-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-top: 2px;
            font-size: 12px;
            color: var(--a-muted);
        }

        .order-items-table .qty-input,
        .order-items-table .discount-input {
            max-width: 110px;
        }

        .add-product-panel {
            padding: 16px;
            margin-bottom: 16px;
            border: 1px dashed var(--a-border);
            border-radius: var(--a-radius-sm);
            background: #fafbfc;
        }

        /* ---------- Financial summary ---------- */
        .fin-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            color: var(--a-text);
        }

        .fin-row + .fin-row {
            border-top: 1px dashed var(--a-border);
        }

        .fin-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            padding-top: 12px;
            border-top: 2px solid var(--a-border);
            font-size: 18px;
            font-weight: 700;
            color: var(--a-text);
        }

        .fin-total span:last-child {
            color: var(--a-primary);
        }

        /* ---------- Notes ---------- */
        .customer-note {
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 14px;
            color: var(--a-text);
        }

        /* ---------- Sticky save bar ---------- */
        .order-savebar {
            position: sticky;
            bottom: 0;
            z-index: 1020;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-top: 16px;
            padding: 12px 16px;
            background: #fff;
            border: 1px solid var(--a-border);
            border-radius: var(--a-radius);
            box-shadow: 0 -4px 16px rgba(15, 23, 42, .06);
        }

        .order-savebar-state {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--a-muted);
        }

        .savebar-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
        }

        .savebar-dot.is-dirty {
            background: var(--a-warning);
        }

        @media (max-width: 575.98px) {
            .order-savebar {
                flex-direction: column;
                align-items: stretch;
            }

            .order-savebar .btn {
                width: 100%;
            }
        }
    </style>

    <div class="container-fluid">
        @include('admin.orders.partials.header')

        @include('admin.orders.partials.summary')

        <form id="orderForm" novalidate>
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    @include('admin.orders.partials.items')
                </div>
            </div>

            <div class="row g-3 mt-0">
                <div class="col-lg-7">
                    @include('admin.orders.partials.customer')
                    @include('admin.orders.partials.notes')
                </div>
                <div class="col-lg-5">
                    @include('admin.orders.partials.status')
                    @include('admin.orders.partials.payment')
                </div>
            </div>

            @include('admin.orders.partials.savebar')
        </form>
    </div>
@endsection

@section('customJs')
    @include('admin.orders.partials.scripts')
@endsection
