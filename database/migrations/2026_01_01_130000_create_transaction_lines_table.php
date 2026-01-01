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
        Schema::create('transaction_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->decimal('debit', 15, 2)->default(0)->comment('Nợ (Debit) - Tăng tài sản/chi phí, giảm nợ/doanh thu');
            $table->decimal('credit', 15, 2)->default(0)->comment('Có (Credit) - Giảm tài sản/chi phí, tăng nợ/doanh thu');
            $table->text('description')->nullable()->comment('Mô tả chi tiết dòng ghi sổ');
            $table->integer('line_number')->default(1)->comment('Thứ tự dòng trong giao dịch');
            $table->timestamps();

            // Indexes
            $table->index('transaction_id');
            $table->index('account_id');
            $table->index(['transaction_id', 'line_number']);
            $table->index(['account_id', 'created_at']);
        });

        // Add CHECK constraints using raw SQL (Laravel 10 compatible)
        DB::statement('ALTER TABLE transaction_lines ADD CONSTRAINT check_debit_non_negative CHECK (debit >= 0)');
        DB::statement('ALTER TABLE transaction_lines ADD CONSTRAINT check_credit_non_negative CHECK (credit >= 0)');
        DB::statement('ALTER TABLE transaction_lines ADD CONSTRAINT check_debit_or_credit CHECK (NOT (debit > 0 AND credit > 0))');

        // Add comment to table
        DB::statement("ALTER TABLE `transaction_lines` COMMENT = 'Double-entry transaction lines (journal entries)'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_lines');
    }
};
