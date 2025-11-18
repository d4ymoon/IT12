<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $module = $request->get('module', 'inventory');
        
        // Load data based on the selected module
        switch($module) {
            case 'inventory':
                $data = $this->getInventoryData($request);
                break;
            case 'purchase':
                $data = $this->getPurchaseData($request);
                break;
            case 'sales':
                $data = $this->getSalesData($request);
                break;
            case 'supplier':
                $data = $this->getSupplierData($request);
                break;
            case 'user':
                $data = $this->getUserData($request);
                break;
            default:
                $data = $this->getInventoryData($request);
        }
        
        return view('reports.index', array_merge($data, ['activeModule' => $module]));
    }
    
    private function getInventoryData(Request $request)
    {
        // For demo purposes - in real app, this would query the database
        $inventory = [
            (object)[
                'product_code' => 'PRD-001',
                'product_name' => 'Laptop Dell XPS 15',
                'category' => 'Electronics',
                'quantity' => 45,
                'unit_price' => 1299.99,
                'last_updated' => '2024-11-15'
            ],
            (object)[
                'product_code' => 'PRD-002',
                'product_name' => 'Office Chair Executive',
                'category' => 'Furniture',
                'quantity' => 12,
                'unit_price' => 349.99,
                'last_updated' => '2024-11-14'
            ],
            // Add more demo data...
        ];

        $totalItems = count($inventory);
        $totalQuantity = array_sum(array_column($inventory, 'quantity'));
        $totalValue = 0;
        foreach ($inventory as $item) {
            $totalValue += $item->quantity * $item->unit_price;
        }
        $lowStockItems = count(array_filter($inventory, function($item) {
            return $item->quantity < 5;
        }));

        return [
            'inventory' => $inventory,
            'totalItems' => $totalItems,
            'totalQuantity' => $totalQuantity,
            'totalValue' => $totalValue,
            'lowStockItems' => $lowStockItems
        ];
    }
    
    private function getPurchaseData(Request $request)
    {
        // Demo data
        $purchases = [
            (object)[
                'order_id' => 'PO-2024-001',
                'supplier' => 'Tech Supplies Inc.',
                'product' => 'Laptop Dell XPS 15',
                'quantity' => 10,
                'unit_cost' => 1150.00,
                'total_cost' => 11500.00,
                'order_date' => '2024-11-10',
                'status' => 'Completed'
            ],
            // Add more demo data...
        ];

        $totalPurchases = count($purchases);
        $totalSpent = array_sum(array_column($purchases, 'total_cost'));
        $pendingOrders = count(array_filter($purchases, function($purchase) {
            return $purchase->status === 'Pending';
        }));
        $supplierCount = count(array_unique(array_column($purchases, 'supplier')));

        return [
            'purchases' => $purchases,
            'totalPurchases' => $totalPurchases,
            'totalSpent' => $totalSpent,
            'pendingOrders' => $pendingOrders,
            'supplierCount' => $supplierCount
        ];
    }
    
    private function getSalesData(Request $request)
    {
        // Demo data
        $sales = [
            (object)[
                'order_id' => 'SO-2024-001',
                'customer' => 'ABC Corporation',
                'product' => 'Laptop Dell XPS 15',
                'quantity' => 5,
                'unit_price' => 1299.99,
                'total_amount' => 6499.95,
                'order_date' => '2024-11-12',
                'status' => 'Completed'
            ],
            // Add more demo data...
        ];

        $totalRevenue = array_sum(array_column($sales, 'total_amount'));
        $totalOrders = count($sales);
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $activeCustomers = count(array_unique(array_column($sales, 'customer')));

        return [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
            'activeCustomers' => $activeCustomers
        ];
    }
    
    private function getSupplierData(Request $request)
    {
        // Demo data
        $suppliers = [
            (object)[
                'supplier_id' => 'SUP-001',
                'supplier_name' => 'Tech Supplies Inc.',
                'contact_person' => 'John Smith',
                'email' => 'john@techsupplies.com',
                'phone' => '(555) 123-4567',
                'product_category' => 'Electronics',
                'total_orders' => 45,
                'status' => 'Active'
            ],
            // Add more demo data...
        ];

        $totalSuppliers = count($suppliers);
        $activeSuppliers = count(array_filter($suppliers, function($supplier) {
            return $supplier->status === 'Active';
        }));
        $totalSpent = 45230.75; // This would be calculated from purchases in real app
        $totalOrders = array_sum(array_column($suppliers, 'total_orders'));

        return [
            'suppliers' => $suppliers,
            'totalSuppliers' => $totalSuppliers,
            'activeSuppliers' => $activeSuppliers,
            'totalSpent' => $totalSpent,
            'totalOrders' => $totalOrders
        ];
    }
    
    private function getUserData(Request $request)
    {
        // Demo data
        $users = [
            (object)[
                'user_id' => 'USR-001',
                'name' => 'John Administrator',
                'email' => 'john.admin@company.com',
                'role' => 'Admin',
                'department' => 'Management',
                'last_login' => '2024-11-16 09:45',
                'join_date' => '2023-01-15',
                'status' => 'Active'
            ],
            // Add more demo data...
        ];

        $totalUsers = count($users);
        $activeUsers = count(array_filter($users, function($user) {
            return $user->status === 'Active';
        }));
        $adminUsers = count(array_filter($users, function($user) {
            return $user->role === 'Admin';
        }));
        $staffUsers = count(array_filter($users, function($user) {
            return $user->role === 'Staff';
        }));

        return [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'adminUsers' => $adminUsers,
            'staffUsers' => $staffUsers
        ];
    }
    
    public function exportPdf(Request $request, $module)
    {
        // Load data based on the selected module
        switch($module) {
            case 'inventory':
                $data = $this->getInventoryData($request);
                $view = 'reports.pdf.inventory';
                $filename = 'inventory-report-' . date('Y-m-d') . '.pdf';
                break;
            case 'purchase':
                $data = $this->getPurchaseData($request);
                $view = 'reports.pdf.purchase';
                $filename = 'purchase-report-' . date('Y-m-d') . '.pdf';
                break;
            case 'sales':
                $data = $this->getSalesData($request);
                $view = 'reports.pdf.sales';
                $filename = 'sales-report-' . date('Y-m-d') . '.pdf';
                break;
            case 'supplier':
                $data = $this->getSupplierData($request);
                $view = 'reports.pdf.supplier';
                $filename = 'supplier-report-' . date('Y-m-d') . '.pdf';
                break;
            case 'user':
                $data = $this->getUserData($request);
                $view = 'reports.pdf.user';
                $filename = 'user-report-' . date('Y-m-d') . '.pdf';
                break;
            default:
                $data = $this->getInventoryData($request);
                $view = 'reports.pdf.inventory';
                $filename = 'inventory-report-' . date('Y-m-d') . '.pdf';
        }
        
        $data['filters'] = $request->all();
        
        $pdf = PDF::loadView($view, $data);
        return $pdf->download($filename);
    }
}
