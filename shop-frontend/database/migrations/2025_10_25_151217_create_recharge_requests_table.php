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
        Schema::create('recharge_requests', function (Blueprint $table) {
            $table->id();
            $table->string('recharge_number')->unique(); // 充值单号
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 用户ID
            $table->string('user_type')->default('merchant'); // 用户类型：merchant, customer
            $table->decimal('amount', 10, 2); // 充值金额
            $table->string('payment_method')->default('bank'); // 支付方式：bank, alipay, wechat, online
            $table->string('payment_account')->nullable(); // 支付账户
            $table->string('receipt_account')->nullable(); // 收款账户
            $table->string('bank_name')->nullable(); // 银行名称
            $table->string('bank_branch')->nullable(); // 开户支行
            $table->string('transaction_id')->nullable(); // 第三方交易ID
            $table->string('status')->default('pending'); // 状态：pending, processing, completed, rejected, cancelled
            $table->text('rejection_reason')->nullable(); // 拒绝原因
            $table->foreignId('processed_by')->nullable()->constrained('admins')->onDelete('set null'); // 处理人
            $table->timestamp('processed_at')->nullable(); // 处理时间
            $table->text('admin_notes')->nullable(); // 管理员备注
            $table->text('user_notes')->nullable(); // 用户备注
            $table->json('attachments')->nullable(); // 附件（转账凭证等）
            $table->json('payment_proof')->nullable(); // 支付凭证
            $table->timestamps();
            
            $table->index(['user_id', 'user_type']);
            $table->index(['status', 'created_at']);
            $table->index('recharge_number');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharge_requests');
    }
};
