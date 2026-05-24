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
            $table->string('avatar')->nullable()->after('email');
            $table->string('theme_preference')->default('light')->after('location_permission_granted');
            $table->string('language_preference')->default('en')->after('theme_preference');
            $table->text('notification_preferences')->nullable()->after('language_preference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'theme_preference', 'language_preference', 'notification_preferences']);
        });
    }
};
