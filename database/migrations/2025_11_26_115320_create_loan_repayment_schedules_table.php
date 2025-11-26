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
        Schema::create('loan_repayment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loan_profiles')->onDelete('cascade');
            $table->integer('period_no')->comment('Kỳ số');
            $table->date('due_date')->comment('Ngày phải trả');
            $table->decimal('principal', 15, 2)->comment('Số tiền gốc kỳ này');
            $table->decimal('interest', 15, 2)->comment('Số tiền lãi kỳ này');
            $table->decimal('total', 15, 2)->comment('Tổng phải trả');
            $table->decimal('interest_rate', 5, 2)->comment('Lãi suất áp dụng (%/năm)');
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->date('paid_date')->nullable()->comment('Ngày thực tế đã trả');
            $table->decimal('paid_amount', 15, 2)->nullable()->comment('Số tiền thực tế đã trả');
            $table->integer('overdue_days')->default(0)->comment('Số ngày quá hạn');
            $table->decimal('late_fee', 15, 2)->default(0)->comment('Phí phạt trả chậm');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['loan_id', 'period_no']);
            $table->index(['due_date', 'status']);
            $table->unique(['loan_id', 'period_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_repayment_schedules');
    }
};
