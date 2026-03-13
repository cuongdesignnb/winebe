<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_published');
            $table->index('is_featured');
            $table->index('sale_mode');
            $table->index('brand_id');
            $table->index('country_id');
        });

        Schema::table('taxa', function (Blueprint $table) {
            $table->index('taxon_type');
            $table->index('parent_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('customer_email');
            $table->index(['created_at', 'status']);
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_published']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['sale_mode']);
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['country_id']);
        });

        Schema::table('taxa', function (Blueprint $table) {
            $table->dropIndex(['taxon_type']);
            $table->dropIndex(['parent_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['customer_email']);
            $table->dropIndex(['created_at', 'status']);
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['email']);
        });
    }
};
