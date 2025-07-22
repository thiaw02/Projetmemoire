<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, 
    AdminController, 
    SecretaireController, 
    MedecinController, 
    InfirmierController, 
    PatientController
};

// Accueil
Route::get('/', fn() => redirect('/login'));

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Secrétaire
Route::prefix('secretaire')->group(function () {
    Route::get('/dashboard', [SecretaireController::class, 'dashboard'])->name('secretaire.dashboard');
    Route::get('/dossieradmin', [SecretaireController::class, 'dossiersAdmin']);
    Route::get('/rendezvous', [SecretaireController::class, 'rendezvous']);
    Route::get('/admissions', [SecretaireController::class, 'admissions']);
});

// Médecin
Route::prefix('medecin')->group(function () {
    Route::get('/dashboard', [MedecinController::class, 'dashboard'])->name('medecin.dashboard');
    Route::get('/dossierpatient', [MedecinController::class, 'dossierpatient']);
    Route::get('/consultations', [MedecinController::class, 'consultations']);
    Route::get('/ordonnances', [MedecinController::class, 'ordonnances']);
});

// Infirmier
Route::prefix('infirmier')->group(function () {
    Route::get('/dashboard', [InfirmierController::class, 'dashboard'])->name('infirmier.dashboard');
    Route::get('/dossiers', [InfirmierController::class, 'dossiers']);
});

// Patient
Route::prefix('patient')->group(function () {
    Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/rendezvous', [PatientController::class, 'rendezvous']);
    Route::get('/dossiermedical', [PatientController::class, 'dossier']);
});
