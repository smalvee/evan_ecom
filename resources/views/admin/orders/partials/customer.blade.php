<div class="a-card mb-3">
    <div class="a-card-head">
        <h5>Customer Information</h5>
    </div>
    <div class="a-card-body">
        <div class="mb-3">
            <label class="form-label a-required" for="f_name">Full Name</label>
            <input type="text" class="form-control" id="f_name" name="f_name" value="{{ $order->name }}" required>
        </div>

        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label" for="customer_phone">Phone</label>
                <input type="text" class="form-control" id="customer_phone" value="{{ $order->phone }}" readonly>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="customer_id">Customer ID</label>
                <input type="text" class="form-control" id="customer_id" value="#{{ $order->user_id }}" readonly>
            </div>
        </div>

        <input type="hidden" name="cus_id" id="cus_id" value="{{ $order->user_id }}">
    </div>
</div>

<div class="a-card mb-3">
    <div class="a-card-head">
        <h5>Delivery Information</h5>
    </div>
    <div class="a-card-body">
        <div class="mb-3">
            <label class="form-label a-required" for="address">Delivery Address</label>
            <textarea class="form-control" id="address" name="address" rows="3" required>{{ $order->address }}</textarea>
        </div>

        <div>
            <label class="form-label" for="shipping_display">Shipping Charge</label>
            <input type="text" class="form-control" id="shipping_display"
                value="৳ {{ number_format($order->shipping, 2) }}" readonly>
        </div>
    </div>
</div>
