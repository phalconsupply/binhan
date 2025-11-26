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
        Schema::create('loan_interest_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loan_profiles')->onDelete('cascade');
            $table->decimal('old_interest_rate', 5, 2)->comment('Lãi suất cũ (%/năm)');
            $table->decimal('new_interest_rate', 5, 2)->comment('Lãi suất mới (%/năm)');
            $table->date('effective_date')->comment('Ngày áp dụng');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['loan_id', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_interest_adjustments');
    }
};
