<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sécurité (phase 1) : distinction fiable administrateur / utilisateur ordinaire.
 *
 * Aucun compte n'est promu automatiquement : jusqu'ici, n'importe qui pouvait
 * créer un compte via /register, on ne peut donc pas savoir lesquels sont légitimes.
 * Promouvoir les vrais administrateurs avec : php artisan jecmap:admin email@exemple.com
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
