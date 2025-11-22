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
        Schema::create('staff_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->comment('User who created this adjustment');
            $table->enum('type', ['addition', 'deduction'])->comment('addition = cộng, deduction = trừ');
            $table->decimal('amount', 10, 2);
            $table->date('month')->comment('Tháng áp dụng điều chỉnh (YYYY-MM-01)');
            $table->string('category')->comment('Loại: thưởng, phạt, tạm ứng, etc');
            $table->text('reason')->comment('Lý do điều chỉnh');
            $table->enum('status', ['pending', 'applied', 'debt'])->default('pending')->comment('pending = chờ, applied = đã áp dụng, debt = nợ');
            $table->decimal('debt_amount', 10, 2)->default(0)->comment('Số tiền nợ nếu không đủ trừ');
            $table->timestamp('applied_at')->nullable()->comment('Thời điểm áp dụng');
            $table->timestamps();
            
            $table->index(['staff_id', 'month']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_adjustments');
    }
};
