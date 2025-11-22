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
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // Số tiền ứng
            $table->decimal('from_earnings', 15, 2)->default(0); // Số tiền trừ từ thu nhập hiện có
            $table->decimal('from_company', 15, 2)->default(0); // Số tiền công ty bù (tạo nợ)
            $table->decimal('debt_amount', 15, 2)->default(0); // Số nợ còn lại (= from_company)
            $table->enum('status', ['pending', 'approved', 'paid_off'])->default('approved'); // Trạng thái
            $table->text('note')->nullable(); // Ghi chú
            $table->json('transaction_ids')->nullable(); // IDs của các transactions liên quan
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Người duyệt
            $table->dateTime('approved_at')->nullable(); // Thời gian duyệt
            $table->dateTime('date'); // Ngày ứng lương
            $table->timestamps();
            
            $table->index('staff_id');
            $table->index('status');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
