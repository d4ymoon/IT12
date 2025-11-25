<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or default to this month
        $dateRange = $request->get('date_range', 'thismonth');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Set dates based on selection
        list($startDate, $endDate) = $this->calculateDateRange($dateRange, $startDate, $endDate);

        // Sales Reports Data
        $salesData = $this->getSalesReportsData($startDate, $endDate);
        $inventoryData = $this->getInventoryReportsData();
        $financialData = $this->getFinancialReportsData($startDate, $endDate);

        return view('reports.index', compact(
            'salesData',
            'inventoryData', 
            'financialData',
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
        // Sales by Date Range
        $salesByDate = DB::table('sales')
            ->select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM((SELECT SUM(si.unit_price * si.quantity_sold) FROM sale_items si WHERE si.sale_id = sales.id)) as total_revenue')
            )
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date')
            ->get();
    
        // Detailed Sales with pagination - Fixed to include payment method directly
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
                'payments.payment_method' // Get payment method directly
            )
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->orderBy('sales.sale_date', 'desc')
            ->paginate(10);
    
        // Product Performance
        $productPerformance = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity_sold) as total_quantity'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) as total_revenue'),
                DB::raw('AVG(sale_items.unit_price) as avg_price')
            )
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
    
        return [
            'salesByDate' => $salesByDate,
            'detailedSales' => $detailedSales,
            'productPerformance' => $productPerformance,
            'categoryAnalysis' => $categoryAnalysis,
            'dateRange' => ['start' => $startDate, 'end' => $endDate]
        ];
    }

    private function getInventoryReportsData()
    {
        // Stock Levels
        $stockLevels = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_active', true)
            ->select(
                'products.name',
                'categories.name as category_name',
                'products.quantity_in_stock',
                'products.reorder_level',
                'products.latest_unit_cost',
                DB::raw('(products.quantity_in_stock * COALESCE(products.latest_unit_cost, 0)) as stock_value')
            )
            ->orderBy('products.quantity_in_stock')
            ->get();

        // Low Stock Alerts
        $lowStockAlerts = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_active', true)
            ->where(function($query) {
                $query->where('products.quantity_in_stock', '<=', DB::raw('products.reorder_level'))
                      ->orWhere('products.quantity_in_stock', 0);
            })
            ->select(
                'products.name',
                'categories.name as category_name',
                'products.quantity_in_stock',
                'products.reorder_level',
                'products.latest_unit_cost'
            )
            ->orderBy('products.quantity_in_stock')
            ->get();

        // Stock Movement
        $stockMovement = DB::table('stock_in_items')
            ->join('products', 'stock_in_items.product_id', '=', 'products.id')
            ->join('stock_ins', 'stock_in_items.stock_in_id', '=', 'stock_ins.id')
            ->select(
                'products.name',
                'stock_ins.stock_in_date',
                'stock_in_items.quantity_received',
                'stock_in_items.actual_unit_cost',
                DB::raw('(stock_in_items.quantity_received * stock_in_items.actual_unit_cost) as total_cost')
            )
            ->orderBy('stock_ins.stock_in_date', 'desc')
            ->limit(20)
            ->get();

        // Valuation Report
        $valuationReport = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_active', true)
            ->select(
                'categories.name as category_name',
                DB::raw('COUNT(*) as product_count'),
                DB::raw('SUM(products.quantity_in_stock) as total_quantity'),
                DB::raw('SUM(products.quantity_in_stock * COALESCE(products.latest_unit_cost, 0)) as total_value')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_value')
            ->get();

        return [
            'stockLevels' => $stockLevels,
            'lowStockAlerts' => $lowStockAlerts,
            'stockMovement' => $stockMovement,
            'valuationReport' => $valuationReport
        ];
    }

    private function getFinancialReportsData($startDate, $endDate)
    {
        // Profit & Loss
        $revenue = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));

        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->sum(DB::raw('sale_items.quantity_sold * COALESCE(products.latest_unit_cost, 0)'));

        $grossProfit = $revenue - $cogs;

        // COGS Analysis
        $cogsAnalysis = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity_sold * COALESCE(products.latest_unit_cost, 0)) as total_cogs'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) as total_revenue'),
                DB::raw('SUM(sale_items.quantity_sold) as total_quantity')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_cogs')
            ->get();

        // Payment Methods
        $paymentMethods = DB::table('payments')
            ->join('sales', 'payments.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount_tendered - change_given) as total_amount')
            )
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();

        return [
            'profitLoss' => [
                'revenue' => $revenue,
                'cogs' => $cogs,
                'grossProfit' => $grossProfit,
                'grossMargin' => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0
            ],
            'cogsAnalysis' => $cogsAnalysis,
            'paymentMethods' => $paymentMethods
        ];
    }

    // Export methods can be added here later
    public function exportSalesReport(Request $request)
    {
        // PDF export functionality can be implemented here
    }

    public function exportInventoryReport(Request $request)
    {
        // PDF export functionality can be implemented here
    }

    public function exportFinancialReport(Request $request)
    {
        // PDF export functionality can be implemented here
    }
}