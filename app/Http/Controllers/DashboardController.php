<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current date range (this month as default)
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

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

        // Sales Trend Data (last 7 days)
        $salesTrend = $this->getSalesTrend();

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
            'recentAdjustments'
        ));
    }

    private function calculateGrossProfit($startDate, $endDate)
    {
        // Simplified gross profit calculation using average cost
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
        // Calculate current stock level and value using average cost
        $inventoryValue = DB::table('products')
            ->where('is_active', true)
            ->sum(DB::raw('quantity_in_stock * COALESCE(latest_unit_cost, 0)'));

        return $inventoryValue;
    }

    private function getSalesTrend()
    {
        $dates = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates[] = $date->format('D');
            
            $dailyRevenue = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereDate('sales.sale_date', $date->format('Y-m-d'))
                ->sum(DB::raw('sale_items.unit_price * sale_items.quantity_sold'));
            
            $data[] = $dailyRevenue;
        }

        return [
            'labels' => $dates,
            'data' => $data
        ];
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
}