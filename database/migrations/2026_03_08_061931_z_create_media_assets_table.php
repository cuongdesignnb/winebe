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
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $table->string('disk')->default('local');
            $table->string('original_name');
            $table->string('original_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('checksum')->nullable();
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('ready');
            $table->json('meta_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
