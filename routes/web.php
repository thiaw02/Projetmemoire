<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AdminController,
    UserController,
    SecretaireController,
    MedecinController,
    InfirmierController,
    PatientController,
    SuiviController,
    HistoriqueController,
    DossierController,
    HomeController,
};

// Accueil
Route::get('/', fn () => redirect('/login'));

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Inscription Patient uniquement
Route::get('/inscription', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/inscription', [AuthController::class, 'register'])->name('register.submit');

// ===================== ADMIN =====================
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Utilisateurs (hors patients)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Patients (côté admin)
    Route::prefix('patients')->group(function () {
        Route::get('/', [UserController::class, 'patientsList'])->name('admin.patients.index');
        Route::get('/create', [UserController::class, 'createPatient'])->name('admin.patients.create');
        Route::post('/', [UserController::class, 'storePatient'])->name('admin.patients.store');
        Route::get('/{id}/edit', [UserController::class, 'editPatient'])->name('admin.patients.edit');
        Route::put('/{id}', [UserController::class, 'updatePatient'])->name('admin.patients.update');
        Route::delete('/{id}', [UserController::class, 'destroyPatient'])->name('admin.patients.destroy');
    });
});

// ===================== SECRETAIRE =====================
Route::prefix('secretaire')->group(function () {
    Route::get('/dashboard', [SecretaireController::class, 'dashboard'])->name('secretaire.dashboard');
    Route::get('/dossieradmin', [SecretaireController::class, 'dossiersAdmin']);
    Route::get('/rendezvous', [SecretaireController::class, 'rendezvous']);
    Route::get('/admissions', [SecretaireController::class, 'admissions']);
});

// ===================== MEDECIN =====================
Route::prefix('medecin')->group(function () {
    Route::get('/dashboard', [MedecinController::class, 'dashboard'])->name('medecin.dashboard');
    Route::get('/dossierpatient', [MedecinController::class, 'dossierpatient']);
    Route::get('/consultations', [MedecinController::class, 'consultations']);
    Route::get('/ordonnances', [MedecinController::class, 'ordonnances']);
});

// ===================== INFIRMIER =====================
Route::prefix('infirmier')->group(function () {
    Route::get('/dashboard', [InfirmierController::class, 'dashboard'])->name('infirmier.dashboard');
    Route::get('/dossiers', [InfirmierController::class, 'dossiers']);
});

// ===================== PATIENT =====================
Route::prefix('patient')->group(function () {
    Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/rendezvous', [PatientController::class, 'rendezvous']);
    Route::get('/dossiermedical', [PatientController::class, 'dossier']);
});

// Page succès inscription
Route::get('/register/success', function () {
    return view('auth.inscription_success');
})->name('register.success');
Route::post('/rendezvous/store', [PatientController::class, 'storeRendez'])->name('rendez.store');

Route::get('/suivi/create', [SuiviController::class, 'create'])->name('suivi.create');

// Optionnel : route pour sauvegarder le suivi (POST)

Route::get('/historique', [HistoriqueController::class, 'index'])->name('historique.index');

// Route pour éditer un dossier médical
Route::get('/dossier/{id}/edit', [DossierController::class, 'edit'])->name('dossier.edit');


// Optionnel : route pour sauvegarder la modification
Route::put('/dossier/{id}', [DossierController::class, 'update'])->name('dossier.update');

Route::get('/', [HomeController::class, 'index'])->name('home');


// Liste des suivis
Route::get('/suivi', [SuiviController::class, 'index'])->name('suivi.index');

// Formulaire de création
Route::get('/suivi/create', [SuiviController::class, 'create'])->name('suivi.create');

// Enregistrement d'un suivi
Route::post('/suivi', [SuiviController::class, 'store'])->name('suivi.store');
