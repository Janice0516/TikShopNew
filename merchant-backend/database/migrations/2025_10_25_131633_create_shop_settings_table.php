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
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            
            // 基本信息
            $table->string('shop_name');
            $table->string('shop_slug')->unique();
            $table->text('shop_description')->nullable();
            $table->string('shop_logo')->nullable();
            $table->string('shop_banner')->nullable();
            
            // 联系信息
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_state')->nullable();
            $table->string('contact_country')->nullable();
            $table->string('contact_zip')->nullable();
            
            // 社交媒体
            $table->string('website_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('youtube_url')->nullable();
            
            // 营业设置
            $table->json('business_hours')->nullable(); // 营业时间
            $table->string('timezone')->default('Asia/Kuala_Lumpur');
            $table->string('currency')->default('MYR');
            $table->string('language')->default('zh');
            
            // 配送设置
            $table->decimal('free_shipping_threshold', 10, 2)->nullable();
            $table->decimal('default_shipping_fee', 10, 2)->default(0);
            $table->json('shipping_zones')->nullable(); // 配送区域设置
            
            // 政策设置
            $table->text('return_policy')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->text('terms_of_service')->nullable();
            
            // 状态设置
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_reviews')->default(true);
            $table->boolean('show_contact_info')->default(true);
            
            // SEO设置
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // 时间戳
            $table->timestamps();
            
            // 索引
            $table->index(['merchant_id']);
            $table->index(['shop_slug']);
            $table->index(['is_active']);
            $table->index(['is_featured']);
            
            // 外键
            $table->foreign('merchant_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};