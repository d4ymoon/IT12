<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or default to this month
        $dateRange = $request->get('date_range', 'thismonth');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Set dates based on selection
        list($startDate, $endDate) = $this->calculateDateRange($dateRange, $startDate, $endDate);

        // Get sales data
        $salesData = $this->getSalesReportsData($startDate, $endDate);

        return view('reports.sales.index', compact(
            'salesData',
            'dateRange',
            'startDate',
            'endDate'
        ));
    }

    private function calculateDateRange($range, $customStart = null, $customEnd = null)
    {
        $today = Carbon::today();
        
        switch ($range) {
            case 'today':
                return [$today->copy()->startOfDay(), $today->copy()->endOfDay()];
            case 'yesterday':
                return [$today->copy()->subDay(), $today->copy()->subDay()];
            case 'thisweek':
                return [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()];
            case 'lastweek':
                return [$today->copy()->subWeek()->startOfWeek(), $today->copy()->subWeek()->endOfWeek()];
            case 'thismonth':
                return [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()];
            case 'lastmonth':
                return [$today->copy()->subMonth()->startOfMonth(), $today->copy()->subMonth()->endOfMonth()];
            case 'thisyear':
                return [$today->copy()->startOfYear(), $today->copy()->endOfYear()];
            case 'custom':
                return [
                    $customStart ? Carbon::parse($customStart) : $today->copy()->startOfMonth(), 
                    $customEnd ? Carbon::parse($customEnd) : $today->copy()->endOfMonth()
                ];
            default:
                return [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()];
        }
    }

    private function getSalesReportsData($startDate, $endDate)
    {
        // Net Sales by Date Range (Sales minus Returns)
        $salesByDate = DB::table('sales')
            ->leftJoin('sale_items', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('product_returns', function ($join) {
                $join->on(DB::raw('DATE(product_returns.created_at)'), '=', DB::raw('DATE(sales.sale_date)'));
            })
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(sales.sale_date) as date'),
                DB::raw('COUNT(DISTINCT sales.id) as transaction_count'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) as gross_revenue'),
                DB::raw('COALESCE(SUM(product_returns.total_refund_amount), 0) as returns_amount'),
                DB::raw('(SUM(sale_items.unit_price * sale_items.quantity_sold) - 
                        COALESCE(SUM(product_returns.total_refund_amount), 0)) as total_revenue')
            )
            ->groupBy(DB::raw('DATE(sales.sale_date)'))
            ->orderBy('date', 'asc')
            ->get();

        // Detailed Sales with pagination
        $detailedSales = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->leftJoin('payments', 'sales.id', '=', 'payments.sale_id')
            ->select(
                'sales.id',
                'sales.sale_date',
                'sales.customer_name',
                'sales.customer_contact',
                'users.f_name',
                'users.l_name',
                DB::raw('(SELECT COUNT(*) FROM sale_items WHERE sale_items.sale_id = sales.id) as items_count'),
                DB::raw('(SELECT SUM(unit_price * quantity_sold) FROM sale_items WHERE sale_items.sale_id = sales.id) as total_amount'),
                'payments.payment_method'
            )
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->orderBy('sales.sale_date', 'desc')
            ->paginate(10);

        // Product Performance (Net of Returns)
        $productPerformance = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity_sold) as total_quantity'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) as gross_revenue'),
                DB::raw('COALESCE((SELECT SUM(ri.total_line_refund) FROM return_items ri JOIN product_returns pr ON ri.product_return_id = pr.id WHERE ri.product_id = products.id AND pr.created_at BETWEEN ? AND ?), 0) as returns_amount'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) - COALESCE((SELECT SUM(ri.total_line_refund) FROM return_items ri JOIN product_returns pr ON ri.product_return_id = pr.id WHERE ri.product_id = products.id AND pr.created_at BETWEEN ? AND ?), 0) as total_revenue'),
                DB::raw('AVG(sale_items.unit_price) as avg_price')
            )
            ->addBinding([$startDate, $endDate, $startDate, $endDate], 'select')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Category Analysis
        $categoryAnalysis = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity_sold) as total_quantity'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) as total_revenue'),
                DB::raw('COUNT(DISTINCT sales.id) as transaction_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Summary Statistics
        $summaryStats = DB::table('sales')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('product_returns', function ($join) use ($startDate, $endDate) {
                $join->on('product_returns.sale_id', '=', 'sales.id')
                     ->whereBetween('product_returns.created_at', [$startDate, $endDate]);
            })
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(DISTINCT sales.id) as total_transactions'),
                DB::raw('SUM(sale_items.quantity_sold) as total_items_sold'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) as gross_revenue'),
                DB::raw('COALESCE(SUM(product_returns.total_refund_amount), 0) as total_returns'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) - COALESCE(SUM(product_returns.total_refund_amount), 0) as net_revenue'),
                DB::raw('AVG((SELECT SUM(unit_price * quantity_sold) FROM sale_items WHERE sale_items.sale_id = sales.id)) as avg_transaction_value')
            )
            ->first();

        return [
            'salesByDate' => $salesByDate,
            'detailedSales' => $detailedSales,
            'productPerformance' => $productPerformance,
            'categoryAnalysis' => $categoryAnalysis,
            'summaryStats' => $summaryStats,
            'dateRange' => ['start' => $startDate, 'end' => $endDate]
        ];
    }

    public function exportSalesReport(Request $request)
    {
        // PDF export functionality can be implemented here
        // You can reuse the same data fetching logic from index method
    }
}