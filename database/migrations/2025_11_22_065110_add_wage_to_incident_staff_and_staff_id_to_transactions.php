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
        // Add wage_amount to incident_staff pivot table
        Schema::table('incident_staff', function (Blueprint $table) {
            $table->decimal('wage_amount', 10, 2)->nullable()->after('role')->comment('Tiền công nhân viên cho chuyến đi này');
        });

        // Add staff_id to transactions table for tracking staff wages
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('incident_id')->constrained()->nullOnDelete()->comment('Nhân viên nhận tiền (nếu là chi phí nhân sự)');
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incident_staff', function (Blueprint $table) {
            $table->dropColumn('wage_amount');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });
    }
};
