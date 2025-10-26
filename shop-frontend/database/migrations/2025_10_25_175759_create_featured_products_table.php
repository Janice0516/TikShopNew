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
        Schema::create('featured_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['savings', 'top_deals']);
            $table->integer('sort_order')->default(0);
            $table->string('custom_title')->nullable();
            $table->decimal('custom_price', 10, 2)->nullable();
            $table->decimal('custom_original_price', 10, 2)->nullable();
            $table->decimal('custom_rating', 2, 1)->nullable();
            $table->integer('custom_sales_count')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['product_id', 'type']);
            $table->index(['type', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_products');
    }
};
