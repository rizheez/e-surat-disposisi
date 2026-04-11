<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratKeluarFileController extends Controller
{
    public function __invoke(SuratKeluar $suratKeluar): StreamedResponse
    {
        Gate::authorize('view', $suratKeluar);

        abort_if(blank($suratKeluar->file_path), 404);

        $filePath = (string) $suratKeluar->file_path;

        abort_if(str_contains($filePath, '..'), 404);

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($filePath)) {
                return Storage::disk($disk)->download($filePath);
            }
        }

        abort(404);
    }
}
