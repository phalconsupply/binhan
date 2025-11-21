<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['thu', 'chi']);
            $table->decimal('amount', 15, 2);
            $table->enum('method', ['cash', 'bank', 'other'])->default('cash');
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->dateTime('date');
            $table->timestamps();
            
            $table->index('incident_id');
            $table->index('vehicle_id');
            $table->index('type');
            $table->index('date');
            $table->index(['vehicle_id', 'type', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
