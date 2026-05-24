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
        Schema::table('users', function (Blueprint $table) {
            $table->double('latitude')->nullable()->after('password');
            $table->double('longitude')->nullable()->after('latitude');
            $table->string('last_detected_location')->nullable()->after('longitude');
            $table->boolean('location_permission_granted')->default(false)->after('last_detected_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'last_detected_location',
                'location_permission_granted',
            ]);
        });
    }
};
