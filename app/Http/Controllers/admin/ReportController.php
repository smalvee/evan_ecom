<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function sales_index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.report.new_sales', compact('orders'));
    }

    public function item_sales_index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        $product_variant = ProductVariant::orderBy('created_at', 'desc')->paginate(100);

        return view('admin.report.item_wise_sales', compact('orders', 'product_variant'));
    }

    public function exportExcel(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $status = $request->status;

        $fileName = 'sales_report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new SalesReportExport($from_date, $to_date, $status), $fileName);
    }

    public function exportPdf(Request $request)
    {
        // Use dompdf or snappy to generate PDF
        // Example: return PDF::loadView('admin.reports.sales_pdf', compact('orders'))->download('sales_report.pdf');
    }
}
