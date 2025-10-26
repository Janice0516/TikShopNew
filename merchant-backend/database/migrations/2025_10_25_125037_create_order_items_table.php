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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('merchant_product_id');
            $table->string('product_name');
            $table->string('product_sku');
            $table->string('product_image')->nullable();
            
            // 价格信息
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            
            // 商品规格
            $table->json('product_specifications')->nullable();
            
            // 时间戳
            $table->timestamps();
            
            // 索引
            $table->index(['order_id']);
            $table->index(['merchant_product_id']);
            
            // 外键
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('merchant_product_id')->references('id')->on('merchant_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};