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
        Schema::create('loan_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('cif', 50)->nullable()->comment('Mã hồ sơ khách hàng');
            $table->string('contract_number', 100)->comment('Số hợp đồng tín dụng');
            $table->string('bank_name', 100)->comment('Tên ngân hàng');
            $table->decimal('principal_amount', 15, 2)->comment('Số tiền vay');
            $table->integer('term_months')->comment('Kỳ hạn (tháng)');
            $table->integer('total_periods')->comment('Số kỳ trả nợ');
            $table->date('disbursement_date')->comment('Ngày giải ngân');
            $table->decimal('base_interest_rate', 5, 2)->comment('Lãi suất theo hợp đồng (%/năm)');
            $table->tinyInteger('payment_day')->comment('Ngày trả nợ hàng tháng (1-28)');
            $table->enum('status', ['active', 'paid_off', 'cancelled'])->default('active');
            $table->decimal('remaining_balance', 15, 2)->default(0)->comment('Dư nợ còn lại');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['vehicle_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_profiles');
    }
};
