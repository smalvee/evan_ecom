<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $status;

    public function __construct($from_date = null, $to_date = null, $status = null)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Order::query();

        if ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer Name',
            'Phone',
            'Status',
            'Subtotal',
            'Shipping',
            'Discount',
            'Grand Total',
            'Order Date',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->name,
            $order->phone,
            ucfirst($order->status),
            number_format($order->subtotal, 2),
            number_format($order->shipping, 2),
            number_format($order->discount, 2),
            number_format($order->grand_total, 2),
            $order->created_at->format('d M Y'),
        ];
    }
}
