<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // كل حصة لها واجبها وامتحانها
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('teacher_id')->constrained('courses')->nullOnDelete();
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('teacher_id')->constrained('courses')->nullOnDelete();
        });

        // باقة المدرس الشهرية: عدد حصص × سعر الحصة + طرق الدفع المتاحة
        Schema::table('admins', function (Blueprint $table) {
            $table->unsignedInteger('monthly_classes')->default(8)->after('brand_secondary');
            $table->decimal('price_per_class', 10, 2)->default(0)->after('monthly_classes');
            $table->string('pay_methods')->default('cash')->after('price_per_class'); // cash,vodafone,instapay,card
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['monthly_classes', 'price_per_class', 'pay_methods']);
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_id');
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_id');
        });
    }
};
