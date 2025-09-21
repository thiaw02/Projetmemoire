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
    DossierController,
    HistoriqueController,
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
Route::prefix('admin')->middleware('auth')->group(function () {
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

    // Patients
    Route::prefix('patients')->group(function () {
        Route::get('/', [UserController::class, 'patientsList'])->name('admin.patients.index');
        Route::get('/create', [UserController::class, 'createPatient'])->name('admin.patients.create');
        Route::post('/', [UserController::class, 'storePatient'])->name('admin.patients.store');
        Route::get('/{id}/edit', [UserController::class, 'editPatient'])->name('admin.patients.edit');
        Route::put('/{id}', [UserController::class, 'updatePatient'])->name('admin.patients.update');
        Route::delete('/{id}', [UserController::class, 'destroyPatient'])->name('admin.patients.destroy');
    });
});

/// ===================== SECRETAIRE =====================
Route::prefix('secretaire')->middleware(['auth'])->group(function() {
    Route::get('/dashboard', [SecretaireController::class, 'dashboard'])->name('secretaire.dashboard');
    Route::get('/dossieradmin', [SecretaireController::class, 'dossiersAdmin'])->name('secretaire.dossiersAdmin');
    Route::put('/dossieradmin/patient/{id}', [SecretaireController::class, 'updatePatient'])->name('secretaire.updatePatient');
    Route::post('/dossieradmin/patient', [SecretaireController::class, 'storePatient'])->name('secretaire.storePatient');
    Route::get('/rendezvous', [SecretaireController::class, 'rendezvous'])->name('secretaire.rendezvous');
    Route::post('/rendezvous/store', [SecretaireController::class, 'storeRdv'])->name('secretaire.rendezvous.store');
    Route::get('/rendezvous/{id}/confirm', [SecretaireController::class, 'confirmRdv'])->name('secretaire.rendezvous.confirm');
    Route::get('/rendezvous/{id}/cancel', [SecretaireController::class, 'cancelRdv'])->name('secretaire.rendezvous.cancel');
    Route::get('/admissions', [SecretaireController::class, 'admissions'])->name('secretaire.admissions');
    Route::post('/admissions', [SecretaireController::class, 'storeAdmission'])->name('secretaire.storeAdmission');
    Route::put('/admissions/{id}', [SecretaireController::class, 'updateAdmission'])->name('secretaire.updateAdmission');
});

// ===================== MEDECIN =====================
Route::prefix('medecin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [MedecinController::class, 'dashboard'])->name('medecin.dashboard');
    Route::get('/dossierpatient', [MedecinController::class, 'dossierpatient'])->name('medecin.dossierpatient');
    Route::get('/consultations', [MedecinController::class, 'consultations'])->name('medecin.consultations');
    Route::post('/consultations', [MedecinController::class, 'storeConsultation'])->name('medecin.consultations.store');
    Route::get('/ordonnances', [MedecinController::class, 'ordonnances'])->name('medecin.ordonnances');
    Route::post('/ordonnances', [MedecinController::class, 'storeOrdonnance'])->name('medecin.ordonnances.store');
});

// ===================== INFIRMIER =====================
Route::prefix('infirmier')->middleware('auth')->group(function () {
    Route::get('/dashboard', [InfirmierController::class, 'dashboard'])->name('infirmier.dashboard');
    Route::get('/dossiers', [InfirmierController::class, 'dossiers']);
});

// ===================== PATIENT =====================
Route::prefix('patient')->middleware('auth')->group(function () {
    Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/rendezvous', [PatientController::class, 'rendezvous']);
    Route::post('/rendezvous', [PatientController::class, 'storeRendez'])->name('rendez.store');
    Route::get('/dossiermedical', [PatientController::class, 'dossier']);
});

// Page succès inscription
Route::get('/register/success', function () {
    return view('auth.inscription_success');
})->name('register.success');



Route::resource('suivi', SuiviController::class);
Route::resource('dossier', DossierController::class);
Route::resource('historique', HistoriqueController::class);
Route::get('/rendezvous/create', [PatientController::class, 'createRendez'])->name('rendez.create');
