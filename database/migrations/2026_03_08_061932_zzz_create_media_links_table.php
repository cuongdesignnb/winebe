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
        Schema::create('media_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('media_assets')->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('collection_name')->default('default');
            $table->string('field_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('transformation_json')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_links');
    }
};
