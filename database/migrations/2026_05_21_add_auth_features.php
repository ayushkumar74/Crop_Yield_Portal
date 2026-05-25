<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add google_id and other columns to users (only if they don't exist)
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }
            if (! Schema::hasColumn('users', 'password_set')) {
                $table->boolean('password_set')->default(true)->after('avatar');
            }
        });

        // OTP table for login verification
        if (! Schema::hasTable('login_otps')) {
            Schema::create('login_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email')->index();
                $table->string('otp', 255);
                $table->timestamp('expires_at');
                $table->boolean('used')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['google_id', 'avatar', 'password_set'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('login_otps');
    }
};
