<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->enum('type', ['mcq', 'true_false', 'essay'])->default('mcq');
            $table->text('question');
            $table->json('options')->nullable(); // للاختيارات [a,b,c,d]
            $table->string('correct_answer')->nullable(); // رقم/نص الإجابة الصحيحة (null للمقالي)
            $table->decimal('marks', 8, 2)->default(1);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
