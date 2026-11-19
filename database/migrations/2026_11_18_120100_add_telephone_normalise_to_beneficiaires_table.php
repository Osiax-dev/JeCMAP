<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sécurité (phase 1) : dédoublonnage des bénéficiaires.
 * Ajoute un téléphone normalisé (chiffres seuls, sans l'indicatif 229) indexé,
 * et le calcule pour les lignes existantes. Aucune donnée n'est supprimée ni fusionnée.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiaires', function (Blueprint $table) {
            $table->string('telephone_normalise', 20)->nullable()->after('telephone')->index();
        });

        DB::table('beneficiaires')
            ->whereNotNull('telephone')
            ->orderBy('id')
            ->chunkById(500, function ($lignes) {
                foreach ($lignes as $ligne) {
                    DB::table('beneficiaires')
                        ->where('id', $ligne->id)
                        ->update(['telephone_normalise' => $this->normaliser($ligne->telephone)]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('beneficiaires', function (Blueprint $table) {
            $table->dropIndex(['telephone_normalise']);
            $table->dropColumn('telephone_normalise');
        });
    }

    /** Copie volontaire de Beneficiaire::normaliserTelephone() : une migration ne doit pas dépendre du modèle. */
    private function normaliser(?string $telephone): ?string
    {
        $chiffres = preg_replace('/\D+/', '', (string) $telephone);
        $chiffres = preg_replace('/^00/', '', $chiffres);

        if (str_starts_with($chiffres, '229') && strlen($chiffres) >= 11) {
            $chiffres = substr($chiffres, 3);
        }

        return strlen($chiffres) >= 8 ? $chiffres : null;
    }
};
