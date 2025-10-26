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
        Schema::create('platform_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2);
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('sales')->default(0);
            $table->integer('stock')->default(0);
            $table->string('sku')->unique();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
            $table->index(['brand', 'is_active']);
            $table->index(['rating', 'sales']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_products');
    }
};