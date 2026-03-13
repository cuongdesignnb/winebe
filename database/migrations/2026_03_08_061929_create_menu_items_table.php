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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('item_type'); // custom_link, category, brand
            $table->string('title');
            $table->string('target_type')->nullable(); // model class
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('url')->nullable();
            $table->string('css_class')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('icon_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('level')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('open_in_new_tab')->default(false);
            $table->json('settings_json')->nullable();
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('menu_items')->nullOnDelete();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
