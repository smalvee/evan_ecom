<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\PreOrderService;
use Illuminate\Http\Request;

class PreOrderController extends Controller
{
    protected PreOrderService $preOrders;

    public function __construct(PreOrderService $preOrders)
    {
        $this->preOrders = $preOrders;
    }

    /**
     * List every pre-order order item, with a derived "Stock Available" state.
     */
    public function index(Request $request)
    {
        $filter = $request->get('status');

        $query = OrderItem::query()
            ->from('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->leftJoin('users as u', 'u.id', '=', 'o.user_id')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'oi.product_id')
            ->leftJoin('new_products as np', 'np.id', '=', 'pv.product_id')
            ->where('oi.is_pre_order', true)
            ->select([
                'oi.id as item_id',
                'oi.qty',
                'oi.price',
                'oi.total',
                'oi.pre_order_status',
                'oi.name as variant_name',
                'oi.product_id as variant_id',
                'o.id as order_pk',
                'o.order_id as order_code',
                'o.status as order_status',
                'o.payment_status',
                'o.created_at',
                'o.name as customer_name',
                'o.phone as customer_phone',
                'pv.sku as variant_sku',
                'pv.qty as current_stock',
                'np.name as product_name',
            ]);

        if ($filter === 'available') {
            $query->where('oi.pre_order_status', 'pending')
                ->whereRaw('CAST(pv.qty AS SIGNED) >= oi.qty');
        } elseif (in_array($filter, ['pending', 'processing', 'completed', 'cancelled'], true)) {
            $query->where('oi.pre_order_status', $filter);
        }

        if ($request->filled('keyword')) {
            $kw = $request->get('keyword');
            $query->where(function ($q) use ($kw) {
                $q->where('o.order_id', 'like', "%{$kw}%")
                    ->orWhere('o.name', 'like', "%{$kw}%")
                    ->orWhere('o.phone', 'like', "%{$kw}%")
                    ->orWhere('np.name', 'like', "%{$kw}%")
                    ->orWhere('pv.sku', 'like', "%{$kw}%");
            });
        }

        $preOrders = $query->orderByDesc('o.created_at')->paginate(15)->appends($request->query());

        $preOrders->getCollection()->transform(function ($row) {
            $stock = ($row->current_stock === null || $row->current_stock === '') ? null : (int) $row->current_stock;
            $row->stock_value = $stock;
            $row->stock_available = $stock === null || $stock >= (int) $row->qty;

            if ($row->pre_order_status === 'pending') {
                $row->pre_order_state = $row->stock_available ? 'available' : 'pending';
            } else {
                $row->pre_order_state = $row->pre_order_status;
            }

            return $row;
        });

        return view('admin.pre_orders.new_list', compact('preOrders', 'filter'));
    }

    /**
     * Pre-order detail page.
     */
    public function details($id)
    {
        $item = OrderItem::where('is_pre_order', true)->findOrFail($id);
        $order = Order::with('items.variant.product')->findOrFail($item->order_id);
        $variant = ProductVariant::with('product')->find($item->product_id);

        $stock = $variant?->stock();
        $stockAvailable = $stock === null || $stock >= (int) $item->qty;

        return view('admin.pre_orders.new_details', compact('item', 'order', 'variant', 'stock', 'stockAvailable'));
    }

    /**
     * Admin action: process a pre-order. Delegates to PreOrderService which runs
     * in a transaction, locks the order and deducts stock exactly once.
     */
    public function process($id)
    {
        $item = OrderItem::where('is_pre_order', true)->findOrFail($id);
        $order = Order::findOrFail($item->order_id);

        $result = $this->preOrders->processOrder($order);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ]);
    }
}
