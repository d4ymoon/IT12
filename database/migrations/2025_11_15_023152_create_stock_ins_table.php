<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();

            $table->dateTime('stock_in_date');
            $table->enum('stock_in_type', ['PO-Based', 'Direct Purchase']);
            $table->string('reference_no', 100)->nullable();
            
            // Foreign keys - also use snake_case
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            
            $table->unsignedBigInteger('received_by_user_id');
            $table->foreign('received_by_user_id')->references('id')->on('users')->onDelete('restrict');
    
            // Add supplier_id if missing
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
    
            $table->string('status')->default('completed');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
