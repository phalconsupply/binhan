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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 100)->index(); // company, appearance, language, business, etc.
            $table->string('key', 100)->unique(); // company_name, logo_path, etc.
            $table->text('value')->nullable(); // Setting value
            $table->string('type', 50)->default('text'); // text, textarea, file, image, color, select, checkbox, number
            $table->text('options')->nullable(); // JSON options for select/radio
            $table->string('description')->nullable(); // Description
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_public')->default(false); // Allow public access
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
