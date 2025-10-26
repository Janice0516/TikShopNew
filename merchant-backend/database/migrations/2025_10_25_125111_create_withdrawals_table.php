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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->string('withdrawal_number')->unique();
            
            // 提现金额
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('actual_amount', 10, 2);
            
            // 提现方式
            $table->enum('method', ['bank_transfer', 'alipay', 'wechat', 'paypal'])
                  ->default('bank_transfer');
            
            // 收款信息
            $table->json('payment_info'); // 银行信息、支付宝账号等
            
            // 状态
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])
                  ->default('pending');
            
            // 处理信息
            $table->text('admin_notes')->nullable();
            $table->text('failure_reason')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            // 时间戳
            $table->timestamps();
            
            // 索引
            $table->index(['merchant_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['withdrawal_number']);
            
            // 外键
            $table->foreign('merchant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};