<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Services\QrSignatureService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class PdfController extends Controller
{
    public function suratKeluar(SuratKeluar $suratKeluar): \Symfony\Component\HttpFoundation\Response
    {
        File::ensureDirectoryExists(storage_path('fonts'));

        $suratKeluar->load(['pembuat', 'penandatangan', 'suratMasuk']);

        $qrDataUri = null;
        if ($suratKeluar->qr_token) {
            $service = new QrSignatureService;
            $qrDataUri = $service->generateQrCode($suratKeluar->qr_token, 4);
        }

        $pdf = Pdf::loadView('pdf.surat-keluar', compact('suratKeluar', 'qrDataUri'))
            ->setPaper('a4', 'portrait');

        $filename = str_replace('/', '-', $suratKeluar->nomor_surat).'.pdf';

        return $pdf->download($filename);
    }

    public function suratKeluarPreview(SuratKeluar $suratKeluar): \Symfony\Component\HttpFoundation\Response
    {
        File::ensureDirectoryExists(storage_path('fonts'));

        $suratKeluar->load(['pembuat', 'penandatangan', 'suratMasuk']);

        $qrDataUri = null;
        if ($suratKeluar->qr_token) {
            $service = new QrSignatureService;
            $qrDataUri = $service->generateQrCode($suratKeluar->qr_token, 4);
        }

        $pdf = Pdf::loadView('pdf.surat-keluar', compact('suratKeluar', 'qrDataUri'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }
}
