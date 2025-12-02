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
        // Modify ENUM column to add 'nop_quy' value
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `type` ENUM('thu', 'chi', 'du_kien_chi', 'nop_quy') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'nop_quy' value from ENUM
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `type` ENUM('thu', 'chi', 'du_kien_chi') NOT NULL");
    }
};
