<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ResidentsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
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 grpq
|--------------------------------------------------------------------------
*/

// ---------- Halaman Depan ----------
Route::get('/', function () {
    return view('splash');
})->name('splash');

// ---------- Dashboard ----------
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ---------- RegistrationController ----------
Route::get('/registration', [RegistrationController::class, 'index'])->name('registration.index');
Route::get('/registration/{registration}/form-wanita', [RegistrationController::class, 'formWanita'])->name('registration.form.wanita');
Route::post('/registration/{registration}/wanita', [RegistrationController::class, 'storeWanita'])->name('registration.store-wanita');

// URL lama tanpa ID pendaftaran (sebelum ada param {registration})
Route::get('/registration/form-wanita', function () {
    return redirect()->route('registration.index');
});
Route::get('/registration/nik/{nik}/lookup', [RegistrationController::class, 'lookupNik'])->name('registration.lookup.nik');
Route::post('/registration/start', [RegistrationController::class, 'start'])->name('registration.start');
Route::get('/registration/start/{serviceType}', [RegistrationController::class, 'startService'])->name('registration.start.service');

Route::post('/registration', [RegistrationController::class, 'store'])->name('registration.store');
Route::get('/region/{table}/{parentId?}', [RegistrationController::class, 'region'])->name('registration.region');
Route::get('/registration/api/region/{table}/{parentId?}', [RegistrationController::class, 'region'])->name('registration.api.region');
Route::get('/api/check-nik/{nik}', [RegistrationController::class, 'checkNik'])->name('api.check.nik');

// Dashboard without ID redirects to index (list of registrations)
Route::get('/registration/dashboard', function () {
    return redirect()->route('registration.index');
})->name('registration.dashboard.index');

// Dynamic routes (specific first, then catch-all)
Route::get('/registration/{registration}/dashboard', [RegistrationController::class, 'dashboard'])->name('registration.dashboard');
Route::get('/registration/{registration}/form-pria', [RegistrationController::class, 'formPria'])->name('registration.form.pria');
Route::post('/registration/{registration}/nik/{nik}/check', [RegistrationController::class, 'checkNik'])->name('registration.check.nik');
Route::post('/registration/{registration}/pria', [RegistrationController::class, 'storePria'])->name('registration.store-pria');
Route::get('/registration/{registration}/form-akad', [RegistrationController::class, 'formAkad'])->name('registration.form.akad');
Route::post('/registration/{registration}/akad', [RegistrationController::class, 'storeAkad'])->name('registration.store-akad');
Route::get('/registration/{registration}/form-wali-saksi', [RegistrationController::class, 'formWaliSaksi'])->name('registration.form.wali-saksi');
Route::post('/registration/{registration}/wali-saksi', [RegistrationController::class, 'storeWaliSaksi'])->name('registration.store-wali-saksi');
Route::get('/registration/{registration}/review', [RegistrationController::class, 'review'])->name('registration.review');
Route::get('/registration/{registration}/cetak/{template}', [RegistrationController::class, 'cetakDokumen'])->name('registration.cetak');
Route::get('/registration/{registration}/cetak-pdf/{template}', [RegistrationController::class, 'cetakDokumen'])->name('registration.cetak-pdf');
Route::get('/registration/{registration}/cetak-pdf-dokumen/{template}', [RegistrationController::class, 'cetakDokumen'])->name('registration.cetak.dokumen');
Route::post('/registration/{registration}/finalize', [RegistrationController::class, 'finalize'])->name('registration.finalize');
Route::delete('/registration/{registration}', [RegistrationController::class, 'destroy'])->name('registration.destroy');
Route::get('/registration/{registration}/preview', [RegistrationController::class, 'preview'])->name('registration.preview');

// Catch-all show route (must be last)
Route::get('/registration/{registration}', [RegistrationController::class, 'show'])->name('registration.show');

// ---------- DocumentsController ----------
Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.index');
Route::get('/documents/preview/{template}', [DocumentsController::class, 'preview'])->name('documents.preview');

// ---------- SettingsController ----------
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
Route::get('/settings/backup', [SettingsController::class, 'backupDatabase'])->name('settings.backup');
Route::get('/settings/backup-status', [SettingsController::class, 'backupStatus'])->name('settings.backup.status');
Route::post('/settings/restore', [SettingsController::class, 'restoreDatabase'])->name('settings.restore');

// ---------- ResidentsController ----------
Route::get('/residents', [ResidentsController::class, '__invoke'])->name('residents.index');
Route::post('/residents', [ResidentsController::class, 'store'])->name('residents.store');
Route::delete('/residents/{warga}', [ResidentsController::class, 'destroy'])->name('residents.destroy');

// echo "Routes registered successfully.\n";
