<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->unsignedInteger('attempt_no')->default(1)->after('student_id');
        });
    }

    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropColumn('attempt_no');
        });
    }
};
