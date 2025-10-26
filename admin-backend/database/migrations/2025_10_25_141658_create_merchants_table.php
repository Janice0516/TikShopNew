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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('merchant_name');
            $table->string('username')->unique();
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('shop_name');
            $table->string('invite_code')->unique();
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->decimal('balance', 15, 2)->default(0); // 账户余额
            $table->decimal('frozen_amount', 15, 2)->default(0); // 冻结金额
            $table->json('settings')->nullable(); // 商家设置
            $table->json('business_info')->nullable(); // 营业执照等信息
            $table->timestamp('verified_at')->nullable(); // 认证时间
            $table->timestamp('last_login_at')->nullable(); // 最后登录时间
            $table->timestamps();
            
            $table->index(['status', 'verified_at']);
            $table->index('invite_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
