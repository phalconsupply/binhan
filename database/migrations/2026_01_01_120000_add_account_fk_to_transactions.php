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
        // Add new columns to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('from_account_id')->nullable()->after('note')->comment('FK to accounts table');
            $table->unsignedBigInteger('to_account_id')->nullable()->after('from_account_id')->comment('FK to accounts table');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'completed'])
                ->default('completed')
                ->after('to_account_id')
                ->comment('Transaction status for approval workflow');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');

            // Add indexes
            $table->index('from_account_id');
            $table->index('to_account_id');
            $table->index('status');
            $table->index(['status', 'date']);

            // Add foreign keys (will be enabled after data migration)
            // Commented out for now - will enable in next migration after data migration
            // $table->foreign('from_account_id')->references('id')->on('accounts')->nullOnDelete();
            // $table->foreign('to_account_id')->references('id')->on('accounts')->nullOnDelete();
            // $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        // Keep old string-based account columns for backward compatibility during migration
        // Will be dropped in a future migration after data migration is complete
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop foreign keys if they exist
            // $table->dropForeign(['from_account_id']);
            // $table->dropForeign(['to_account_id']);
            // $table->dropForeign(['approved_by']);

            // Drop indexes
            $table->dropIndex(['from_account_id']);
            $table->dropIndex(['to_account_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'date']);

            // Drop columns
            $table->dropColumn([
                'from_account_id',
                'to_account_id',
                'status',
                'approved_by',
                'approved_at',
                'rejection_reason',
            ]);
        });
    }
};
