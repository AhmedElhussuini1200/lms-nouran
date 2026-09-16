<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });

        // القوالب الافتراضية
        DB::table('whatsapp_templates')->insert([
            ['title' => 'تذكير بموعد حصة', 'body' => 'تذكير: لديك حصة اليوم، نرجو الالتزام بالموعد', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'تنبيه غياب', 'body' => 'تم تسجيل غياب اليوم، نرجو التواصل مع الإدارة', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'تذكير بفاتورة', 'body' => 'تذكير: يوجد مبلغ مستحق، نرجو سرعة السداد', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'تهنئة بتفوق', 'body' => 'مبارك التفوق! نتمنى دوام التميز', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
