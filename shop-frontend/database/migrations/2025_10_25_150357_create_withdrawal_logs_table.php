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
        Schema::create('withdrawal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('withdrawal_id')->constrained()->onDelete('cascade'); // 提现记录ID
            $table->string('action'); // 操作：created, processing, completed, rejected, cancelled
            $table->string('old_status')->nullable(); // 原状态
            $table->string('new_status')->nullable(); // 新状态
            $table->text('description')->nullable(); // 操作描述
            $table->foreignId('operator_id')->nullable()->constrained('admins')->onDelete('set null'); // 操作人
            $table->string('operator_type')->default('admin'); // 操作人类型：admin, merchant, system
            $table->json('data')->nullable(); // 额外数据
            $table->timestamps();
            
            $table->index(['withdrawal_id', 'created_at']);
            $table->index(['operator_id', 'operator_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_logs');
    }
};
