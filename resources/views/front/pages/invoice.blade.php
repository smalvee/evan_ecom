<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>

    <!-- Meta Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '2261244381019011');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=2261244381019011&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
</head>

<body style="margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;background-color:#f4f4f4;">
    <?php
    
    use App\Models\Order;
    use App\Models\CustomerAddress;
    use App\Models\OrderItem;
    use App\Models\ProductVariant;
    use App\Models\NewProduct;
    
    ?>

    @php
        $order_info = Order::where('order_id', $id)->first();
        $order_items = OrderItem::where('order_id', $order_info->id)->get();
        $customer_address = CustomerAddress::where('user_id', $order_info->user_id)->first();
    @endphp

    <!-- Print Button -->
    <div class="no-print" style="text-align:center;margin:15px;">
        <button onclick="window.print()"
            style="background:#007bff;color:#fff;border:none;padding:12px 20px;
             font-size:16px;border-radius:4px;cursor:pointer;width:100%;max-width:300px;">
            🖨️ Print Invoice
        </button>
    </div>

    <!-- Invoice Wrapper -->
    <div
        style="
      position:relative;
      max-width:800px;
      margin:20px auto;
      background:#ffffff;
      padding:20px;
      border:1px solid #ddd;
      overflow:hidden;
    ">

        <!-- Watermark -->
        <div
            style="
        position:absolute;
        top:50%;
        left:50%;
        width:400px;
        height:400px;
        transform:translate(-50%, -50%);
        background-image:url('https://evans.com.bd/new-front-assets/images/logo/8.png');
        background-repeat:no-repeat;
        background-position:center;
        background-size:contain;
        opacity:0.08;
        z-index:0;
        pointer-events:none;
      ">
        </div>

        <!-- Invoice Content -->
        <div style="position:relative;z-index:1;">

            <!-- Header -->
            <div style="text-align:center;margin-bottom:20px;">
                <h1 style="margin:0;font-size:28px;color:#333;">INVOICE</h1>
                <p style="margin:5px 0;font-size:14px;color:#666;">Invoice #INV-{{ $id }}</p>
            </div>

            <!-- Company & Invoice Info -->
            <div style="margin-bottom:20px;">
                <p style="margin:0;font-size:14px;"><strong>Evan Store</strong></p>
                <p style="margin:5px 0;font-size:13px;color:#666;">House 35, (Level-03), Road No. 14</p>
                <p style="margin:5px 0;font-size:13px;color:#666;">Sector-13, Uttara Model Town, Dhaka-1230</p>
                <p style="margin:5px 0;font-size:13px;color:#666;">Email: support@evan.com.bd, evansstore.com.bd@gmail.com</p>

                <p style="margin-top:10px;font-size:13px;"><strong>Invoice
                        Date:</strong> {{ \Carbon\Carbon::parse($order_info->created_at)->format('d M Y') }}
                </p>
                <p style="margin-top:10px;font-size:13px;"><strong>Invoice
                        Time:</strong> {{ \Carbon\Carbon::parse($order_info->created_at)->format('h:i A') }}
                </p>
                {{-- <p style="margin:5px 0;font-size:13px;"><strong>Due Date:</strong> 10 Jan 2026</p> --}}
            </div>



            <!-- Bill To -->
            <div style="margin-bottom:20px;">
                <p style="margin:0 0 5px 0;font-size:14px;"><strong>Bill To:</strong></p>
                <p style="margin:0;font-size:13px;color:#666;">{{ $customer_address->name }}</p>
                <p style="margin:5px 0;font-size:13px;color:#666;">{{ $customer_address->address }}</p>
                <p style="margin:5px 0;font-size:13px;color:#666;">{{ $customer_address->phone }}</p>
                <p style="margin:5px 0;font-size:13px;color:#666;"><strong>Payment Method: </strong>COD</p>
            </div>

            <!-- Items -->
            <div style="overflow-x:auto;">
                @php
                    $showDiscount = collect($order_items)->contains(fn($item) => $item->discount != 0);

                   
                @endphp
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr>
                            <th style="border:1px solid #ddd;padding:8px;background:#f2f2f2;text-align:left;">Item</th>
                            <th style="border:1px solid #ddd;padding:8px;background:#f2f2f2;text-align:center;">Qty</th>
                            <th style="border:1px solid #ddd;padding:8px;background:#f2f2f2;text-align:right;">Price
                            </th>
                            @if ($showDiscount)
                                <th style="border:1px solid #ddd;padding:8px;background:#f2f2f2;text-align:right;">
                                    Discount</th>
                            @endif
                            <th style="border:1px solid #ddd;padding:8px;background:#f2f2f2;text-align:right;">Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order_items as $item)
                            @php
                                $product_variant_info = ProductVariant::with('product')->find($item->product_id);
                                $product_info = NewProduct::where('id', $product_variant_info->product_id)->first();

                            @endphp
                            <tr>
                                <td style="border:1px solid #ddd;padding:8px;">
                                    {{ $product_info->name ?? 'N/A' }}<br>
                                    <span style="color:#868686;font-size:12px;">
                                        SKU: {{ $product_variant_info->sku ?? '-' }}
                                    </span>
                                    @if ($item->free_delivery)
                                        <br>
                                        <span style="color:#198754;font-weight:bold;font-size:12px;">Free Delivery</span>
                                    @endif
                                </td>

                                <td style="border:1px solid #ddd;padding:8px;text-align:center;">{{ $item->qty }}
                                </td>
                                <td style="border:1px solid #ddd;padding:8px;text-align:right;">{{ $item->price }}</td>
                                @if ($showDiscount)
                                    <td style="border:1px solid #ddd;padding:8px;text-align:right;">
                                        {{ $item->discount }}
                                    </td>
                                @endif
                                </td>
                                <td style="border:1px solid #ddd;padding:8px;text-align:right;">{{ $item->total }}
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div style="margin-top:20px;">
                <table style="width:100%;font-size:13px;">
                    <tr>
                        <td style="text-align:right;">Subtotal:</td>
                        <td style="text-align:right;width:120px;">{{ $order_info->subtotal }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Delivery Charge:</td>
                        <td style="text-align:right;width:120px;">{{ $order_info->shipping }}</td>
                    </tr>
                    @if ($order_info->discount > 0)
                        <tr>
                            <td style="text-align:right;">Discount (Coupon):</td>
                            <td style="text-align:right;width:120px;">{{ $order_info->discount }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td style="text-align:right;font-size:14px;"><strong>Total:</strong></td>
                        <td style="text-align:right;font-size:14px;"><strong>{{ $order_info->grand_total }}</strong>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            <div style="margin-top:30px;text-align:center;font-size:12px;color:#777;">
                Thank you for your business!
            </div>

        </div>
    </div>

    <!-- Print Rules -->
    <style>
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            .no-print {
                display: none !important;
            }

            body {
                background: #ffffff;
            }
        }
    </style>

</body>

</html>
