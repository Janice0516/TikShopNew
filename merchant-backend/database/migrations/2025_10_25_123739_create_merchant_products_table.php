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
        Schema::create('merchant_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('platform_product_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->string('sku')->unique();
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('specifications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('merchant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('platform_product_id')->references('id')->on('platform_products')->onDelete('set null');
            
            $table->index(['merchant_id', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['brand', 'is_active']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_products');
    }
};