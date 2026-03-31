<?php

namespace App\Observers;

use App\Models\SuratKeluar;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SuratKeluarObserver
{
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
