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
        Schema::table('loan_profiles', function (Blueprint $table) {
            $table->boolean('first_period_interest_only')->default(false)->after('payment_day')
                ->comment('Kỳ đầu tiên chỉ trả lãi, không trả gốc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_profiles', function (Blueprint $table) {
            $table->dropColumn('first_period_interest_only');
        });
    }
};
