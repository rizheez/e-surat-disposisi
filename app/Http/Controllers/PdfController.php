<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function suratKeluar(SuratKeluar $suratKeluar): Response
    {
        $suratKeluar->load(['createdBy', 'unitKerja.parent', 'templateSurat']);

        $unitKerja = $suratKeluar->unitKerja;

        $pdf = Pdf::loadView('pdf.surat-keluar', compact('suratKeluar', 'unitKerja'))
            ->setPaper('a4', 'portrait');

        $filename = str_replace('/', '-', $suratKeluar->nomor_surat) . '.pdf';

        return $pdf->download($filename);
    }

    public function suratKeluarPreview(SuratKeluar $suratKeluar): Response
    {
        $suratKeluar->load(['createdBy', 'unitKerja.parent', 'templateSurat']);

        $unitKerja = $suratKeluar->unitKerja;

        $pdf = Pdf::loadView('pdf.surat-keluar', compact('suratKeluar', 'unitKerja'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }
}
