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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Account code (e.g., CASH-001, VEH-001)');
            $table->string('name', 100)->comment('Account display name');
            $table->enum('type', [
                'asset',        // Tài sản (cash, vehicle accounts)
                'liability',    // Nợ phải trả (loans, payables)
                'equity',       // Vốn chủ sở hữu (owner equity)
                'revenue',      // Doanh thu
                'expense',      // Chi phí
            ])->comment('Account type following accounting principles');
            $table->enum('category', [
                'company_fund',      // Quỹ công ty
                'company_reserved',  // Quỹ dự kiến chi
                'vehicle',           // Tài khoản xe
                'staff',             // Tài khoản nhân viên
                'customer',          // Khách hàng
                'partner',           // Đối tác
                'external',          // Bên ngoài
                'income',            // Thu nhập
                'system',            // Tài khoản hệ thống
            ])->comment('Business category');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID tham chiếu (vehicle_id, staff_id, etc)');
            $table->string('reference_type', 50)->nullable()->comment('Model class (Vehicle, Staff, etc)');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Parent account for hierarchical structure');
            $table->decimal('balance', 15, 2)->default(0)->comment('Current balance (denormalized for performance)');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('system_account')->default(false)->comment('System accounts cannot be deleted');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('category');
            $table->index(['reference_type', 'reference_id']);
            $table->index('parent_id');
            $table->index(['is_active', 'type']);
            
            // Foreign key for parent
            $table->foreign('parent_id')->references('id')->on('accounts')->nullOnDelete();
        });

        // Add comment to table
        DB::statement("ALTER TABLE `accounts` COMMENT = 'Chart of Accounts - Master account list'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
