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
        Schema::create('loan_payment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('loan_id')->constrained('loan_profiles')->onDelete('cascade');
            $table->enum('payment_type', ['regular', 'partial_prepayment', 'full_payoff']);
            $table->decimal('amount', 15, 2);
            $table->json('schedules_snapshot')->comment('Snapshot of schedules before payment');
            $table->decimal('previous_remaining_balance', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payment_history');
    }
};
