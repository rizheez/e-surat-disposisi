<?php

namespace App\Observers;

use App\Models\SuratMasuk;
use App\Models\User;
use Filament\Notifications\Notification;

class SuratMasukObserver
{
    public function created(SuratMasuk $suratMasuk): void
    {
        // Notify pimpinan and sekretaris when new surat masuk arrives
        $users = User::role(['pimpinan', 'sekretaris'])->get();

        foreach ($users as $user) {
            Notification::make()
                ->title('Surat Masuk Baru')
                ->body("Surat dari {$suratMasuk->pengirim}: {$suratMasuk->perihal}")
                ->icon('heroicon-o-envelope')
                ->iconColor('info')
                ->sendToDatabase($user);
        }
    }

    public function updated(SuratMasuk $suratMasuk): void
    {
        if ($suratMasuk->isDirty('status')) {
            $statusLabel = match ($suratMasuk->status) {
                'diterima' => 'Diterima',
                'didisposisi' => 'Didisposisi',
                'diproses' => 'Sedang Diproses',
                'selesai' => 'Selesai',
                default => $suratMasuk->status,
            };

            // Notify the creator
            if ($suratMasuk->created_by) {
                $pembuat = User::find($suratMasuk->created_by);
                if ($pembuat) {
                    Notification::make()
                        ->title("Status Surat Masuk: {$statusLabel}")
                        ->body("Surat {$suratMasuk->nomor_surat} - {$suratMasuk->perihal}")
                        ->icon('heroicon-o-envelope')
                        ->iconColor('success')
                        ->sendToDatabase($pembuat);
                }
            }
        }
    }
}
