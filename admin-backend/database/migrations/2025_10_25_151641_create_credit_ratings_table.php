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
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade'); // 商家ID
            $table->decimal('overall_score', 5, 2)->default(0); // 总体评分
            $table->decimal('service_score', 5, 2)->default(0); // 服务评分
            $table->decimal('quality_score', 5, 2)->default(0); // 质量评分
            $table->decimal('delivery_score', 5, 2)->default(0); // 配送评分
            $table->decimal('communication_score', 5, 2)->default(0); // 沟通评分
            $table->integer('total_reviews')->default(0); // 总评价数
            $table->integer('positive_reviews')->default(0); // 好评数
            $table->integer('neutral_reviews')->default(0); // 中评数
            $table->integer('negative_reviews')->default(0); // 差评数
            $table->string('rating_level')->default('C'); // 评级等级：A+, A, B+, B, C+, C, D
            $table->text('rating_summary')->nullable(); // 评级总结
            $table->json('improvement_suggestions')->nullable(); // 改进建议
            $table->json('rating_history')->nullable(); // 评分历史
            $table->timestamp('last_updated')->nullable(); // 最后更新时间
            $table->timestamps();
            
            $table->index(['merchant_id', 'overall_score']);
            $table->index(['rating_level', 'overall_score']);
            $table->index('last_updated');
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
