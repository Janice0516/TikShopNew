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
        Schema::create('fund_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name'); // 账户名称
            $table->string('account_type')->default('platform'); // 账户类型：platform, merchant, customer, system
            $table->string('account_number')->unique(); // 账户号码
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null'); // 账户所有者ID
            $table->string('owner_type')->nullable(); // 所有者类型：merchant, customer, admin
            $table->decimal('balance', 15, 2)->default(0); // 账户余额
            $table->decimal('frozen_amount', 15, 2)->default(0); // 冻结金额
            $table->decimal('available_balance', 15, 2)->default(0); // 可用余额
            $table->string('currency')->default('RM'); // 货币类型
            $table->string('status')->default('active'); // 状态：active, frozen, closed
            $table->text('description')->nullable(); // 账户描述
            $table->json('settings')->nullable(); // 账户设置
            $table->timestamp('last_transaction_at')->nullable(); // 最后交易时间
            $table->timestamps();
            
            $table->index(['account_type', 'status']);
            $table->index(['owner_id', 'owner_type']);
            $table->index('account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_accounts');
    }
};
