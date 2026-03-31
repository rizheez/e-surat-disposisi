<?php

use App\Http\Controllers\PdfController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

// Public verification route (no auth required)
Route::get('/verifikasi/{token}', [VerifikasiController::class, 'show'])
    ->name('verifikasi.show')
    ->middleware('throttle:60,1');

Route::middleware('auth')->group(function () {
    Route::get('/pdf/surat-keluar/{suratKeluar}', [PdfController::class, 'suratKeluar'])
        ->name('pdf.surat-keluar');
    Route::get('/pdf/surat-keluar/{suratKeluar}/preview', [PdfController::class, 'suratKeluarPreview'])
        ->name('pdf.surat-keluar.preview');
});
