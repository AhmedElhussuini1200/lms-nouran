<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تتبع الدافع: الفاتورة باسم الطالب (المستفيد) + مين دفع فعلاً
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('paid_by')->nullable()->after('student_id')->references('id')->on('admins')->nullOnDelete();
            $table->string('payer_name')->nullable()->after('paid_by'); // اسم حر (كاش من ولي أمر غير مسجل...)
        });

        // موضوع السؤال (لملف المهارة مستقبلاً)
        Schema::table('questions', function (Blueprint $table) {
            $table->string('topic')->nullable()->after('question');
        });
        Schema::table('question_bank', function (Blueprint $table) {
            // topic موجود أصلاً — لا شيء
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['paid_by', 'payer_name']);
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('topic');
        });
    }
};
