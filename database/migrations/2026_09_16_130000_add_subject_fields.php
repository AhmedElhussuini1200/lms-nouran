<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // مادة المدرس + مادة المحتوى (ديناميكية تعدد المدرسين: فيزياء/كيمياء/أحياء...)
        Schema::table('admins', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('grade');
        });

        foreach (['courses', 'videos', 'assignments', 'exams'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->string('subject')->nullable()->after('grade');
            });
        }
    }

    public function down(): void
    {
        foreach (['courses', 'videos', 'assignments', 'exams'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn('subject');
            });
        }
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
    }
};
