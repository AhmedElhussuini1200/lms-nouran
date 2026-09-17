<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // بنك الأسئلة المركزي
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->string('subject')->nullable();
            $table->enum('grade', ['1_secondary', '2_secondary', '3_secondary']);
            $table->enum('type', ['mcq', 'true_false', 'essay'])->default('mcq');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('topic')->nullable(); // درس/وحدة
            $table->text('question');
            $table->json('options')->nullable();
            $table->string('correct_answer')->nullable();
            $table->decimal('marks', 8, 2)->default(1);
            $table->timestamps();
        });

        // أكواد الخصم والإحالة
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('kind', ['percent', 'fixed'])->default('percent');
            $table->decimal('value', 8, 2);
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->date('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->foreignId('referred_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->integer('bonus_points')->default(20);
            $table->boolean('awarded')->default(false);
            $table->timestamps();
            $table->unique(['referrer_id', 'referred_id']);
        });

        // محفظة المدرس
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->unique()->references('id')->on('admins')->cascadeOnDelete();
            $table->decimal('balance', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->enum('kind', ['credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('question_bank');
    }
};
