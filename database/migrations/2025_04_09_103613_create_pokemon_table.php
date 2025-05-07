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
