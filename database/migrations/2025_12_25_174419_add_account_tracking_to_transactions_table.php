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
            $table->string('from_account', 100)->nullable()->after('note')->comment('Tài khoản nguồn');
            $table->string('to_account', 100)->nullable()->after('from_account')->comment('Tài khoản đích');
            $table->decimal('from_balance_before', 15, 2)->nullable()->after('to_account')->comment('Số dư tài khoản nguồn trước GD');
            $table->decimal('from_balance_after', 15, 2)->nullable()->after('from_balance_before')->comment('Số dư tài khoản nguồn sau GD');
            $table->decimal('to_balance_before', 15, 2)->nullable()->after('from_balance_after')->comment('Số dư tài khoản đích trước GD');
            $table->decimal('to_balance_after', 15, 2)->nullable()->after('to_balance_before')->comment('Số dư tài khoản đích sau GD');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'from_account',
                'to_account',
                'from_balance_before',
                'from_balance_after',
                'to_balance_before',
                'to_balance_after',
            ]);
        });
    }
};
