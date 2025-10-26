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
        Schema::create('invite_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 邀请码
            $table->string('type')->default('merchant'); // 类型：merchant, customer, admin
            $table->string('name')->nullable(); // 邀请码名称
            $table->text('description')->nullable(); // 描述
            $table->foreignId('creator_id')->nullable()->constrained('admins')->onDelete('set null'); // 创建者
            $table->foreignId('merchant_id')->nullable()->constrained('users')->onDelete('set null'); // 关联商家
            $table->integer('max_uses')->nullable(); // 最大使用次数
            $table->integer('used_count')->default(0); // 已使用次数
            $table->decimal('reward_amount', 10, 2)->nullable(); // 奖励金额
            $table->string('reward_type')->default('cash'); // 奖励类型：cash, points, discount
            $table->decimal('discount_percent', 5, 2)->nullable(); // 折扣百分比
            $table->decimal('discount_amount', 10, 2)->nullable(); // 折扣金额
            $table->date('start_date')->nullable(); // 开始日期
            $table->date('end_date')->nullable(); // 结束日期
            $table->boolean('is_active')->default(true); // 是否激活
            $table->json('conditions')->nullable(); // 使用条件
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['merchant_id', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invite_codes');
    }
};
