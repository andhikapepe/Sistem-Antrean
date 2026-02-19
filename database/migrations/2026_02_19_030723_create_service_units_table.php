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
        Schema::create('service_units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Loket 1, Ruang Mawar
            $table->string('slug')->unique();
            $table->enum('type', ['room', 'counter', 'table'])->default('counter');
            $table->string('location')->nullable(); // Lantai 1, Sayap Barat, dll
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            // status: 'ready', 'resting', 'away'
            $table->string('status')->default('ready');
            $table->boolean('is_occupied')->default(false);
            $table->foreignId('current_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_units');
    }
};
