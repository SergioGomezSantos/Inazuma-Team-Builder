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
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->integer("GP");
            $table->integer("TP");
            $table->integer("Kick");
            $table->integer("Body");
            $table->integer("Control");
            $table->integer("Guard");
            $table->integer("Speed");
            $table->integer("Stamina");
            $table->integer("Guts");
            $table->integer("Freedom");
            $table->string("version");
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
