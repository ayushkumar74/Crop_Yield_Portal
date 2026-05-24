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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->decimal('temperature', 5, 2);
            $table->decimal('rainfall', 8, 2);
            $table->decimal('humidity', 5, 2);
            $table->decimal('soil_ph', 4, 2);
            $table->decimal('predicted_yield', 8, 2);
            $table->integer('suitability_score');
            $table->string('risk_level'); // Low, Medium, High
            $table->text('recommendation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
