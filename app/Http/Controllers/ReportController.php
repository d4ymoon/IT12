<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        // Demo data
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
            (object)[
                'product_code' => 'PRD-003',
                'product_name' => 'Wireless Mouse',
                'category' => 'Electronics',
                'quantity' => 0,
                'unit_price' => 29.99,
                'last_updated' => '2024-11-10'
            ],
            (object)[
                'product_code' => 'PRD-004',
                'product_name' => 'Standing Desk',
                'category' => 'Furniture',
                'quantity' => 23,
                'unit_price' => 599.99,
                'last_updated' => '2024-11-16'
            ],
            (object)[
                'product_code' => 'PRD-005',
                'product_name' => 'Monitor 27" 4K',
                'category' => 'Electronics',
                'quantity' => 18,
                'unit_price' => 449.99,
                'last_updated' => '2024-11-15'
            ],
        ];

        $lowStockItems = count(array_filter($inventory, function($item) {
            return $item->quantity < 5;
        }));

        return [
            'inventory' => $inventory,
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
            (object)[
                'order_id' => 'PO-2024-002',
                'supplier' => 'Office Furniture Co.',
                'product' => 'Office Chair Executive',
                'quantity' => 15,
                'unit_cost' => 280.00,
                'total_cost' => 4200.00,
                'order_date' => '2024-11-12',
                'status' => 'Completed'
            ],
            (object)[
                'order_id' => 'PO-2024-003',
                'supplier' => 'Global Electronics',
                'product' => 'Wireless Mouse',
                'quantity' => 50,
                'unit_cost' => 22.50,
                'total_cost' => 1125.00,
                'order_date' => '2024-11-15',
                'status' => 'Pending'
            ],
        ];

        return [
            'purchases' => $purchases
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
            (object)[
                'order_id' => 'SO-2024-002',
                'customer' => 'XYZ Enterprises',
                'product' => 'Office Chair Executive',
                'quantity' => 8,
                'unit_price' => 349.99,
                'total_amount' => 2799.92,
                'order_date' => '2024-11-14',
                'status' => 'Completed'
            ],
            (object)[
                'order_id' => 'SO-2024-003',
                'customer' => 'Global Tech Inc.',
                'product' => 'Monitor 27" 4K',
                'quantity' => 3,
                'unit_price' => 449.99,
                'total_amount' => 1349.97,
                'order_date' => '2024-11-16',
                'status' => 'Completed'
            ],
        ];

        return [
            'sales' => $sales
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
            (object)[
                'supplier_id' => 'SUP-002',
                'supplier_name' => 'Office Furniture Co.',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@officefurniture.com',
                'phone' => '(555) 234-5678',
                'product_category' => 'Furniture',
                'total_orders' => 38,
                'status' => 'Active'
            ],
            (object)[
                'supplier_id' => 'SUP-003',
                'supplier_name' => 'Global Electronics',
                'contact_person' => 'Mike Chen',
                'email' => 'mike@globalelectronics.com',
                'phone' => '(555) 345-6789',
                'product_category' => 'Electronics',
                'total_orders' => 27,
                'status' => 'Active'
            ],
        ];

        return [
            'suppliers' => $suppliers
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
            (object)[
                'user_id' => 'USR-002',
                'name' => 'Sarah Manager',
                'email' => 'sarah.manager@company.com',
                'role' => 'Manager',
                'department' => 'Sales',
                'last_login' => '2024-11-16 08:30',
                'join_date' => '2023-03-20',
                'status' => 'Active'
            ],
            (object)[
                'user_id' => 'USR-003',
                'name' => 'Mike Technician',
                'email' => 'mike.tech@company.com',
                'role' => 'Staff',
                'department' => 'IT',
                'last_login' => '2024-11-15 14:20',
                'join_date' => '2023-06-10',
                'status' => 'Active'
            ],
        ];

        return [
            'users' => $users
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