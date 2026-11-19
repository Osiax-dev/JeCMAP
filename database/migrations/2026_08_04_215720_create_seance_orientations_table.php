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
        Schema::create('seance_orientations', function (Blueprint $table) {
    $table->id();
    $table->string('titre');
    $table->text('description')->nullable();
    $table->dateTime('date_seance');
    $table->string('lieu')->nullable(); // adresse ou lien en ligne
    $table->string('format')->default('presentiel'); // presentiel, en_ligne
    $table->unsignedInteger('places')->nullable();
    $table->string('statut')->default('a_venir'); // a_venir, passee
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seance_orientations');
    }
};
