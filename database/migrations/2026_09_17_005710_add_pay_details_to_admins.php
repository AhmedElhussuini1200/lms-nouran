<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // بيانات الدفع للمدرس: أرقام المحافظ اللي الطالب يحول عليها
            $table->text('pay_details')->nullable()->after('pay_methods');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('pay_details');
        });
    }
};
