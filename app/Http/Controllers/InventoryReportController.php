<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryReportController extends Controller
{
    public function index()
    {
        // Get inventory data
        $inventoryData = $this->getInventoryReportsData();

        return view('reports.inventory.index', compact('inventoryData'));
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

        // Stock Adjustments (latest 20)
        $adjustments = DB::table('stock_adjustments')
            ->join('stock_adjustment_items', 'stock_adjustment_items.stock_adjustment_id', '=', 'stock_adjustments.id')
            ->join('products', 'stock_adjustment_items.product_id', '=', 'products.id')
            ->join('users', 'stock_adjustments.processed_by_user_id', '=', 'users.id')
            ->select(
                'products.name as product_name',
                'stock_adjustments.adjustment_date',
                'stock_adjustments.adjustment_type',
                'stock_adjustments.reason_notes',
                'stock_adjustment_items.quantity_change',
                'stock_adjustment_items.unit_cost_at_adjustment',
                DB::raw("CONCAT(users.f_name, ' ', COALESCE(users.m_name, ''), ' ', users.l_name) AS processed_by")
            )
            ->orderBy('stock_adjustments.adjustment_date', 'desc')
            ->limit(20)
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

        // Returns Stock Impact
        $returns = DB::table('product_returns')
            ->join('return_items', 'return_items.product_return_id', '=', 'product_returns.id')
            ->join('products', 'return_items.product_id', '=', 'products.id')
            ->join('users', 'product_returns.user_id', '=', 'users.id')
            ->select(
                'products.name as product_name',
                'product_returns.created_at',
                'product_returns.return_reason',
                'product_returns.notes',
                'return_items.quantity_returned',
                'return_items.inventory_adjusted',
                'return_items.refunded_price_per_unit',
                DB::raw("CONCAT(users.f_name, ' ', COALESCE(users.m_name,''), ' ', users.l_name) AS processed_by")
            )
            ->orderBy('product_returns.created_at', 'desc')
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

        // Summary Statistics
        $summaryStats = DB::table('products')
            ->where('products.is_active', true)
            ->select(
                DB::raw('COUNT(*) as total_products'),
                DB::raw('SUM(quantity_in_stock) as total_quantity'),
                DB::raw('SUM(quantity_in_stock * COALESCE(latest_unit_cost, 0)) as total_inventory_value'),
                DB::raw('COUNT(CASE WHEN quantity_in_stock <= reorder_level OR quantity_in_stock = 0 THEN 1 END) as low_stock_count'),
                DB::raw('COUNT(CASE WHEN quantity_in_stock = 0 THEN 1 END) as out_of_stock_count')
            )
            ->first();

        return [
            'stockLevels' => $stockLevels,
            'lowStockAlerts' => $lowStockAlerts,
            'stockMovement' => $stockMovement,
            'valuationReport' => $valuationReport,
            'summaryStats' => $summaryStats,
            'stockAdjustments' => $adjustments ,
            'returns' => $returns,
        ];
    }

    public function exportInventoryReport(Request $request)
    {
        // PDF export functionality can be implemented here
    }
}