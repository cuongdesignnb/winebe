<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique()->nullable();
            $table->decimal('abv_percent', 5, 2)->nullable();
            $table->integer('volume_ml')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('compare_at_price', 15, 2)->nullable();
            $table->boolean('can_checkout')->default(true);
            $table->string('stock_policy')->default('track'); // track, always_in_stock
            $table->string('barcode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
