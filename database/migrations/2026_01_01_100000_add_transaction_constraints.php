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
        // Add CHECK constraint for amount must be positive
        DB::statement('ALTER TABLE transactions ADD CONSTRAINT check_amount_positive CHECK (amount > 0)');

        // Add UNIQUE constraint on transaction code
        Schema::table('transactions', function (Blueprint $table) {
            $table->unique('code', 'unique_transaction_code');
        });

        // Add index on account columns for better query performance
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('from_account');
            $table->index('to_account');
            $table->index(['from_account', 'date']);
            $table->index(['to_account', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop CHECK constraint
        DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS check_amount_positive');

        // Drop UNIQUE constraint
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('unique_transaction_code');
        });

        // Drop indexes
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['from_account']);
            $table->dropIndex(['to_account']);
            $table->dropIndex(['from_account', 'date']);
            $table->dropIndex(['to_account', 'date']);
        });
    }
};
