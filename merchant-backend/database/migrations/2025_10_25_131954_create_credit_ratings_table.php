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
        Schema::create('credit_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            
            // 信用评分
            $table->decimal('overall_score', 3, 1)->default(0.0); // 总分 (0-100)
            $table->decimal('service_score', 3, 1)->default(0.0); // 服务评分
            $table->decimal('quality_score', 3, 1)->default(0.0); // 商品质量评分
            $table->decimal('shipping_score', 3, 1)->default(0.0); // 配送评分
            $table->decimal('communication_score', 3, 1)->default(0.0); // 沟通评分
            
            // 评级等级
            $table->string('rating_level', 20)->default('C'); // A+, A, B+, B, C+, C, D
            $table->string('rating_text', 50)->default('普通'); // 优秀, 良好, 普通, 较差
            
            // 统计数据
            $table->integer('total_reviews')->default(0); // 总评价数
            $table->integer('positive_reviews')->default(0); // 好评数
            $table->integer('neutral_reviews')->default(0); // 中评数
            $table->integer('negative_reviews')->default(0); // 差评数
            
            // 订单相关
            $table->integer('total_orders')->default(0); // 总订单数
            $table->integer('completed_orders')->default(0); // 完成订单数
            $table->integer('cancelled_orders')->default(0); // 取消订单数
            $table->integer('refund_orders')->default(0); // 退款订单数
            
            // 时间相关
            $table->integer('avg_response_time')->default(0); // 平均响应时间(分钟)
            $table->integer('avg_shipping_time')->default(0); // 平均发货时间(小时)
            $table->integer('avg_delivery_time')->default(0); // 平均配送时间(天)
            
            // 财务相关
            $table->decimal('total_revenue', 15, 2)->default(0.00); // 总收入
            $table->integer('payment_delays')->default(0); // 支付延迟次数
            $table->integer('refund_rate')->default(0); // 退款率(百分比)
            
            // 平台评估
            $table->boolean('is_verified')->default(false); // 是否认证商家
            $table->boolean('is_premium')->default(false); // 是否高级商家
            $table->json('badges')->nullable(); // 徽章列表
            
            // 时间戳
            $table->timestamps();
            
            // 索引
            $table->index(['merchant_id']);
            $table->index(['rating_level']);
            $table->index(['overall_score']);
            $table->index(['is_verified']);
            $table->index(['is_premium']);
            
            // 外键
            $table->foreign('merchant_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_ratings');
    }
};