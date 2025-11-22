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
        Schema::table('transactions', function (Blueprint $table) {
            // Add transaction_category to distinguish types
            $table->string('transaction_category', 50)->nullable()->after('category')->index();
            
            // Add soft delete and audit trail support
            $table->boolean('is_active')->default(true)->after('transaction_category')->index();
            $table->foreignId('replaced_by')->nullable()->after('is_active')->constrained('transactions')->onDelete('set null');
            $table->timestamp('edited_at')->nullable()->after('replaced_by');
            $table->foreignId('edited_by')->nullable()->after('edited_at')->constrained('users')->onDelete('set null');
        });
        
        // Add comment for clarity
        DB::statement("ALTER TABLE transactions MODIFY COLUMN transaction_category VARCHAR(50) 
            COMMENT 'thu_chinh, chi_chinh, tien_cong_lai_xe, tien_cong_nvyt, hoa_hong, bao_tri, dich_vu_bo_sung, chi_phi_bo_sung'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['replaced_by']);
            $table->dropForeign(['edited_by']);
            $table->dropColumn(['transaction_category', 'is_active', 'replaced_by', 'edited_at', 'edited_by']);
        });
    }
};
