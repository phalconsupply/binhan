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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên tài sản
            $table->date('equipped_date'); // Ngày trang bị
            $table->integer('quantity')->default(1); // Số lượng
            $table->string('brand')->nullable(); // Nhãn hiệu
            $table->enum('usage_type', ['vehicle', 'staff']); // Nơi sử dụng: xe hoặc cá nhân
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade'); // Xe sử dụng
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('cascade'); // Nhân viên sử dụng
            $table->text('note')->nullable(); // Ghi chú
            $table->boolean('is_active')->default(true); // Trạng thái
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes(); // Soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
