<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pokemon', function (Blueprint $table) {
            $table->id();
            $table->integer('pokeapi_id')->unique();
            $table->string('name');
            $table->integer('height');
            $table->integer('weight');
            $table->integer('base_experience');
            $table->string('sprite_url');
            $table->json('types')->nullable();
            $table->json('abilities')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pokemon');
    }
};
// Create teams migration
Schema::create('teams', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('user_id')->constrained();
    $table->timestamps();
});

// Create team_pokemon pivot table
Schema::create('team_pokemon', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id')->constrained();
    $table->foreignId('pokemon_id')->constrained();
    $table->json('moves')->nullable();
    $table->string('item')->nullable();
    $table->timestamps();
});
