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
        Schema::create('credit_rating_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_rating_id')->constrained()->onDelete('cascade'); // 信用评级ID
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null'); // 订单ID
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade'); // 客户ID
            $table->decimal('overall_score', 5, 2); // 总体评分
            $table->decimal('service_score', 5, 2); // 服务评分
            $table->decimal('quality_score', 5, 2); // 质量评分
            $table->decimal('delivery_score', 5, 2); // 配送评分
            $table->decimal('communication_score', 5, 2); // 沟通评分
            $table->string('review_type')->default('positive'); // 评价类型：positive, neutral, negative
            $table->text('review_content')->nullable(); // 评价内容
            $table->json('review_images')->nullable(); // 评价图片
            $table->boolean('is_anonymous')->default(false); // 是否匿名
            $table->boolean('is_verified')->default(false); // 是否已验证
            $table->foreignId('verified_by')->nullable()->constrained('admins')->onDelete('set null'); // 验证人
            $table->timestamp('verified_at')->nullable(); // 验证时间
            $table->text('admin_notes')->nullable(); // 管理员备注
            $table->timestamps();
            
            $table->index(['credit_rating_id', 'created_at']);
            $table->index(['customer_id', 'created_at']);
            $table->index(['review_type', 'created_at']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_rating_records');
    }
};
