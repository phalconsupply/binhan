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
        Schema::table('staff_adjustments', function (Blueprint $table) {
            $table->foreignId('incident_id')->nullable()->after('staff_id')->constrained('incidents')->onDelete('set null')->comment('Chuyến đi liên quan (nếu có)');
            $table->json('transaction_ids')->nullable()->after('applied_at')->comment('Danh sách ID transactions được tạo');
            $table->decimal('from_incident_amount', 10, 2)->default(0)->after('transaction_ids')->comment('Số tiền lấy từ chuyến đi');
            $table->decimal('from_company_amount', 10, 2)->default(0)->after('from_incident_amount')->comment('Số tiền lấy từ công ty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_adjustments', function (Blueprint $table) {
            $table->dropForeign(['incident_id']);
            $table->dropColumn(['incident_id', 'transaction_ids', 'from_incident_amount', 'from_company_amount']);
        });
    }
};
