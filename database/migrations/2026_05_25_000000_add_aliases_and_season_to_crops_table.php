<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('crops', function (Blueprint $table) {
            if (! Schema::hasColumn('crops', 'aliases')) {
                $table->text('aliases')->nullable()->after('name');
            }
            if (! Schema::hasColumn('crops', 'season')) {
                $table->string('season')->nullable()->after('aliases');
            }
        });
    }

    public function down()
    {
        Schema::table('crops', function (Blueprint $table) {
            if (Schema::hasColumn('crops', 'aliases')) {
                $table->dropColumn('aliases');
            }
            if (Schema::hasColumn('crops', 'season')) {
                $table->dropColumn('season');
            }
        });
    }
};
