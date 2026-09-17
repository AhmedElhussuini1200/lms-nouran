<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تسجيل الطالب مع مدرسين (مواد) — عند الدخول يختار هيحضر عند مين
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->foreignId('teacher_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
