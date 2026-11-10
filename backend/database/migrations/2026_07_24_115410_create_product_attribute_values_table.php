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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->string('abbr')->nullable();
            $table->unsignedBigInteger('product_attribute_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_attribute_id', 'abbr'], 'pav_abbr_unique');
            $table->unique(['product_attribute_id', 'value'], 'pav_value_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
