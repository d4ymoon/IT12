<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockInItem;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or default to current week
        $startDate = $request->input('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfWeek()->format('Y-m-d'));
        
        // Convert to Carbon instances for easier manipulation
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Get previous period for comparison
        $daysDiff = $start->diffInDays($end);
        $prevStart = $start->copy()->subDays($daysDiff + 1);
        $prevEnd = $start->copy()->subDay();
        
        return view('dashboard', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'kpiData' => $this->getKPIData($start, $end, $prevStart, $prevEnd),
            'revenueTrendData' => $this->getRevenueTrendData($start, $end),
            'categorySalesData' => $this->getCategorySalesData($start, $end),
            'topProductsData' => $this->getTopProductsData($start, $end),
            'staffPerformanceData' => $this->getStaffPerformanceData($start, $end),
            'lowStockAlerts' => $this->getLowStockAlerts(),
            'stockControlIssues' => $this->getStockControlIssues($start, $end),
        ]);
    }
    
    private function getKPIData($start, $end, $prevStart, $prevEnd)
    {
        // Total Revenue (from payments)
        $currentRevenue = Payment::whereBetween('payment_date', [$start, $end])
            ->sum('amount_tendered');
            
        $previousRevenue = Payment::whereBetween('payment_date', [$prevStart, $prevEnd])
            ->sum('amount_tendered');
            
        $revenueTrend = $previousRevenue > 0 ? 
            (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        
        // Gross Profit (Revenue - COGS)
        $currentCOGS = SaleItem::whereHas('sale', function($query) use ($start, $end) {
                $query->whereBetween('sale_date', [$start, $end]);
            })
            ->join('stock_in_items', function($join) {
                $join->on('sale_items.product_id', '=', 'stock_in_items.product_id')
                     ->whereRaw('stock_in_items.created_at <= sale_items.created_at');
            })
            ->select(DB::raw('SUM(sale_items.quantity_sold * stock_in_items.actual_unit_cost) as total_cogs'))
            ->value('total_cogs') ?? 0;
            
        $currentGrossProfit = $currentRevenue - $currentCOGS;
        
        $previousCOGS = SaleItem::whereHas('sale', function($query) use ($prevStart, $prevEnd) {
                $query->whereBetween('sale_date', [$prevStart, $prevEnd]);
            })
            ->join('stock_in_items', function($join) {
                $join->on('sale_items.product_id', '=', 'stock_in_items.product_id')
                     ->whereRaw('stock_in_items.created_at <= sale_items.created_at');
            })
            ->select(DB::raw('SUM(sale_items.quantity_sold * stock_in_items.actual_unit_cost) as total_cogs'))
            ->value('total_cogs') ?? 0;
            
        $previousGrossProfit = $previousRevenue - $previousCOGS;
        $profitTrend = $previousGrossProfit > 0 ? 
            (($currentGrossProfit - $previousGrossProfit) / $previousGrossProfit) * 100 : 0;
        
        // Total Sales Transactions
        $currentTransactions = Sale::whereBetween('sale_date', [$start, $end])->count();
        $previousTransactions = Sale::whereBetween('sale_date', [$prevStart, $prevEnd])->count();
        $transactionsTrend = $previousTransactions > 0 ? 
            (($currentTransactions - $previousTransactions) / $previousTransactions) * 100 : 0;
        
        // Average Order Value
        $currentAOV = $currentTransactions > 0 ? $currentRevenue / $currentTransactions : 0;
        $previousAOV = $previousTransactions > 0 ? $previousRevenue / $previousTransactions : 0;
        $aovTrend = $previousAOV > 0 ? (($currentAOV - $previousAOV) / $previousAOV) * 100 : 0;
        
        // Current Inventory Value
        $inventoryValue = Product::where('is_active', true)
            ->sum(DB::raw('quantity_in_stock * latest_unit_cost'));
            
        // Profit Margin
        $profitMargin = $currentRevenue > 0 ? ($currentGrossProfit / $currentRevenue) * 100 : 0;
        
        return [
            'totalRevenue' => $currentRevenue,
            'grossProfit' => $currentGrossProfit,
            'avgOrderValue' => $currentAOV,
            'totalTransactions' => $currentTransactions,
            'inventoryValue' => $inventoryValue,
            'profitMargin' => $profitMargin,
            'trends' => [
                'revenue' => $revenueTrend,
                'profit' => $profitTrend,
                'aov' => $aovTrend,
                'transactions' => $transactionsTrend,
                'inventory' => 0, // This would require historical inventory data
                'margin' => 0, // Simplified for this example
            ]
        ];
    }
    
    private function getRevenueTrendData($start, $end)
    {
        // Group revenue by day
        return Payment::whereBetween('payment_date', [$start, $end])
            ->select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('SUM(amount_tendered) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function($item) {
                return [Carbon::parse($item->date)->format('M j') => $item->revenue];
            });
    }
    
    private function getCategorySalesData($start, $end)
    {
        return SaleItem::whereHas('sale', function($query) use ($start, $end) {
                $query->whereBetween('sale_date', [$start, $end]);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity_sold * sale_items.unit_price) as revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();
    }
    
    private function getTopProductsData($start, $end)
    {
        return SaleItem::whereHas('sale', function($query) use ($start, $end) {
                $query->whereBetween('sale_date', [$start, $end]);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(sale_items.quantity_sold) as total_sold'),
                DB::raw('SUM(sale_items.quantity_sold * sale_items.unit_price) as revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
    }
    
    private function getStaffPerformanceData($start, $end)
    {
        return Sale::whereBetween('sale_date', [$start, $end])
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.f_name, ' ', users.l_name) as staff_name"),
                DB::raw('SUM(sale_items.quantity_sold * sale_items.unit_price) as revenue'),
                DB::raw('COUNT(DISTINCT sales.id) as transactions')
            )
            ->groupBy('users.id', 'users.f_name', 'users.l_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
    }
    
    private function getLowStockAlerts()
    {
        return Product::where('is_active', true)
            ->whereRaw('quantity_in_stock <= reorder_level')
            ->select('id', 'name', 'sku', 'quantity_in_stock', 'reorder_level')
            ->orderBy('quantity_in_stock')
            ->limit(10)
            ->get();
    }
    
    private function getStockControlIssues($start, $end)
    {
        return StockAdjustmentItem::whereHas('stockAdjustment', function($query) use ($start, $end) {
                $query->whereBetween('adjustment_date', [$start, $end])
                      ->whereIn('adjustment_type', ['Damage/Scrap', 'Physical Count', 'Error Correction']);
            })
            ->join('stock_adjustments', 'stock_adjustment_items.stock_adjustment_id', '=', 'stock_adjustments.id')
            ->join('products', 'stock_adjustment_items.product_id', '=', 'products.id')
            ->select(
                'stock_adjustments.adjustment_date',
                'products.name as product_name',
                'stock_adjustment_items.quantity_change',
                'stock_adjustments.adjustment_type',
                'stock_adjustments.reason_notes'
            )
            ->where('stock_adjustment_items.quantity_change', '<', 0) // Only negative adjustments
            ->orderByDesc('stock_adjustments.adjustment_date')
            ->limit(10)
            ->get();
    }
    
    // API endpoint for AJAX updates
    public function getDashboardData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $prevStart = $start->copy()->subDays($start->diffInDays($end) + 1);
        $prevEnd = $start->copy()->subDay();
        
        return response()->json([
            'kpiData' => $this->getKPIData($start, $end, $prevStart, $prevEnd),
            'revenueTrendData' => $this->getRevenueTrendData($start, $end),
            'categorySalesData' => $this->getCategorySalesData($start, $end),
            'topProductsData' => $this->getTopProductsData($start, $end),
            'staffPerformanceData' => $this->getStaffPerformanceData($start, $end),
            'lowStockAlerts' => $this->getLowStockAlerts(),
            'stockControlIssues' => $this->getStockControlIssues($start, $end),
        ]);
    }
}