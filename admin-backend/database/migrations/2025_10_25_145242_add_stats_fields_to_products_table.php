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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('views_count')->default(0)->after('sort_order');
            $table->integer('sales_count')->default(0)->after('views_count');
            $table->decimal('rating', 3, 1)->default(0.0)->after('sales_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['views_count', 'sales_count', 'rating']);
        });
    }
};
