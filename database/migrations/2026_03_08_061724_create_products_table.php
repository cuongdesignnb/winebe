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
        Schema::disableForeignKeyConstraints();
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('distillery_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('product_type')->nullable(); // whisky, wine, cognac
            $table->string('sale_mode')->default('online_checkout'); // online_checkout, inquiry_only
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->text('short_desc')->nullable();
            $table->longText('long_desc_html')->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->nullOnDelete();
            $table->foreign('distillery_id')->references('id')->on('distilleries')->nullOnDelete();
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
