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
        Schema::create('prompt2s', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->text('codeMermaid')->nullable(); // Ajouter une valeur par défaut
            $table->string('imagePath')->nullable(); // Ajouter ce champ et le rendre nullable si nécessaire
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt2s');
    }
};
