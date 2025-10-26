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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('company_name')->nullable()->after('phone');
            $table->text('address')->nullable()->after('company_name');
            $table->string('business_license')->nullable()->after('address');
            $table->string('tax_number')->nullable()->after('business_license');
            $table->string('contact_person')->nullable()->after('tax_number');
            $table->string('contact_phone')->nullable()->after('contact_person');
            $table->string('status')->default('active')->after('contact_phone');
            $table->boolean('is_verified')->default(false)->after('status');
            $table->decimal('balance', 10, 2)->default(0)->after('is_verified');
            $table->decimal('frozen_amount', 10, 2)->default(0)->after('balance');
            $table->timestamp('last_login_at')->nullable()->after('frozen_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'company_name',
                'address',
                'business_license',
                'tax_number',
                'contact_person',
                'contact_phone',
                'status',
                'is_verified',
                'balance',
                'frozen_amount',
                'last_login_at',
            ]);
        });
    }
};
