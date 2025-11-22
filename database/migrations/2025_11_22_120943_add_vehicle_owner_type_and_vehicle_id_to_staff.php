<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add vehicle_owner to staff_type enum
        DB::statement("ALTER TABLE staff MODIFY COLUMN staff_type ENUM('medical_staff', 'driver', 'manager', 'investor', 'admin', 'vehicle_owner') NOT NULL DEFAULT 'medical_staff'");
        
        // Add vehicle_id foreign key
        Schema::table('staff', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->after('equity_percentage')->constrained('vehicles')->onDelete('set null')->comment('Xe của chủ xe - chỉ áp dụng cho loại chủ xe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn('vehicle_id');
        });
        
        // Remove vehicle_owner from enum
        DB::statement("ALTER TABLE staff MODIFY COLUMN staff_type ENUM('medical_staff', 'driver', 'manager', 'investor', 'admin') NOT NULL DEFAULT 'medical_staff'");
    }
};
