<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get date range based on filter
        $dateRange = $this->getDateRangeFromFilter($request);
        $startDate = $dateRange['start_date'];
        $endDate = $dateRange['end_date'];
        $filterType = $dateRange['filter_type'];

        // Total Revenue
        $totalRevenue = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));

        // Total Sales Transactions
        $totalTransactions = DB::table('sales')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->count();

        // Average Order Value
        $averageOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Gross Profit (simplified calculation using average cost)
        $grossProfit = $this->calculateGrossProfit($startDate, $endDate);

        // Inventory Value
        $inventoryValue = $this->calculateInventoryValue();

        // Sales Trend Data
        $salesTrend = $this->getSalesTrend($startDate, $endDate, $filterType);

        // Top Products
        $topProducts = $this->getTopProducts($startDate, $endDate);

        // Sales by Category
        $categorySales = $this->getCategorySales($startDate, $endDate);

        // Low Stock Alerts
        $lowStockAlerts = $this->getLowStockAlerts();

        // Recent Adjustments
        $recentAdjustments = $this->getRecentAdjustments();

        return view('dashboard', compact(
            'totalRevenue',
            'grossProfit',
            'averageOrderValue',
            'totalTransactions',
            'inventoryValue',
            'salesTrend',
            'topProducts',
            'categorySales',
            'lowStockAlerts',
            'recentAdjustments',
            'startDate',
            'endDate'
        ));
    }

    private function getDateRangeFromFilter(Request $request)
    {
        $filter = $request->get('filter', 'this_month');
        $filterType = $request->get('filter_type', 'preset');

        if ($filterType === 'custom') {
            $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        } else {
            switch ($filter) {
                case 'today':
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'this_year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                case 'this_month':
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
            }
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'filter_type' => $filterType
        ];
    }

    private function getSalesTrend($startDate, $endDate, $filterType)
    {
        $daysDiff = $startDate->diffInDays($endDate);
        
        // For longer periods, show weekly/monthly data
        if ($daysDiff > 60) {
            // Monthly data for long periods
            return $this->getMonthlySalesData($startDate, $endDate);
        } elseif ($daysDiff > 14) {
            // Weekly data for medium periods
            return $this->getWeeklySalesData($startDate, $endDate);
        } else {
            // Daily data for short periods
            return $this->getDailySalesData($startDate, $endDate);
        }
    }

    private function getDailySalesData($startDate, $endDate)
    {
        $dates = [];
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('M d');
            
            $dailyRevenue = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereDate('sales.sale_date', $currentDate->format('Y-m-d'))
                ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));
            
            $data[] = $dailyRevenue;
            $currentDate->addDay();
        }

        return [
            'labels' => $dates,
            'data' => $data
        ];
    }

    private function getWeeklySalesData($startDate, $endDate)
    {
        $labels = [];
        $data = [];
        $currentWeek = $startDate->copy();

        while ($currentWeek <= $endDate) {
            $weekStart = $currentWeek->copy()->startOfWeek();
            $weekEnd = $currentWeek->copy()->endOfWeek();
            
            $labels[] = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
            
            $weeklyRevenue = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereBetween('sales.sale_date', [$weekStart, $weekEnd])
                ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));
            
            $data[] = $weeklyRevenue;
            $currentWeek->addWeek();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getMonthlySalesData($startDate, $endDate)
    {
        $labels = [];
        $data = [];
        $currentMonth = $startDate->copy();

        while ($currentMonth <= $endDate) {
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();
            
            $labels[] = $monthStart->format('M Y');
            
            $monthlyRevenue = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereBetween('sales.sale_date', [$monthStart, $monthEnd])
                ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));
            
            $data[] = $monthlyRevenue;
            $currentMonth->addMonth();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function calculateGrossProfit($startDate, $endDate)
    {
        $revenue = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));

        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(products.latest_unit_cost, 0) * sale_items.quantity_sold'));

        return $revenue - $cogs;
    }

    private function calculateInventoryValue()
    {
        $inventoryValue = DB::table('products')
            ->where('is_active', true)
            ->sum(DB::raw('quantity_in_stock * COALESCE(latest_unit_cost, 0)'));

        return $inventoryValue;
    }

    private function getTopProducts($startDate, $endDate)
    {
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity_sold) as total_quantity')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'labels' => $topProducts->pluck('name')->toArray(),
            'data' => $topProducts->pluck('total_quantity')->toArray()
        ];
    }

    private function getCategorySales($startDate, $endDate)
    {
        $categorySales = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'categories.name',
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity_sold) as total_sales')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return [
            'labels' => $categorySales->pluck('name')->toArray(),
            'data' => $categorySales->pluck('total_sales')->toArray()
        ];
    }

    private function getLowStockAlerts()
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_active', true)
            ->where(function($query) {
                $query->where('products.quantity_in_stock', '<=', DB::raw('products.reorder_level'))
                      ->orWhere('products.quantity_in_stock', 0);
            })
            ->select(
                'products.name',
                'products.quantity_in_stock as current_stock',
                'products.reorder_level',
                'categories.name as category_name'
            )
            ->orderBy('products.quantity_in_stock')
            ->get();
    }

    private function getRecentAdjustments()
    {
        return DB::table('stock_adjustments')
            ->join('stock_adjustment_items', 'stock_adjustments.id', '=', 'stock_adjustment_items.stock_adjustment_id')
            ->join('products', 'stock_adjustment_items.product_id', '=', 'products.id')
            ->where('stock_adjustments.adjustment_date', '>=', Carbon::now()->subDays(7))
            ->select(
                'stock_adjustments.adjustment_date',
                'stock_adjustments.adjustment_type',
                'stock_adjustments.reason_notes',
                'stock_adjustment_items.quantity_change',
                'products.name as product_name'
            )
            ->orderBy('stock_adjustments.adjustment_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->adjustment_date = Carbon::parse($item->adjustment_date);
                return $item;
            });
    }
    
    public function getSalesChartData(Request $request)
    {
        $chartType = $request->get('chart_type', 'daily');
        
        // Get date range based on filter
        $dateRange = $this->getDateRangeFromFilter($request);
        $startDate = $dateRange['start_date'];
        $endDate = $dateRange['end_date'];
        
        // Get sales data based on chart type
        switch ($chartType) {
            case 'weekly':
                $salesData = $this->getWeeklySalesData($startDate, $endDate);
                break;
            case 'monthly':
                $salesData = $this->getMonthlySalesData($startDate, $endDate);
                break;
            case 'yearly':
                $salesData = $this->getYearlySalesData($startDate, $endDate);
                break;
            case 'daily':
            default:
                $salesData = $this->getDailySalesData($startDate, $endDate);
                break;
        }
        
        return response()->json($salesData);
    }
    
    private function getYearlySalesData($startDate, $endDate)
    {
        $labels = [];
        $data = [];
        $currentYear = $startDate->copy();
    
        while ($currentYear <= $endDate) {
            $yearStart = $currentYear->copy()->startOfYear();
            $yearEnd = $currentYear->copy()->endOfYear();
            
            $labels[] = $yearStart->format('Y');
            
            $yearlyRevenue = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereBetween('sales.sale_date', [$yearStart, $yearEnd])
                ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));
            
            $data[] = $yearlyRevenue;
            $currentYear->addYear();
        }
    
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}