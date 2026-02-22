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
        Schema::create('kiosk_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('kiosk_name'); // Misal: Pintu Utama, Kiosk Lantai 2
            $table->string('client_ip')->unique(); // IP Komputer/Tablet yang buka Kiosk
            $table->foreignId('printer_setting_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosk_assignments');
    }
};
