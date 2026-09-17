<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) تطوير الامتحانات
        Schema::table('exams', function (Blueprint $table) {
            $table->boolean('shuffle_questions')->default(false)->after('total_marks');
            $table->unsignedInteger('max_attempts')->default(1)->after('shuffle_questions');
            $table->decimal('passing_marks', 8, 2)->nullable()->after('max_attempts');
            $table->dateTime('starts_at')->nullable()->after('passing_marks');
            $table->dateTime('ends_at')->nullable()->after('starts_at');
            $table->boolean('anti_cheat')->default(false)->after('ends_at');
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->unsignedInteger('attempt_no')->default(1);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->unsignedInteger('tab_switches')->default(0);
            $table->timestamps();
            $table->unique(['exam_id', 'student_id', 'attempt_no']);
        });

        // 2) تقدم الفيديو + تعليقات
        Schema::create('video_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('videos')->cascadeOnDelete();
            $table->foreignId('student_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->unsignedInteger('watched_seconds')->default(0);
            $table->unsignedTinyInteger('percent')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->unique(['video_id', 'student_id']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('commentable'); // video / course
            $table->foreignId('admin_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        // 3) شهادات + نقاط
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('pdf_path')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('student_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->integer('points')->default(0);
            $table->string('reason')->nullable();
            $table->nullableMorphs('source');
            $table->timestamps();
        });

        // 5) مدفوعات أونلاين
        Schema::table('payments', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('method'); // cash|paymob|fawry
            $table->string('transaction_ref')->nullable()->after('provider');
            $table->string('receipt_path')->nullable()->after('transaction_ref');
            $table->dateTime('paid_at')->nullable()->after('receipt_path');
        });

        // 6+8) حصص: QR + لايف
        Schema::table('courses', function (Blueprint $table) {
            $table->string('qr_token')->nullable()->unique()->after('price');
            $table->boolean('is_live')->default(false)->after('qr_token');
            $table->string('live_url')->nullable()->after('is_live');
            $table->dateTime('live_started_at')->nullable()->after('live_url');
        });

        Schema::create('live_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('admin_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();
        });

        // 7) سجل AI
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->string('kind'); // ask|generate_quiz|grade_essay
            $table->text('prompt');
            $table->longText('response')->nullable();
            $table->timestamps();
        });

        // 9) استلام واجبات واتساب
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->string('source')->default('web')->after('id'); // web|whatsapp
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
        Schema::dropIfExists('live_messages');
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'is_live', 'live_url', 'live_started_at']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['provider', 'transaction_ref', 'receipt_path', 'paid_at']);
        });
        Schema::dropIfExists('student_points');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('video_progresses');
        Schema::dropIfExists('exam_attempts');
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropColumn(['source']);
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['shuffle_questions', 'max_attempts', 'passing_marks', 'starts_at', 'ends_at', 'anti_cheat']);
        });
    }
};
