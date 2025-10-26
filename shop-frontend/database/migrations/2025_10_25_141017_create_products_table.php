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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('status')->default('active'); // active, inactive, draft
            $table->json('images')->nullable(); // 商品图片数组
            $table->json('variants')->nullable(); // 商品变体（颜色、尺寸等）
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('merchant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('brand')->nullable();
            $table->json('specifications')->nullable(); // 商品规格
            $table->boolean('is_featured')->default(false); // 是否推荐
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['status', 'category_id']);
            $table->index(['merchant_id', 'status']);
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
