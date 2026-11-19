<?php

// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\DemandeOrientationController;
use App\Http\Controllers\SeanceOrientationController;
use App\Http\Controllers\Admin\FormationAdminController;
use App\Http\Controllers\Admin\DemandeAdminController;
use App\Http\Controllers\Admin\SeanceAdminController;
use App\Http\Controllers\Admin\BeneficiaireAdminController;

// ---- Public ----
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/a-propos', [HomeController::class, 'apropos'])->name('apropos');

Route::get('/formations', [FormationController::class, 'index'])->name('formations.index');
Route::get('/formations/{formation}', [FormationController::class, 'show'])->name('formations.show');
Route::post('/formations/{formation}/candidater', [FormationController::class, 'candidater'])
    ->middleware(['throttle:formulaires-publics', 'anti-robot'])
    ->name('formations.candidater');

Route::get('/orientation', [DemandeOrientationController::class, 'create'])->name('orientation.create');
Route::post('/orientation', [DemandeOrientationController::class, 'store'])
    ->middleware(['throttle:formulaires-publics', 'anti-robot'])
    ->name('orientation.store');

Route::get('/seances', [SeanceOrientationController::class, 'index'])->name('seances.index');
Route::post('/seances/{seance}/inscription', [SeanceOrientationController::class, 'inscrire'])
    ->middleware(['throttle:formulaires-publics', 'anti-robot'])
    ->name('seances.inscrire');

// ---- Back-office : réservé aux comptes administrateurs (is_admin) ----

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/dashboard')->name('accueil'); 
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('formations', FormationAdminController::class);
    Route::resource('seances', SeanceAdminController::class);

    Route::get('/demandes', [DemandeAdminController::class, 'index'])->name('demandes.index');
    Route::get('/demandes/{demande}', [DemandeAdminController::class, 'show'])->name('demandes.show');
    Route::post('/demandes/{demande}/repondre', [DemandeAdminController::class, 'repondre'])->name('demandes.repondre');

    Route::get('/beneficiaires', [BeneficiaireAdminController::class, 'index'])->name('beneficiaires.index');
    Route::get('/beneficiaires/{beneficiaire}', [BeneficiaireAdminController::class, 'show'])->name('beneficiaires.show');
    Route::post('/beneficiaires/{beneficiaire}/suivis', [BeneficiaireAdminController::class, 'ajouterSuivi'])->name('beneficiaires.suivis.store');
});
// ---- Profil de l'administrateur connecté (nom, e-mail ; mot de passe via PUT /password) ----
// La suppression de compte n'est pas proposée : les comptes se gèrent avec `php artisan jecmap:admin`.
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'admin'])->name('dashboard');

require __DIR__.'/auth.php';