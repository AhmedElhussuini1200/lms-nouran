<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // سعر الحصة (للحسابات)
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('scheduled_at');
        });

        // المدفوعات الشهرية
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('admins')->cascadeOnDelete();
            $table->string('month'); // 2026-09
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->string('method')->nullable(); // cash, vodafone, instapay
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
