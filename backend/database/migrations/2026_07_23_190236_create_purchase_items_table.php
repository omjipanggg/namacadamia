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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_sku_id');
            $table->unique([
                'purchase_id',
                'product_sku_id',
            ]);
            $table->unsignedBigInteger('quantity')->default(0);
            $table->unsignedBigInteger('measure_unit_id')->nullable();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('received_quantity')->default(0);
            $table->unsignedBigInteger('received_measure_unit_id')->nullable();
            $table->unsignedBigInteger('received_subtotal')->default(0);
            $table->unsignedBigInteger('cost_price')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
