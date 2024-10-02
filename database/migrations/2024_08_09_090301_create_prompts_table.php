<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromptsTable extends Migration
{
    public function up()
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->text('codeMermaid')->nullable(); // Ajouter une valeur par défaut
            $table->string('imagePath')->nullable(); // Ajouter ce champ et le rendre nullable si nécessaire
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prompts');
    }
}
