<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or default to this month
        $dateRange = $request->get('date_range', 'thismonth');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Set dates based on selection
        list($startDate, $endDate) = $this->calculateDateRange($dateRange, $startDate, $endDate);

        // Get financial data
        $financialData = $this->getFinancialReportsData($startDate, $endDate);

        return view('reports.financial.index', compact(
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

    private function getFinancialReportsData($startDate, $endDate)
    {
        // Gross Revenue
        $grossRevenue = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));

        // Returns Amount
        $returnsAmount = DB::table('product_returns')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_refund_amount');

        // Net Revenue
        $netRevenue = $grossRevenue - $returnsAmount;

        // COGS (adjusted for returned items that were restocked)
        $grossCogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->sum(DB::raw('sale_items.quantity_sold * COALESCE(products.latest_unit_cost, 0)'));

        $returnedCogs = DB::table('return_items')
            ->join('product_returns', 'return_items.product_return_id', '=', 'product_returns.id')
            ->join('products', 'return_items.product_id', '=', 'products.id')
            ->whereBetween('product_returns.created_at', [$startDate, $endDate])
            ->where('return_items.inventory_adjusted', true)
            ->sum(DB::raw('COALESCE(products.latest_unit_cost, 0) * return_items.quantity_returned'));

        $netCogs = $grossCogs - $returnedCogs;
        $grossProfit = $netRevenue - $netCogs;

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

        // Additional Financial Metrics
        $totalTransactions = DB::table('sales')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->count();

        $averageTransactionValue = $totalTransactions > 0 ? $netRevenue / $totalTransactions : 0;

        return [
            'profitLoss' => [
                'gross_revenue' => $grossRevenue,
                'returns_amount' => $returnsAmount,
                'net_revenue' => $netRevenue,
                'gross_cogs' => $grossCogs,
                'returned_cogs' => $returnedCogs,
                'net_cogs' => $netCogs,
                'grossProfit' => $grossProfit,
                'grossMargin' => $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0
            ],
            'cogsAnalysis' => $cogsAnalysis,
            'paymentMethods' => $paymentMethods,
            'additionalMetrics' => [
                'total_transactions' => $totalTransactions,
                'average_transaction_value' => $averageTransactionValue,
                'returns_percentage' => $grossRevenue > 0 ? ($returnsAmount / $grossRevenue) * 100 : 0
            ]
        ];
    }

    public function exportFinancialReport(Request $request)
    {
        // PDF export functionality can be implemented here
    }
}