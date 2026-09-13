<div class="a-card mb-3">
    <div class="a-card-head">
        <h5>Customer Note</h5>
    </div>
    <div class="a-card-body">
        @if (filled($order->notes))
            <div class="customer-note">{{ $order->notes }}</div>
        @else
            <p class="text-muted mb-0">No note from the customer.</p>
        @endif
    </div>
</div>

<div class="a-card mb-3">
    <div class="a-card-head">
        <h5>Internal Order Note</h5>
    </div>
    <div class="a-card-body">
        <label class="form-label" for="admin_note">Admin / internal note (not shown to the customer)</label>
        <textarea name="admin_note" id="admin_note" rows="3" class="form-control">{{ $order->admin_note }}</textarea>
    </div>
</div>
