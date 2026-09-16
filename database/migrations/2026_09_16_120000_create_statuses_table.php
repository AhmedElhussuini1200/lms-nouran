<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الحالات الموحد (للتسليمات والنتائج والمدفوعات)
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // pending, submitted, under_review, graded, returned, paid, overdue
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('color')->default('info'); // info, warning, success, danger
            $table->string('scope')->default('submission'); // submission, exam, payment
            $table->timestamps();
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('student_id')->constrained('statuses')->nullOnDelete();
        });

        Schema::table('exam_results', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('student_id')->constrained('statuses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');
        });
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');
        });
        Schema::dropIfExists('statuses');
    }
};
