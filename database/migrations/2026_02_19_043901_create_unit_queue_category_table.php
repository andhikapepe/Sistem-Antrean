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
        Schema::create('unit_queue_category', function (Blueprint $table) {
            // Gunakan nama foreign yang konsisten dengan nama tabel master
            $table->foreignId('service_unit_id')->constrained('service_units')->cascadeOnDelete();
            $table->foreignId('queue_category_id')->constrained('queue_categories')->cascadeOnDelete();

            $table->primary(['service_unit_id', 'queue_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_queue_category');
    }
};
