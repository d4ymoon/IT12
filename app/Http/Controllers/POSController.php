<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }
    
    /**
     * Initialize a new sale when POS page loads
     */
    public function initializeSale()
    {
        try {
            DB::beginTransaction();

            $sale = Sale::create([
                'user_id' => session('user_id'),
                'sale_date' => now(),
                'customer_name' => null,
                'customer_contact' => null,
            ]);

            DB::commit();

            $sale->refresh();   // Force reload from database
            $sale->load('items.product', 'payment');

            return response()->json([
                'success' => true,
                'sale' => $sale,
                'message' => 'Sale initialized successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error initializing sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search product by SKU or barcode
     */
    public function searchProduct(Request $request)
    {
        try {
            $searchTerm = $request->input('search_term');
            
            $product = Product::where('is_active', true)
                ->where(function($query) use ($searchTerm) {
                    $query->where('sku', $searchTerm)
                          ->orWhere('manufacturer_barcode', $searchTerm);
                })
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if ($product->quantity_in_stock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product out of stock'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'product' => $product
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to current sale
     */
    public function addItem(Request $request)
    {
        try {
            DB::beginTransaction();

            $saleId = $request->input('sale_id');
            $productId = $request->input('product_id');
            $quantity = $request->input('quantity', 1);

            // Check if product exists and has stock
            $product = Product::where('is_active', true)->find($productId);
            if (!$product) {
                throw new Exception('Product not found');
            }

            if ($product->quantity_in_stock < $quantity) {
                throw new Exception('Insufficient stock. Available: ' . $product->quantity_in_stock);
            }

            // Check if item already exists in sale
            $existingItem = SaleItem::where('sale_id', $saleId)
                ->where('product_id', $productId)
                ->first();

            if ($existingItem) {
                // Update quantity if item exists
                $newQuantity = $existingItem->quantity_sold + $quantity;
                
                if ($product->quantity_in_stock < $newQuantity) {
                    throw new Exception('Insufficient stock. Available: ' . $product->quantity_in_stock);
                }

                $existingItem->update([
                    'quantity_sold' => $newQuantity
                ]);

                $item = $existingItem;
            } else {
                // Create new sale item
                $item = SaleItem::create([
                    'sale_id' => $saleId,
                    'product_id' => $productId,
                    'quantity_sold' => $quantity,
                    'unit_price' => $product->price
                ]);
            }

            // Calculate current total
            $total = SaleItem::where('sale_id', $saleId)
                ->selectRaw('SUM(quantity_sold * unit_price) as total')
                ->value('total');

            DB::commit();

            return response()->json([
                'success' => true,
                'item' => $item->load('product'),
                'total' => $total,
                'message' => 'Item added successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update item quantity
     */
    public function updateItem(Request $request, $itemId)
    {
        try {
            DB::beginTransaction();

            $saleItem = SaleItem::with('product')->findOrFail($itemId);
            $quantity = $request->input('quantity');

            if ($quantity <= 0) {
                $saleItem->delete();
            } else {
                // Check stock availability
                if ($saleItem->product->quantity_in_stock < $quantity) {
                    throw new Exception('Insufficient stock. Available: ' . $saleItem->product->quantity_in_stock);
                }

                $saleItem->update([
                    'quantity_sold' => $quantity
                ]);
            }

            // Recalculate total
            $total = SaleItem::where('sale_id', $saleItem->sale_id)
                ->selectRaw('SUM(quantity_sold * unit_price) as total')
                ->value('total');

            DB::commit();

            return response()->json([
                'success' => true,
                'total' => $total,
                'message' => 'Item updated successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove item from sale
     */
    public function removeItem($itemId)
    {
        try {
            DB::beginTransaction();

            $saleItem = SaleItem::findOrFail($itemId);
            $saleId = $saleItem->sale_id;
            $saleItem->delete();

            // Recalculate total
            $total = SaleItem::where('sale_id', $saleId)
                ->selectRaw('SUM(quantity_sold * unit_price) as total')
                ->value('total');

            DB::commit();

            return response()->json([
                'success' => true,
                'total' => $total,
                'message' => 'Item removed successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error removing item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sale items
     */
    public function getSaleItems($saleId)
    {
        try {
            $items = SaleItem::with('product')
                ->where('sale_id', $saleId)
                ->get();

            $total = $items->sum(function($item) {
                return $item->quantity_sold * $item->unit_price;
            });

            return response()->json([
                'success' => true,
                'items' => $items,
                'total' => $total
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment and complete sale
     */
    public function processPayment(Request $request)
    {
        try {
            DB::beginTransaction();

            $saleId = $request->input('sale_id');
            $paymentMethod = $request->input('payment_method');
            $amountTendered = $request->input('amount_tendered');
            $referenceNo = $request->input('reference_no');
            $customerName = $request->input('customer_name');
            $customerContact = $request->input('customer_contact');

            // Get sale with items
            $sale = Sale::with('items.product')->findOrFail($saleId);
            $total = $sale->items->sum(function($item) {
                return $item->quantity_sold * $item->unit_price;
            });

            // Validate payment based on method
            if ($paymentMethod === 'Cash') {
                if ($amountTendered < $total) {
                    throw new Exception('Amount tendered must be greater than or equal to total');
                }
                $changeGiven = $amountTendered - $total;
            } else {
                // GCash or Card - amount tendered should equal total
                if ($amountTendered != $total) {
                    throw new Exception('Amount tendered must equal total for ' . $paymentMethod);
                }
                if (empty($referenceNo)) {
                    throw new Exception('Reference number is required for ' . $paymentMethod);
                }
                $changeGiven = 0;
            }

            // Update customer details
            $sale->update([
                'customer_name' => $customerName,
                'customer_contact' => $customerContact
            ]);

            // Create payment record
            $payment = Payment::create([
                'sale_id' => $saleId,
                'payment_date' => now(),
                'payment_method' => $paymentMethod,
                'amount_tendered' => $amountTendered,
                'change_given' => $changeGiven,
                'reference_no' => $referenceNo
            ]);

            // Update product stock quantities
            foreach ($sale->items as $item) {
                $product = $item->product;
                $product->decrement('quantity_in_stock', $item->quantity_sold);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale->load('items.product', 'payment'),
                'change_given' => $changeGiven,
                'message' => 'Payment processed successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function downloadReceiptPDF(Sale $sale)
{
    $sale->load(['items.product', 'payment', 'user']);

    if (!$sale->payment) {
        abort(404, "Payment not found.");
    }

    return PDF::loadView('pos.receipt', compact('sale'))
            ->download("receipt-{$sale->id}.pdf");
}
}