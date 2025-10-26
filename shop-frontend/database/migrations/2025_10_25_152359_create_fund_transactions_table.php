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
        Schema::create('fund_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // 交易单号
            $table->foreignId('from_account_id')->nullable()->constrained('fund_accounts')->onDelete('set null'); // 转出账户
            $table->foreignId('to_account_id')->nullable()->constrained('fund_accounts')->onDelete('set null'); // 转入账户
            $table->decimal('amount', 15, 2); // 交易金额
            $table->string('transaction_type'); // 交易类型：recharge, withdrawal, payment, refund, commission, fee, transfer
            $table->string('status')->default('pending'); // 状态：pending, processing, completed, failed, cancelled
            $table->string('currency')->default('RM'); // 货币类型
            $table->text('description')->nullable(); // 交易描述
            $table->string('reference_type')->nullable(); // 关联类型：order, withdrawal, recharge
            $table->unsignedBigInteger('reference_id')->nullable(); // 关联ID
            $table->foreignId('operator_id')->nullable()->constrained('admins')->onDelete('set null'); // 操作人
            $table->string('operator_type')->default('admin'); // 操作人类型：admin, system, user
            $table->json('metadata')->nullable(); // 额外数据
            $table->timestamp('processed_at')->nullable(); // 处理时间
            $table->timestamps();
            
            $table->index(['from_account_id', 'created_at']);
            $table->index(['to_account_id', 'created_at']);
            $table->index(['transaction_type', 'status']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('transaction_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_transactions');
    }
};
