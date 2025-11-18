<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'items.product', 'payment']) // This should work if relationship is defined correctly
        ->latest();
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_contact', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Date filter
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('sale_date', $request->date);
        }

        $sales = $query->paginate(20);

        return view('sales.index', compact('sales'));
    }

    public function show($id)
{
    $sale = Sale::with(['user', 'items.product', 'payment'])
        ->findOrFail($id);

    return response()->json($sale);
}

    public function receipt($id)
    {
        $sale = Sale::with(['user', 'items.product', 'payment'])
            ->findOrFail($id);

        $pdf = PDF::loadView('pos.receipt', compact('sale'));
        
        return $pdf->download("receipt-{$sale->id}.pdf");
    }

    public function details($id)
    {
        $sale = Sale::with(['user', 'items.product', 'payment'])
            ->findOrFail($id);

        return response()->json($sale);
    }
}