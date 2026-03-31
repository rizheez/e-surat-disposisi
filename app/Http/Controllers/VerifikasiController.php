<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;

class VerifikasiController extends Controller
{
    public function show(string $token)
    {
        $surat = SuratKeluar::where('qr_token', $token)
            ->with(['pembuat', 'penandatangan'])
            ->first();

        if (!$surat) {
            return view('verifikasi', [
                'valid' => false,
                'surat' => null,
            ]);
        }

        return view('verifikasi', [
            'valid' => true,
            'surat' => $surat,
        ]);
    }
}
