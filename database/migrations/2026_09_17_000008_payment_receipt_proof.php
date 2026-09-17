<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // إثبات التحويل + اعتماده من المدرس/الإدارة
            $table->string('receipt_image')->nullable()->after('receipt_path');
            $table->decimal('unverified_amount', 10, 2)->default(0)->after('receipt_image');
            $table->boolean('receipt_verified')->default(false)->after('unverified_amount');
            $table->foreignId('verified_by')->nullable()->after('receipt_verified')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['receipt_image', 'unverified_amount', 'receipt_verified', 'verified_by']);
        });
    }
};
