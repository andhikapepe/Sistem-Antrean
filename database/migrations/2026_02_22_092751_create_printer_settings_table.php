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
        Schema::create('printer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama printer (untuk label saja)
            $table->enum('type', ['windows', 'network', 'linux']); // Tipe koneksi
            $table->string('address'); // Nama Share (USB) atau IP Address (LAN)
            $table->integer('port')->default(9100); // Untuk LAN
            $table->integer('width')->default(32); // 32 untuk 58mm, 48 untuk 80mm
            $table->boolean('is_active')->default(false); // Printer mana yang dipakai sekarang
            $table->string('last_status')->default('unknown'); // online, offline, unknown
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printer_settings');
    }
};
