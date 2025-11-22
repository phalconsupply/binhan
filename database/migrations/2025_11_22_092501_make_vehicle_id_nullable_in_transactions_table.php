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
        Schema::table('transactions', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['vehicle_id']);
            
            // Make vehicle_id nullable
            $table->foreignId('vehicle_id')->nullable()->change();
            
            // Re-add foreign key constraint
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['vehicle_id']);
            
            // Make vehicle_id not nullable
            $table->foreignId('vehicle_id')->nullable(false)->change();
            
            // Re-add foreign key constraint
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });
    }
};
