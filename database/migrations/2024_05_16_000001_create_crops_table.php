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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('min_temp', 5, 2);
            $table->decimal('max_temp', 5, 2);
            $table->decimal('min_rainfall', 8, 2);
            $table->decimal('max_rainfall', 8, 2);
            $table->decimal('min_humidity', 5, 2);
            $table->decimal('max_humidity', 5, 2);
            $table->decimal('base_yield', 8, 2); // Tonnes per hectare
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};
