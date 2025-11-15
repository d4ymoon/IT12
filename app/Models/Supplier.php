<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'contactNO',
        'address',
        'is_active',
        'date_disabled',
        'disabled_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_disabled' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function disabledBy()
    {
        return $this->belongsTo(User::class, 'disabled_by_user_id')->withDefault([
            'full_name' => 'System'
        ]);
    }

    // Relationship with products (many-to-many through product_suppliers)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_suppliers')
                    ->withPivot('default_unit_cost')
                    ->withTimestamps();
    }

    // Relationship with purchase orders
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Check if supplier has associated products
    public function hasProducts()
    {
        return $this->products()->exists();
    }

    // Check if supplier has associated purchase orders
    public function hasPurchaseOrders()
    {
        return $this->purchaseOrders()->exists();
    }

    // Check if supplier can be archived
    // Only prevent archiving if there are active purchase orders
    public function canBeArchived()
    {
        // Only prevent if there are active purchase orders that might reference this supplier
        return !$this->hasActivePurchaseOrders();
    }

    // Check if supplier has active purchase orders (not completed/cancelled)
    public function hasActivePurchaseOrders()
    {
        return $this->purchaseOrders()
                    ->whereNotIn('status', ['Completed', 'Cancelled', 'Received'])
                    ->exists();
    }

    // Get active purchase orders count
    public function getActivePurchaseOrdersCountAttribute()
    {
        return $this->purchaseOrders()
                    ->whereNotIn('status', ['Completed', 'Cancelled', 'Received'])
                    ->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_active', false);
    }
}