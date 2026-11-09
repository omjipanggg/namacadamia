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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->decimal('payment_amount', 15, 2);
            $table->decimal('received_amount', 15, 2);
            $table->decimal('change_amount', 15, 2);
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('paid_at');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
