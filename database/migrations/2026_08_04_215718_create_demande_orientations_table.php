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
       Schema::create('demande_orientations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('beneficiaire_id')->constrained('beneficiaires')->cascadeOnDelete();
    $table->text('besoin');
    $table->string('statut')->default('nouvelle'); // nouvelle, traitee, cloturee
    $table->text('reponse_admin')->nullable();
    $table->timestamp('traitee_le')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_orientations');
    }
};
