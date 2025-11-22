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
        Schema::create('incident_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['driver', 'medical_staff']); // Vai trò: lái xe hoặc nhân viên y tế
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['incident_id', 'staff_id', 'role']);
            $table->index('incident_id');
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_staff');
    }
};
