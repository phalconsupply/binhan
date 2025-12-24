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
        // Add 'vay_cong_ty' (borrow from company) and 'tra_cong_ty' (return to company) to transaction types
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `type` ENUM('thu', 'chi', 'du_kien_chi', 'nop_quy', 'vay_cong_ty', 'tra_cong_ty') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'vay_cong_ty' and 'tra_cong_ty' values from ENUM
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `type` ENUM('thu', 'chi', 'du_kien_chi', 'nop_quy') NOT NULL");
    }
};
