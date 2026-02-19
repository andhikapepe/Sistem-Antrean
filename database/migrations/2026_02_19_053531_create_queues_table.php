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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number'); // Contoh: A-001
            $table->foreignId('queue_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_unit_id')->nullable()->constrained()->nullOnDelete();

            // Status: waiting, calling, serving, completed, skipped
            $table->string('status')->default('waiting');

            $table->timestamp('called_at')->nullable();    // Waktu dipanggil
            $table->timestamp('completed_at')->nullable(); // Waktu selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
