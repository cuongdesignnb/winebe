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
        Schema::create('cms_block_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('cms_page_blocks')->cascadeOnDelete();
            $table->string('field_key');
            $table->string('field_type')->nullable();
            $table->longText('field_value')->nullable();
            $table->json('field_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_block_fields');
    }
};
