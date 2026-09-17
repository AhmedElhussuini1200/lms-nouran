<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // هوية المدرس: المشروع كله يتلون بيها لما يدخل
            $table->string('brand_name')->nullable()->after('subject');
            $table->string('brand_logo')->nullable()->after('brand_name');
            $table->string('brand_primary', 7)->nullable()->after('brand_logo');
            $table->string('brand_secondary', 7)->nullable()->after('brand_primary');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['brand_name', 'brand_logo', 'brand_primary', 'brand_secondary']);
        });
    }
};
