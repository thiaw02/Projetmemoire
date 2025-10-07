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

// Profil (auth)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::put('/profile/patient', [\App\Http\Controllers\ProfileController::class, 'updatePatientInfo'])->name('profile.patient.update');
    Route::put('/admin/settings', [\App\Http\Controllers\ProfileController::class, 'updateSettings'])->name('admin.settings.update');
    Route::post('/profile/patient/document', [\App\Http\Controllers\ProfileController::class, 'uploadPatientDocument'])->name('profile.patient.document.upload');
    Route::delete('/profile/patient/document/{id}', [\App\Http\Controllers\ProfileController::class, 'deletePatientDocument'])->name('profile.patient.document.delete');

    // Chat
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/unread-count', [\App\Http\Controllers\ChatController::class, 'unreadCount'])->name('chat.unread');
    Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/typing', [\App\Http\Controllers\ChatController::class, 'typing'])->name('chat.typing');
    Route::get('/chat/typing-status', [\App\Http\Controllers\ChatController::class, 'typingStatus'])->name('chat.typingStatus');

    // Alerts/Notifications summary
    Route::get('/alerts/summary', [\App\Http\Controllers\AlertsController::class, 'summary'])->name('alerts.summary');
});

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset
Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.update');

// Inscription Patient uniquement
Route::get('/inscription', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/inscription', [AuthController::class, 'register'])->name('register.submit');

// ===================== ADMIN =====================
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Utilisateurs (hors patients)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/export', [UserController::class, 'exportCsv'])->name('admin.users.export');
        Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::put('/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
        Route::put('/{id}/active', [UserController::class, 'updateActive'])->name('admin.users.updateActive');
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

    // Enregistrement des permissions
    Route::post('/permissions', [AdminController::class, 'savePermissions'])->name('admin.permissions.save');

    // Audit logs
Route::get('/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('admin.audit.index');
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
Route::prefix('medecin')->middleware(['auth','role:medecin'])->group(function () {
    Route::get('/dashboard', [MedecinController::class, 'dashboard'])->name('medecin.dashboard');
    Route::get('/dossierpatient', [MedecinController::class, 'dossierpatient'])->name('medecin.dossierpatient');
    Route::get('/patients/{patientId}', [MedecinController::class, 'showPatient'])->name('medecin.patients.show');
    Route::get('/consultations', [MedecinController::class, 'consultations'])->name('medecin.consultations');
    Route::post('/consultations', [MedecinController::class, 'storeConsultation'])->name('medecin.consultations.store');
    Route::get('/consultations/{id}/edit', [MedecinController::class, 'editConsultation'])->name('medecin.consultations.edit');
    Route::put('/consultations/{id}', [MedecinController::class, 'updateConsultation'])->name('medecin.consultations.update');
    Route::get('/ordonnances', [MedecinController::class, 'ordonnances'])->name('medecin.ordonnances');
    Route::post('/ordonnances', [MedecinController::class, 'storeOrdonnance'])->name('medecin.ordonnances.store');
    Route::get('/ordonnances/{id}/edit', [MedecinController::class, 'editOrdonnance'])->name('medecin.ordonnances.edit');
    Route::put('/ordonnances/{id}', [MedecinController::class, 'updateOrdonnance'])->name('medecin.ordonnances.update');
    Route::get('/ordonnances/{id}/download', [MedecinController::class, 'downloadOrdonnance'])->name('medecin.ordonnances.download');
    Route::post('/ordonnances/{id}/resend', [MedecinController::class, 'resendOrdonnance'])->name('medecin.ordonnances.resend');
    Route::post('/rendezvous/{id}/mark-consulted', [MedecinController::class, 'markRdvConsulted'])->name('medecin.rdv.markConsulted');
});

// ===================== INFIRMIER =====================
Route::prefix('infirmier')->middleware('auth')->group(function () {
    Route::get('/dashboard', [InfirmierController::class, 'dashboard'])->name('infirmier.dashboard');
    Route::get('/dossiers', [InfirmierController::class, 'dossiers']);
});

// ===================== PATIENT =====================
Route::prefix('patient')->middleware('auth')->group(function () {
    Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/rendezvous', [PatientController::class, 'rendezvous'])->name('patient.rendezvous');
    Route::post('/rendezvous', [PatientController::class, 'storeRendez'])->name('patient.storeRendez');
    Route::get('/dossiermedical', [PatientController::class, 'dossier'])->name('patient.dossier');
    Route::get('/ordonnances/{id}/download', [PatientController::class, 'downloadOrdonnance'])->name('patient.ordonnances.download');
    Route::post('/ordonnances/{id}/resend', [PatientController::class, 'resendOrdonnance'])->name('patient.ordonnances.resend');
});

// Page succès inscription
Route::get('/register/success', function () {
    return view('auth.inscription_success');
})->name('register.success');



Route::resource('suivi', SuiviController::class);
Route::resource('dossier', DossierController::class);
Route::resource('historique', HistoriqueController::class);
Route::get('/rendezvous/create', [PatientController::class, 'createRendez'])->name('rendez.create');
