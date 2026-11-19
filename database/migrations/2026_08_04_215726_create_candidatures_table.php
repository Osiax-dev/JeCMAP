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
        Schema::create('candidatures', function (Blueprint $table) {
    $table->id();
    $table->foreignId('beneficiaire_id')->constrained('beneficiaires')->cascadeOnDelete();
    $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
    $table->text('motivation')->nullable();
    $table->string('statut')->default('en_attente'); // en_attente, retenu, refuse, en_cours, termine
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
