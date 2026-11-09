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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_location_id');
            $table->unsignedBigInteger('product_sku_id');
            $table->unique(['stock_location_id', 'product_sku_id']);
            $table->integer('quantity')->default(0);
            $table->integer('quantity_reserved')->default(0);
            $table->integer('quantity_minimum')->default(1);
            $table->integer('quantity_maximum')->default(999);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
