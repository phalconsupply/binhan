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
        Schema::table('transactions', function (Blueprint $table) {
            // Soft delete
            $table->softDeletes();
            
            // Transaction lifecycle
            $table->enum('lifecycle_status', [
                'draft',        // Nháp, chưa áp dụng
                'active',       // Đang hoạt động
                'reversed',     // Đã đảo ngược
                'replaced',     // Đã thay thế
                'cancelled'     // Đã hủy
            ])->default('active')->after('status');
            
            // Reversal tracking
            $table->foreignId('reversed_by_transaction_id')->nullable()
                ->constrained('transactions')
                ->nullOnDelete()
                ->comment('ID của giao dịch đảo ngược (reversal transaction)');
            
            $table->foreignId('reverses_transaction_id')->nullable()
                ->constrained('transactions')
                ->nullOnDelete()
                ->comment('ID của giao dịch bị đảo ngược');
            
            // Modification tracking
            $table->text('modification_reason')->nullable()
                ->comment('Lý do sửa đổi/hủy/đảo ngược');
            
            $table->foreignId('modified_by')->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Người thực hiện thay đổi');
            
            $table->timestamp('modified_at')->nullable()
                ->comment('Thời điểm thay đổi');
            
            // Approval workflow (optional) - approved_by already exists, just add approved_at
            if (!Schema::hasColumn('transactions', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            
            // Lock transaction (prevent modification)
            $table->boolean('is_locked')->default(false)
                ->comment('Khóa giao dịch, không cho sửa/xóa');
            
            $table->timestamp('locked_at')->nullable();
            
            $table->foreignId('locked_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            
            // Indexes
            $table->index('lifecycle_status');
            $table->index('reversed_by_transaction_id');
            $table->index('reverses_transaction_id');
            $table->index(['deleted_at', 'lifecycle_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
            
            // Drop foreign keys first
            if (Schema::hasColumn('transactions', 'reversed_by_transaction_id')) {
                $table->dropForeign(['reversed_by_transaction_id']);
            }
            if (Schema::hasColumn('transactions', 'reverses_transaction_id')) {
                $table->dropForeign(['reverses_transaction_id']);
            }
            if (Schema::hasColumn('transactions', 'modified_by')) {
                $table->dropForeign(['modified_by']);
            }
            if (Schema::hasColumn('transactions', 'locked_by')) {
                $table->dropForeign(['locked_by']);
            }
            
            $columns = [
                'lifecycle_status',
                'reversed_by_transaction_id',
                'reverses_transaction_id',
                'modification_reason',
                'modified_by',
                'modified_at',
                'is_locked',
                'locked_at',
                'locked_by',
            ];
            
            if (Schema::hasColumn('transactions', 'approved_at')) {
                $columns[] = 'approved_at';
            }
            
            $table->dropColumn($columns);
        });
    }
};
