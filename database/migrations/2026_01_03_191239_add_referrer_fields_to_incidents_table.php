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
        Schema::table('incidents', function (Blueprint $table) {
            // Add polymorphic referrer fields (người giới thiệu)
            $table->string('referrer_type')->nullable()->after('partner_id');
            $table->unsignedBigInteger('referrer_id')->nullable()->after('referrer_type');
            
            // Add index for polymorphic relationship
            $table->index(['referrer_type', 'referrer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropIndex(['referrer_type', 'referrer_id']);
            $table->dropColumn(['referrer_type', 'referrer_id']);
        });
    }
};
