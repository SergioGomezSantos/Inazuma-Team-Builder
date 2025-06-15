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
        Schema::create('techniques', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('element', ['Montaña', 'Aire', 'Bosque', 'Fuego', 'Neutro'])->nullable();
            $table->enum('type', ['Tiro', 'Regate', 'Bloqueo', 'Atajo', 'Talento']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('techniques');
    }
};
