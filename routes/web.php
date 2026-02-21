<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/pdf/surat-keluar/{suratKeluar}', [PdfController::class, 'suratKeluar'])
        ->name('pdf.surat-keluar');
    Route::get('/pdf/surat-keluar/{suratKeluar}/preview', [PdfController::class, 'suratKeluarPreview'])
        ->name('pdf.surat-keluar.preview');
});
