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
            $table->foreignId('from_location_id')->nullable()->after('destination')->constrained('locations')->nullOnDelete();
            $table->foreignId('to_location_id')->nullable()->after('from_location_id')->constrained('locations')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->after('to_location_id')->constrained()->nullOnDelete();
            $table->decimal('commission_amount', 10, 2)->nullable()->after('partner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['from_location_id']);
            $table->dropForeign(['to_location_id']);
            $table->dropForeign(['partner_id']);
            $table->dropColumn(['from_location_id', 'to_location_id', 'partner_id', 'commission_amount']);
        });
    }
};
