<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Gestion des comptes administrateurs (seul moyen de créer ou promouvoir un admin).
 *
 *   php artisan jecmap:admin --liste             liste tous les comptes et leur rôle
 *   php artisan jecmap:admin email@exemple.com   promeut un compte existant, ou le crée
 *   php artisan jecmap:admin email@exemple.com --retirer   retire le rôle administrateur
 */
class GererAdministrateur extends Command
{
    protected $signature = 'jecmap:admin
                            {email? : Adresse e-mail du compte}
                            {--retirer : Retirer le rôle administrateur au lieu de l\'accorder}
                            {--liste : Lister les comptes existants}';

    protected $description = 'Créer, promouvoir ou rétrograder un administrateur JeCMAP';

    public function handle(): int
    {
        if ($this->option('liste')) {
            return $this->lister();
        }

        $email = mb_strtolower(trim((string) $this->argument('email')));

        if ($email === '' || Validator::make(['email' => $email], ['email' => 'email'])->fails()) {
            $this->error('Indique une adresse e-mail valide (ou utilise --liste).');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($this->option('retirer')) {
            if (! $user) {
                $this->error("Aucun compte pour {$email}.");

                return self::FAILURE;
            }

            $user->forceFill(['is_admin' => false])->save();
            $this->info("Rôle administrateur retiré à {$email}.");

            return self::SUCCESS;
        }

        if ($user) {
            $user->forceFill(['is_admin' => true])->save();
            $this->info("{$email} est maintenant administrateur.");

            return self::SUCCESS;
        }

        return $this->creer($email);
    }

    private function creer(string $email): int
    {
        $this->line("Aucun compte pour {$email} : création d'un nouvel administrateur.");

        $nom = trim((string) $this->ask('Nom affiché'));
        $motDePasse = (string) $this->secret('Mot de passe (12 caractères minimum)');
        $confirmation = (string) $this->secret('Confirme le mot de passe');

        $validation = Validator::make(
            ['name' => $nom, 'password' => $motDePasse, 'password_confirmation' => $confirmation],
            ['name' => ['required', 'string', 'max:255'], 'password' => ['required', 'confirmed', Password::min(12)]]
        );

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $erreur) {
                $this->error($erreur);
            }

            return self::FAILURE;
        }

        $user = User::create(['name' => $nom, 'email' => $email, 'password' => $motDePasse]);
        $user->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();

        $this->info("Administrateur {$email} créé.");

        return self::SUCCESS;
    }

    private function lister(): int
    {
        $this->table(
            ['ID', 'Nom', 'E-mail', 'Admin', 'Créé le'],
            User::orderBy('id')->get()->map(fn (User $u) => [
                $u->id, $u->name, $u->email, $u->isAdmin() ? 'oui' : 'non', $u->created_at?->format('d/m/Y H:i'),
            ])
        );

        return self::SUCCESS;
    }
}
