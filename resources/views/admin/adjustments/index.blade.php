@extends('admin.layouts.new_app')

@section('content')
    <style>
        .adj-qty {
            font-weight: 700;
            white-space: nowrap;
        }

        .adj-qty.is-pos {
            color: #0f7b43;
        }

        .adj-qty.is-neg {
            color: #b42318;
        }
    </style>

    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Stock Adjustments</li>
                </ul>
                <h4 class="a-page-title">Stock Adjustments</h4>
                <p class="a-page-desc">History of manual stock increases, decreases and corrections.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('admin.adjustments.create') }}" class="btn btn-theme">
                    <i class="ri-add-line"></i> New Adjustment
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="a-card">
            <div class="a-card-head">
                <h5>Adjustment History
                    <span class="a-badge a-badge-neutral"><span class="dot"></span>{{ $adjustments->total() }}</span>
                </h5>
            </div>

            @if ($adjustments->isEmpty())
                <div class="a-card-body">
                    <div class="a-empty">
                        <div class="a-empty-icon"><i class="ri-scales-3-line"></i></div>
                        <h5>No adjustments yet</h5>
                        <p>Stock increases, decreases and corrections will appear here.</p>
                        <a href="{{ route('admin.adjustments.create') }}" class="btn btn-theme">New Adjustment</a>
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table theme-table">
                        <thead>
                            <tr>
                                <th>Adjustment No.</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Type</th>
                                <th class="text-end">Adjustment</th>
                                <th class="text-end">Before</th>
                                <th class="text-end">After</th>
                                <th>Reason</th>
                                <th>Created By</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($adjustments as $adjustment)
                                @php
                                    $badge = $adjustment->isIncrease()
                                        ? 'a-badge-success'
                                        : ($adjustment->isDecrease()
                                            ? 'a-badge-danger'
                                            : 'a-badge-info');
                                @endphp
                                <tr>
                                    <td><strong>{{ $adjustment->adjustment_no }}</strong></td>
                                    <td>{{ optional($adjustment->created_at)->format('d M Y, h:i A') }}</td>
                                    <td>{{ $adjustment->product?->name ?? '—' }}</td>
                                    <td>{{ $adjustment->variant?->sku ?? '—' }}</td>
                                    <td>
                                        <span class="a-badge {{ $badge }}"><span
                                                class="dot"></span>{{ $adjustment->typeLabel() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span
                                            class="adj-qty {{ $adjustment->quantity >= 0 ? 'is-pos' : 'is-neg' }}">{{ $adjustment->signedQuantity() }}</span>
                                    </td>
                                    <td class="text-end">{{ $adjustment->stock_before }}</td>
                                    <td class="text-end">{{ $adjustment->stock_after }}</td>
                                    <td>{{ $adjustment->reason ?: '—' }}</td>
                                    <td>{{ $adjustment->creator?->name ?? 'System' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.adjustments.show', $adjustment->id) }}"
                                            class="btn btn-outline-secondary btn-sm">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="a-card-body pt-0">
                    {{ $adjustments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
