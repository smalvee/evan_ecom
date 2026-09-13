@php
    use App\Support\CourierPricing;
    use App\Support\CourierStatus;

    $courierMode = $courierSetting->mode ?: 'test';
    $isLive = $courierMode === 'live';
    $providerLabel = config('courier.providers.' . $courierSetting->provider . '.label', ucfirst($courierSetting->provider));
    $hasShipment = (bool) $courierShipment;
    $isActive = $hasShipment && $courierShipment->isActive();
    $status = $courierShipment->status ?? null;
    $canCancel = $isActive && !$courierShipment->isDelivered() && !$courierShipment->isCancelled();
@endphp

<div class="a-card mb-3" id="courierCard"
    data-create-url="{{ route('admin.orders.courier.create', $order->id) }}"
    data-status-url="{{ route('admin.orders.courier.status', $order->id) }}"
    data-cancel-url="{{ route('admin.orders.courier.cancel', $order->id) }}"
    data-simulate-url="{{ route('admin.orders.courier.simulate', $order->id) }}"
    data-mode="{{ $courierMode }}">
    <div class="a-card-head">
        <h5>Courier</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="a-badge {{ $isLive ? 'a-badge-danger' : 'a-badge-warning' }}"><span
                    class="dot"></span>{{ $isLive ? 'LIVE MODE' : 'TEST MODE' }}</span>
            @if ($status)
                <span class="a-badge {{ CourierStatus::badgeClass($status) }}"><span
                        class="dot"></span>{{ CourierStatus::label($status) }}</span>
            @endif
        </div>
    </div>

    <div class="a-card-body">
        <div id="courierAlert" class="mb-3" hidden></div>

        @if (!$hasShipment)
            <div class="courier-empty d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div class="courier-meta">
                    <div class="courier-meta-row"><span>Provider</span><strong>{{ $providerLabel }}</strong></div>
                    <div class="courier-meta-row"><span>Status</span><strong>Not Submitted</strong></div>
                    <div class="courier-meta-row"><span>COD</span><strong>৳
                            {{ number_format(CourierPricing::codAmount($order), 2) }}</strong></div>
                </div>
                <button type="button" class="btn btn-theme" id="courierSendBtn">
                    <i class="ri-send-plane-line"></i> Send to Courier
                </button>
            </div>
        @else
            <div class="courier-grid">
                <div class="courier-meta-row"><span>Provider</span><strong>{{ $providerLabel }}</strong></div>
                <div class="courier-meta-row"><span>Environment</span>
                    <strong>{{ $courierShipment->isTest() ? 'Test' : 'Live' }}</strong>
                </div>
                <div class="courier-meta-row"><span>Status</span>
                    <strong>{{ CourierStatus::label($status) }}</strong>
                </div>
                <div class="courier-meta-row"><span>Consignment ID</span>
                    <strong>{{ $courierShipment->consignment_id ?: '—' }}</strong>
                </div>
                <div class="courier-meta-row"><span>Invoice</span>
                    <strong>{{ $courierShipment->invoice ?: '—' }}</strong>
                </div>
                <div class="courier-meta-row"><span>Tracking Code</span>
                    <strong>{{ $courierShipment->tracking_code ?: '—' }}</strong>
                </div>
                <div class="courier-meta-row"><span>COD Amount</span>
                    <strong>৳ {{ number_format((float) $courierShipment->cod_amount, 2) }}</strong>
                </div>
                <div class="courier-meta-row"><span>Created</span>
                    <strong>{{ optional($courierShipment->created_at)->format('d M Y, h:i A') }}</strong>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="courierRefreshBtn">
                    <i class="ri-refresh-line"></i> Refresh Status
                </button>
                @if ($canCancel)
                    <button type="button" class="btn btn-outline-danger btn-sm" id="courierCancelBtn">
                        <i class="ri-close-circle-line"></i> Cancel Shipment
                    </button>
                @endif
            </div>

            @if (!$isLive && $isActive)
                <div class="courier-sim mt-3">
                    <div class="courier-sim-title"><i class="ri-flask-line"></i> Test tools — simulate courier status</div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success courier-sim-btn"
                            data-status="delivered">Simulate Delivered</button>
                        <button type="button" class="btn btn-sm btn-outline-warning courier-sim-btn"
                            data-status="hold">Simulate Hold</button>
                        <button type="button" class="btn btn-sm btn-outline-danger courier-sim-btn"
                            data-status="cancelled">Simulate Cancelled</button>
                    </div>
                </div>
            @endif

            @if ($courierShipment->statusHistories->isNotEmpty())
                <div class="mt-3">
                    <div class="text-muted small mb-2 fw-semibold">Status History</div>
                    <ul class="courier-history list-unstyled mb-0">
                        @foreach ($courierShipment->statusHistories as $history)
                            <li class="courier-history-item">
                                <span
                                    class="a-badge {{ CourierStatus::badgeClass($history->status) }}"><span
                                        class="dot"></span>{{ CourierStatus::label($history->status) }}</span>
                                <span class="courier-history-msg">{{ $history->message ?: '—' }}</span>
                                <span
                                    class="courier-history-time">{{ optional($history->created_at)->format('d M Y, h:i A') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif
    </div>
</div>
