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
        Schema::create('inscription_seances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('seance_id')->constrained('seance_orientations')->cascadeOnDelete();
    $table->foreignId('beneficiaire_id')->constrained('beneficiaires')->cascadeOnDelete();
    $table->string('statut')->default('inscrit'); // inscrit, participe, absent
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscription_seances');
    }
};
