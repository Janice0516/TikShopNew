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
        Schema::create('finance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->string('transaction_id')->unique();
            
            // 交易类型
            $table->enum('type', ['income', 'expense', 'withdrawal', 'refund', 'commission', 'bonus'])
                  ->default('income');
            
            // 金额信息
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            
            // 关联信息
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('withdrawal_id')->nullable();
            
            // 描述信息
            $table->string('description');
            $table->text('notes')->nullable();
            
            // 状态
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])
                  ->default('completed');
            
            // 时间戳
            $table->timestamps();
            
            // 索引
            $table->index(['merchant_id', 'type']);
            $table->index(['merchant_id', 'created_at']);
            $table->index(['order_id']);
            $table->index(['withdrawal_id']);
            $table->index(['transaction_id']);
            
            // 外键
            $table->foreign('merchant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_records');
    }
};