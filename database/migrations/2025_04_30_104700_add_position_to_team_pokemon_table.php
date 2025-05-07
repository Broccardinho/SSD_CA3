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
        Schema::create('team_pokemon', function (Blueprint $table) {
            // Your existing columns
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pokemon_id')->constrained()->cascadeOnDelete();
            $table->json('moves')->nullable();
            $table->string('item')->nullable();

            // Add these new columns
            $table->integer('position')->default(0);
            $table->unique(['team_id', 'position']); // Ensures one Pokémon per position

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_pokemon', function (Blueprint $table) {
            //
        });
    }
};
