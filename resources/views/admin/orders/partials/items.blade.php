<div class="a-card">
    <div class="a-card-head">
        <h5>Order Items
            <span class="a-badge a-badge-neutral"><span class="dot"></span>{{ $orderedItems->count() }}
                products</span>
        </h5>
        <button type="button" class="btn btn-theme btn-sm" id="toggleAddItem" aria-expanded="false"
            aria-controls="addProductPanel">
            <i class="ri-add-line"></i> Add Product
        </button>
    </div>

    <div class="a-card-body pb-0">
        <div id="addProductPanel" class="add-product-panel" hidden>
            <h6 class="a-section-title mb-3">Add Product</h6>
            <div class="row g-2 align-items-end">
                <div class="col-lg-6">
                    <label class="form-label" for="new_variant">Product / Variant</label>
                    <select id="new_variant" class="form-select">
                        <option value="">Search product or select variant…</option>
                        @foreach ($variants as $v)
                            <option value="{{ $v->id }}" data-price="{{ $v->selling_price }}"
                                data-name="{{ $v->product->name ?? '' }}" data-sku="{{ $v->sku }}">
                                {{ $v->product->name ?? '' }} — {{ $v->sku }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label" for="new_item_qty">Quantity</label>
                    <input type="number" id="new_item_qty" class="form-control" value="1" min="1">
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label" for="new_item_discount">Discount</label>
                    <input type="number" id="new_item_discount" class="form-control" value="0" min="0">
                </div>
                <div class="col-12 col-lg-2">
                    <button type="button" class="btn btn-theme w-100" id="addItemBtn">
                        <i class="ri-add-line"></i> Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table all-package theme-table order-items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-end">Unit Price</th>
                    <th width="110">Qty</th>
                    <th width="130">Discount</th>
                    <th>Delivery</th>
                    <th class="text-end">Total</th>
                    <th width="110" class="text-end">Action</th>
                </tr>
            </thead>
            <tbody id="order-items">
                @forelse ($orderedItems as $item)
                    @php
                        $variant_info = $item->variant;
                        $product_info = $variant_info?->product;
                    @endphp
                    <tr class="item-row existing-row" data-price="{{ $item->price }}">
                        <td>
                            <div class="oi-name">{{ $product_info->name ?? '—' }}</div>
                            <div class="oi-meta">
                                <span>SKU: {{ $item->name }}</span>
                                @if ($item->is_pre_order)
                                    <span class="a-badge a-badge-warning"><span class="dot"></span>PRE ORDER</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end">৳ <span class="price-text">{{ number_format($item->price, 2) }}</span>
                        </td>
                        <td>
                            <input type="number" class="form-control qty-input" name="item_qty[{{ $item->id }}]"
                                value="{{ $item->qty }}" min="1" aria-label="Quantity for {{ $item->name }}">
                        </td>
                        <td>
                            <input type="number" class="form-control discount-input"
                                name="item_discount[{{ $item->id }}]" value="{{ $item->discount ?? 0 }}" min="0"
                                aria-label="Discount for {{ $item->name }}">
                        </td>
                        <td>
                            @if ($item->free_delivery)
                                <span class="a-badge a-badge-success"><span class="dot"></span>FREE</span>
                            @else
                                <span class="a-badge a-badge-secondary"><span class="dot"></span>Standard</span>
                            @endif
                        </td>
                        <td class="text-end">৳ <span
                                class="row-total-text">{{ number_format($item->total, 2) }}</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm btn-cancel-item"
                                data-item-id="{{ $item->id }}">
                                <i class="ri-close-line"></i> Cancel
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="order-empty-row">
                        <td colspan="7">
                            <div class="a-empty">
                                <div class="a-empty-icon"><i class="ri-shopping-bag-3-line"></i></div>
                                <h5>No items in this order</h5>
                                <p>Use “Add Product” to add items to the order.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <input type="hidden" id="shipping" value="{{ $order->shipping }}">
    <input type="hidden" id="coupon" value="{{ $order->discount }}">
</div>
