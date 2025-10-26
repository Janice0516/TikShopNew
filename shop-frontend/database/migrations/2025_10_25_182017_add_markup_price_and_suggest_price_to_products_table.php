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
            $table->decimal('markup_price', 10, 2)->nullable()->after('cost_price')->comment('商家设定的销售价格（商城端显示）');
            $table->decimal('suggest_price', 10, 2)->nullable()->after('markup_price')->comment('管理后台建议售价（仅商家后台显示）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['markup_price', 'suggest_price']);
        });
    }
};
