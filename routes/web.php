<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\InternalCatinController;
use App\Http\Controllers\InternalRegionController;
use App\Http\Controllers\N6Controller;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RegistrationDocumentController;
use App\Http\Controllers\RegistrationDocumentNumberController;
use App\Http\Controllers\RegistrationKtpCopyController;
use App\Http\Controllers\Settings\DocumentNumberSettingController;
use App\Http\Controllers\VillageProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->middleware('throttle:60,1');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('throttle:60,1')->name('dashboard');
Route::get('/help', [HelpController::class, 'index'])->middleware('throttle:60,1')->name('help.index');
Route::get('/panduan', [HelpController::class, 'index'])->middleware('throttle:60,1')->name('help.panduan');

Route::get('/n6/manual', [N6Controller::class, 'manualIndex'])->middleware('throttle:60,1')->name('n6.manual.index');
Route::get('/n6/manual/create', [N6Controller::class, 'manualCreate'])->middleware('throttle:60,1')->name('n6.manual.create');

Route::get('/settings/village-profile', [VillageProfileController::class, 'edit'])
    ->middleware('throttle:60,1')->name('settings.village-profile.edit');
Route::put('/settings/village-profile', [VillageProfileController::class, 'update'])
    ->middleware('throttle:30,1')->name('settings.village-profile.update');
Route::get('/settings/document-numbers', [DocumentNumberSettingController::class, 'edit'])
    ->middleware('throttle:60,1')->name('settings.document-numbers.edit');
Route::put('/settings/document-numbers', [DocumentNumberSettingController::class, 'update'])
    ->middleware('throttle:30,1')->name('settings.document-numbers.update');

Route::get('/backups', [BackupController::class, 'index'])->middleware('throttle:60,1')->name('backups.index');
Route::post('/backups', [BackupController::class, 'store'])->middleware('throttle:10,1')->name('backups.store');
Route::post('/backups/restore', [BackupController::class, 'restore'])->middleware('throttle:10,1')->name('backups.restore');
Route::post('/backups/dismiss', [BackupController::class, 'dismiss'])->middleware('throttle:60,1')->name('backups.dismiss');
Route::get('/backups/download/{filename}', [BackupController::class, 'download'])->middleware('throttle:60,1')->name('backups.download');

Route::get('/pendaftaran/baru', [RegistrationController::class, 'create'])->middleware('throttle:60,1')->name('registrations.create');
Route::post('/pendaftaran', [RegistrationController::class, 'store'])->middleware('throttle:30,1')->name('registrations.store');
Route::get('/registrations/{registration}/success', [RegistrationController::class, 'success'])->middleware('throttle:60,1')->name('registrations.success');

Route::prefix('internal/regions')->middleware('throttle:120,1')->group(function () {
    Route::get('/regencies', [InternalRegionController::class, 'regencies']);
    Route::get('/districts', [InternalRegionController::class, 'districts']);
    Route::get('/villages', [InternalRegionController::class, 'villages']);
});
Route::post('/internal/catins/lookup', [InternalCatinController::class, 'lookup'])->middleware('throttle:30,1')->name('internal.catins.lookup');

Route::get('/registrations/{registration}', [RegistrationDocumentController::class, 'show'])->middleware('throttle:60,1')->name('registrations.detail');
Route::get('/registrations/{registration}/documents/data-registration', [RegistrationDocumentController::class, 'printRegistrationData'])->middleware('throttle:60,1')->name('registrations.documents.data');
Route::get('/registrations/{registration}/documents/n1/print', [RegistrationDocumentController::class, 'printN1'])->middleware('throttle:60,1')->name('registrations.documents.n1.print');
Route::get('/registrations/{registration}/documents/n2/print', [RegistrationDocumentController::class, 'printN2'])->middleware('throttle:60,1')->name('registrations.documents.n2.print');
Route::get('/registrations/{registration}/documents/n4/print', [RegistrationDocumentController::class, 'printN4'])->middleware('throttle:60,1')->name('registrations.documents.n4.print');
Route::get('/registrations/{registration}/documents/n5/print', [RegistrationDocumentController::class, 'printN5'])->middleware('throttle:60,1')->name('registrations.documents.n5.print');
Route::get('/registrations/{registration}/documents/n6/print', [RegistrationDocumentController::class, 'printN6'])->middleware('throttle:60,1')->name('registrations.documents.n6.print');
Route::get('/registrations/{registration}/documents/surat-pernyataan-wali/print', [RegistrationDocumentController::class, 'printSuratPernyataanWali'])->middleware('throttle:60,1')->name('registrations.documents.surat-pernyataan-wali.print');
Route::get('/registrations/{registration}/documents/surat-pernyataan-status/{catinRole}/print', [RegistrationDocumentController::class, 'printSuratPernyataanStatus'])->middleware('throttle:60,1')->name('registrations.documents.surat-pernyataan-status.print');
Route::get('/registrations/{registration}/documents/numbers', [RegistrationDocumentNumberController::class, 'edit'])->middleware('throttle:60,1')->name('registrations.documents.numbers.edit');
Route::put('/registrations/{registration}/documents/numbers', [RegistrationDocumentNumberController::class, 'update'])->middleware('throttle:30,1')->name('registrations.documents.numbers.update');
Route::get('/registrations/{registration}/ktp', [RegistrationKtpCopyController::class, 'edit'])->middleware('throttle:60,1')->name('registrations.ktp.edit');
Route::put('/registrations/{registration}/ktp', [RegistrationKtpCopyController::class, 'update'])->middleware('throttle:30,1')->name('registrations.ktp.update');
Route::get('/registrations/{registration}/ktp/file/{slot}', [RegistrationKtpCopyController::class, 'file'])->middleware('throttle:60,1')->name('registrations.ktp.file');
Route::delete('/registrations/{registration}/ktp/{slot}', [RegistrationKtpCopyController::class, 'destroy'])->middleware('throttle:30,1')->name('registrations.ktp.destroy');
Route::get('/registrations/{registration}/ktp/print', [RegistrationKtpCopyController::class, 'print'])->middleware('throttle:60,1')->name('registrations.ktp.print');

Route::prefix('n6')->name('n6.')->middleware('throttle:60,1')->group(function () {
    Route::get('/create/{registration}', [N6Controller::class, 'create'])->name('create');
    Route::post('/store', [N6Controller::class, 'store'])->name('store')->middleware('throttle:10,1');
    Route::get('/{n6}', [N6Controller::class, 'show'])->name('show');
    Route::get('/{n6}/edit', [N6Controller::class, 'edit'])->name('edit');
    Route::get('/{n6}/print', [N6Controller::class, 'print'])->name('print');
    Route::put('/{n6}', [N6Controller::class, 'update'])->name('update')->middleware('throttle:30,1');
    Route::delete('/{n6}', [N6Controller::class, 'destroy'])->name('destroy')->middleware('throttle:30,1');
});

Route::get('/registrations/{registration}/edit', [RegistrationController::class, 'edit'])->middleware('throttle:60,1')->name('registrations.edit');
Route::put('/registrations/{registration}', [RegistrationController::class, 'update'])->middleware('throttle:60,1')->name('registrations.update');
Route::patch('/registrations/{registration}/autosave', [RegistrationController::class, 'autosave'])->middleware('throttle:120,1')->name('registrations.autosave');
Route::delete('/registrations/{registration}', [RegistrationController::class, 'destroy'])->middleware('throttle:30,1')->name('registrations.destroy');
