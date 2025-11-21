<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('date');
            $table->foreignId('dispatch_by')->constrained('users');
            $table->string('destination')->nullable();
            $table->text('summary')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_id');
            $table->index('patient_id');
            $table->index('date');
            $table->index(['vehicle_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
