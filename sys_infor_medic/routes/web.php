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

    // Création rapide d'utilisateurs (AdminController)
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');

    // Gestion classique des utilisateurs (UserController)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Gestion des patients (UserController)
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
Route::prefix('medecin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [MedecinController::class, 'dashboard'])->name('medecin.dashboard');
    Route::get('/dossierpatient', [MedecinController::class, 'dossierpatient'])->name('medecin.dossierpatient');
    Route::get('/consultations', [MedecinController::class, 'consultations'])->name('medecin.consultations');
    Route::get('/ordonnances', [MedecinController::class, 'ordonnances'])->name('medecin.ordonnances');

    // Routes AJAX pour FullCalendar
    Route::prefix('consultations')->group(function () {
        Route::get('events', [MedecinController::class, 'getConsultations'])->name('medecin.consultations.events');
        Route::post('store', [MedecinController::class, 'storeConsultation'])->name('medecin.consultations.store');
        Route::put('update/{id}', [MedecinController::class, 'updateConsultation'])->name('medecin.consultations.update');
        Route::delete('delete/{id}', [MedecinController::class, 'deleteConsultation'])->name('medecin.consultations.delete');
    });
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
