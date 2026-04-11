<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\GeneratedNomorSurat;
use App\Models\SuratKeluar;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SuratKeluarObserver
{
    public function created(SuratKeluar $suratKeluar): void
    {
        if (! Schema::hasTable('generated_nomor_surats')) {
            return;
        }

        GeneratedNomorSurat::query()
            ->where('nomor_surat', $suratKeluar->nomor_surat)
            ->where('status', 'reserved')
            ->update([
                'status' => 'used',
                'used_at' => now(),
                'used_by_id' => $suratKeluar->pembuat_id,
                'surat_keluar_id' => $suratKeluar->getKey(),
            ]);
    }

    public function updated(SuratKeluar $suratKeluar): void
    {
        if ($suratKeluar->isDirty('status')) {
            $statusLabel = match ($suratKeluar->status) {
                'draft' => 'Draft',
                'review' => 'Menunggu Review',
                'approved' => 'Disetujui',
                'sent' => 'Terkirim',
                default => $suratKeluar->status,
            };

            // Notify pembuat when status changes (e.g., approved, rejected)
            if ($suratKeluar->pembuat_id && $suratKeluar->pembuat_id !== Auth::id()) {
                $pembuat = User::find($suratKeluar->pembuat_id);
                if ($pembuat) {
                    Notification::make()
                        ->title("Surat Keluar: {$statusLabel}")
                        ->body("{$suratKeluar->nomor_surat} - {$suratKeluar->perihal}")
                        ->icon('heroicon-o-paper-clip')
                        ->iconColor($suratKeluar->status === 'approved' ? 'success' : 'warning')
                        ->sendToDatabase($pembuat);
                }
            }

            // Notify pimpinan when surat submitted for review
            if ($suratKeluar->status === 'review') {
                $pimpinans = User::role('pimpinan')->get();
                foreach ($pimpinans as $pimpinan) {
                    Notification::make()
                        ->title('Surat Keluar Menunggu Review')
                        ->body("{$suratKeluar->perihal}")
                        ->icon('heroicon-o-clipboard-document-check')
                        ->iconColor('warning')
                        ->sendToDatabase($pimpinan);
                }
            }
        }
    }
}
