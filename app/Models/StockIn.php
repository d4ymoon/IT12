<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $fillable = [
        'stock_in_date',
        'stock_in_type', 
        'reference_no',
        'purchase_order_id',
        'received_by_user_id',
        'supplier_id',
        'status'
    ];

    protected $casts = [
        'stock_in_date' => 'datetime'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_user_id')->withDefault([
            'f_name' => 'Unknown',
            'm_name' => null,
            'l_name' => 'User',
            'full_name' => 'Unknown User'
        ]);
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(StockInItem::class, 'stock_in_id');
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->count();
    }

    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity_received');
    }

    public function getTotalCostAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->quantity_received * $item->actual_unit_cost;
        });
    }

    
}
