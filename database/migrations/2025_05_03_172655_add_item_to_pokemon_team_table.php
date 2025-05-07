<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pokemon_team', function (Blueprint $table) {
            if (!Schema::hasColumn('pokemon_team', 'item')) {
                $table->string('item')->nullable()->after('moves');
            }
        });
    }

    public function down()
    {
        Schema::table('pokemon_team', function (Blueprint $table) {
            $table->dropColumn('item');
        });
    }
};
