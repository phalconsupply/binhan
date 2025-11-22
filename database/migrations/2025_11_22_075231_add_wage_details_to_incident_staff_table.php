<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incident_staff', function (Blueprint $table) {
            // Add wage_details JSON column to store multiple wage types
            $table->json('wage_details')->nullable()->after('wage_amount')->comment('Chi tiết các loại tiền công (công, thưởng, hoa hồng, tip...)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incident_staff', function (Blueprint $table) {
            $table->dropColumn('wage_details');
        });
    }
};
