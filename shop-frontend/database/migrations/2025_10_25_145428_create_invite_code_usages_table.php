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
        Schema::create('invite_code_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invite_code_id')->constrained()->onDelete('cascade'); // 邀请码ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 使用者ID
            $table->string('user_type')->default('merchant'); // 用户类型
            $table->string('ip_address')->nullable(); // IP地址
            $table->string('user_agent')->nullable(); // 用户代理
            $table->decimal('reward_amount', 10, 2)->nullable(); // 获得的奖励金额
            $table->string('reward_type')->nullable(); // 奖励类型
            $table->string('status')->default('success'); // 状态：success, failed, pending
            $table->text('notes')->nullable(); // 备注
            $table->timestamps();
            
            $table->index(['invite_code_id', 'user_id']);
            $table->index(['user_id', 'user_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invite_code_usages');
    }
};
