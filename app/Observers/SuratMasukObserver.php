<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\SuratMasuk;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SuratMasukObserver
{
    public function created(SuratMasuk $suratMasuk): void
    {
        $users = User::role([
            'admin',
            ...User::leadershipRoleNames(),
            'staf_administrasi',
        ])->get();

        if (filled($suratMasuk->penerima)) {
            $users->push(User::find($suratMasuk->penerima));
        }

        $this->sendToUsers(
            users: $users,
            notification: Notification::make()
                ->title('Surat Masuk Baru')
                ->body("Surat dari {$suratMasuk->pengirim}: {$suratMasuk->perihal}")
                ->icon('heroicon-o-envelope')
                ->iconColor('info'),
        );
    }

    public function updated(SuratMasuk $suratMasuk): void
    {
        if ($suratMasuk->wasChanged('status')) {
            $statusLabel = match ($suratMasuk->status) {
                'diterima' => 'Diterima',
                'dibaca' => 'Dibaca',
                'didisposisi' => 'Didisposisi',
                'diproses' => 'Sedang Diproses',
                'selesai' => 'Selesai',
                default => $suratMasuk->status,
            };

            $users = collect();

            if (filled($suratMasuk->created_by)) {
                $users->push(User::find($suratMasuk->created_by));
            }

            if (filled($suratMasuk->penerima)) {
                $users->push(User::find($suratMasuk->penerima));
            }

            $this->sendToUsers(
                users: $users,
                notification: Notification::make()
                    ->title("Status Surat Masuk: {$statusLabel}")
                    ->body("Surat {$suratMasuk->nomor_surat} - {$suratMasuk->perihal}")
                    ->icon('heroicon-o-envelope')
                    ->iconColor($suratMasuk->status === 'selesai' ? 'success' : 'warning'),
            );
        }
    }

    private function sendToUsers(Collection $users, Notification $notification): void
    {
        $users
            ->filter(fn (?User $user): bool => filled($user) && $user->id !== Auth::id())
            ->unique('id')
            ->each(fn (User $user): mixed => $notification->sendToDatabase($user));
    }
}
