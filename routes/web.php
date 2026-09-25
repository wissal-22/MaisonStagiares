<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliberationController;
use App\Http\Controllers\DossierInstructionController;
use App\Http\Controllers\ExportReportController;
use App\Http\Controllers\HebergementController;
use App\Http\Controllers\IncidentReclamationController;
use App\Http\Controllers\PreinscriptionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ReferentielController;
use App\Http\Controllers\RestaurationController;
use Illuminate\Support\Facades\Route;

// Redirection accueil
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentification & Test switcher
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/auth/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Espace Stagiaire / Candidat
Route::middleware(['auth', 'role:candidat'])->prefix('mon-espace')->name('candidat.')->group(function () {
    Route::get('/', [PreinscriptionController::class, 'dashboard'])->name('dashboard');
    Route::get('/preinscription', [PreinscriptionController::class, 'create'])->name('preinscription');
    Route::post('/preinscription', [PreinscriptionController::class, 'store'])->name('preinscription.store');
    Route::post('/reclamations', [PreinscriptionController::class, 'storeReclamation'])->name('reclamation.store');
});

// Espace Administratif & Opérationnel (Admin, Gestionnaire, Directeur, Directions Régionale & Centrale)
Route::middleware(['auth', 'role:admin,gestionnaire,directeur,direction_regionale,direction_centrale'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Module Préinscription & Dossiers
    Route::get('/dossiers', [DossierInstructionController::class, 'index'])->name('dossiers.index');
    Route::get('/dossiers/{id}', [DossierInstructionController::class, 'show'])->name('dossiers.show');
    Route::post('/dossiers/{id}/docs/{docId}/valider', [DossierInstructionController::class, 'validerPiece'])->name('dossiers.docs.valider');
    Route::post('/dossiers/{id}/statut', [DossierInstructionController::class, 'changerStatut'])->name('dossiers.statut');

    // 2. Module Sélection & Admission
    Route::get('/deliberation', [DeliberationController::class, 'index'])->name('deliberation.index');
    Route::post('/deliberation/lancer', [DeliberationController::class, 'lancerDeliberation'])->name('deliberation.lancer');
    Route::post('/deliberation/valider-directeur', [DeliberationController::class, 'validerParDirecteur'])->name('deliberation.valider_directeur');
    Route::post('/deliberation/promouvoir/{id}', [DeliberationController::class, 'promouvoirAttente'])->name('deliberation.promouvoir');

    // 3. Module Gestion de l'Hébergement
    Route::get('/hebergement', [HebergementController::class, 'index'])->name('hebergement.index');
    Route::post('/hebergement/auto-affecter', [HebergementController::class, 'autoAffecter'])->name('hebergement.auto_affecter');
    Route::post('/hebergement/affecter-lit', [HebergementController::class, 'affecterLit'])->name('hebergement.affecter_lit');
    Route::post('/hebergement/liberer-lit/{id}', [HebergementController::class, 'libererLit'])->name('hebergement.liberer_lit');
    Route::get('/hebergement/mouvements', [HebergementController::class, 'mouvements'])->name('hebergement.mouvements');
    Route::get('/hebergement/chambres', [HebergementController::class, 'chambres'])->name('hebergement.chambres');
    Route::post('/hebergement/chambres', [HebergementController::class, 'storeChambre'])->name('hebergement.chambres.store');
    Route::get('/hebergement/chambres/{id}/fiche', [HebergementController::class, 'ficheChambre'])->name('hebergement.chambres.fiche');

    // 4. Module Restauration
    Route::get('/restauration', [RestaurationController::class, 'index'])->name('restauration.index');
    Route::post('/restauration/synchroniser', [RestaurationController::class, 'synchroniser'])->name('restauration.synchroniser');
    Route::post('/restauration/repas/{id}', [RestaurationController::class, 'updateRepas'])->name('restauration.repas.update');

    // 5. Module Présences
    Route::get('/presences', [PresenceController::class, 'index'])->name('presences.index');
    Route::post('/presences/enregistrer', [PresenceController::class, 'enregistrerAppel'])->name('presences.enregistrer');

    // 6. Module Incidents & Réclamations
    Route::get('/incidents', [IncidentReclamationController::class, 'index'])->name('incidents.index');
    Route::post('/incidents', [IncidentReclamationController::class, 'store'])->name('incidents.store');
    Route::post('/incidents/{id}/traiter', [IncidentReclamationController::class, 'traiter'])->name('incidents.traiter');

    // 7. Module Paramétrage & Référentiels
    Route::get('/referentiels', [ReferentielController::class, 'index'])->name('referentiels.index');
    Route::post('/referentiels/campagnes', [ReferentielController::class, 'storeCampagne'])->name('referentiels.campagnes.store');
    Route::post('/referentiels/campagnes/{id}', [ReferentielController::class, 'updateCampagne'])->name('referentiels.campagnes.update');
    Route::post('/referentiels/annees-scolaires', [ReferentielController::class, 'storeAnneeScolaire'])->name('referentiels.annees.store');
    Route::post('/referentiels/niveaux', [ReferentielController::class, 'storeNiveau'])->name('referentiels.niveaux.store');
    Route::post('/referentiels/filieres', [ReferentielController::class, 'storeFiliere'])->name('referentiels.filieres.store');
    Route::post('/referentiels/criteres/{id}', [ReferentielController::class, 'updateCritere'])->name('referentiels.criteres.update');

    // 8. Rapports officiels imprimables
    Route::get('/reports/fiche-hebergement/{dossierId}', [ExportReportController::class, 'ficheHebergement'])->name('reports.fiche_hebergement');
    Route::get('/reports/registre-occupation/{internatId}', [ExportReportController::class, 'registreOccupation'])->name('reports.registre_occupation');
    Route::get('/reports/pv-deliberation/{campagneId}', [ExportReportController::class, 'pvDeliberation'])->name('reports.pv_deliberation');
    Route::get('/reports/rapport-restauration/{internatId}', [ExportReportController::class, 'rapportRestauration'])->name('reports.rapport_restauration');
});
