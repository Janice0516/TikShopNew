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
            $table->string('withdrawal_number')->unique(); // 提现单号
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade'); // 商家ID
            $table->decimal('amount', 10, 2); // 提现金额
            $table->decimal('fee', 10, 2)->default(0); // 手续费
            $table->decimal('actual_amount', 10, 2); // 实际到账金额
            $table->string('withdrawal_method')->default('bank'); // 提现方式：bank, alipay, wechat
            $table->string('account_name'); // 账户姓名
            $table->string('account_number'); // 账户号码
            $table->string('bank_name')->nullable(); // 银行名称
            $table->string('bank_branch')->nullable(); // 开户支行
            $table->string('status')->default('pending'); // 状态：pending, processing, completed, rejected, cancelled
            $table->text('rejection_reason')->nullable(); // 拒绝原因
            $table->foreignId('processed_by')->nullable()->constrained('admins')->onDelete('set null'); // 处理人
            $table->timestamp('processed_at')->nullable(); // 处理时间
            $table->text('admin_notes')->nullable(); // 管理员备注
            $table->text('merchant_notes')->nullable(); // 商家备注
            $table->json('attachments')->nullable(); // 附件
            $table->timestamps();
            
            $table->index(['merchant_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('withdrawal_number');
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
