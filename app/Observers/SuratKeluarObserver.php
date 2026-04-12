<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\GeneratedNomorSurat;
use App\Models\SuratKeluar;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SuratKeluarObserver
{
    public function created(SuratKeluar $suratKeluar): void
    {
        $this->markGeneratedNomorAsUsed($suratKeluar);
    }

    public function updated(SuratKeluar $suratKeluar): void
    {
        if ($suratKeluar->wasChanged('nomor_surat')) {
            $this->releasePreviousGeneratedNomor($suratKeluar);
            $this->markGeneratedNomorAsUsed($suratKeluar);
        }

        if ($suratKeluar->wasChanged('status')) {
            $statusLabel = match ($suratKeluar->status) {
                'draft' => 'Draft',
                'review' => 'Menunggu Review',
                'approved' => 'Disetujui',
                'sent' => 'Terkirim',
                default => $suratKeluar->status,
            };

            if (filled($suratKeluar->pembuat_id)) {
                $this->sendToUsers(
                    users: collect([User::find($suratKeluar->pembuat_id)]),
                    notification: Notification::make()
                        ->title("Surat Keluar: {$statusLabel}")
                        ->body("{$suratKeluar->nomor_surat} - {$suratKeluar->perihal}")
                        ->icon('heroicon-o-paper-clip')
                        ->iconColor($suratKeluar->status === 'approved' ? 'success' : 'warning'),
                );
            }

            if ($suratKeluar->status === 'review') {
                $reviewers = collect();

                if (filled($suratKeluar->penandatangan_id)) {
                    $reviewers->push(User::find($suratKeluar->penandatangan_id));
                }

                if ($reviewers->filter()->isEmpty()) {
                    $reviewers = User::role(User::leadershipRoleNames())->get();
                }

                $this->sendToUsers(
                    users: $reviewers,
                    notification: Notification::make()
                        ->title('Surat Keluar Menunggu Review')
                        ->body("{$suratKeluar->nomor_surat} - {$suratKeluar->perihal}")
                        ->icon('heroicon-o-clipboard-document-check')
                        ->iconColor('warning'),
                );
            }
        }
    }

    private function markGeneratedNomorAsUsed(SuratKeluar $suratKeluar): void
    {
        if (! Schema::hasTable('generated_nomor_surats') || blank($suratKeluar->nomor_surat)) {
            return;
        }

        GeneratedNomorSurat::query()
            ->where('nomor_surat', $suratKeluar->nomor_surat)
            ->where(function ($query) use ($suratKeluar): void {
                $query
                    ->where('status', 'reserved')
                    ->orWhere('surat_keluar_id', $suratKeluar->getKey());
            })
            ->update([
                'status' => 'used',
                'used_at' => now(),
                'used_by_id' => $suratKeluar->pembuat_id,
                'surat_keluar_id' => $suratKeluar->getKey(),
            ]);
    }

    private function releasePreviousGeneratedNomor(SuratKeluar $suratKeluar): void
    {
        $previousNomor = $suratKeluar->getOriginal('nomor_surat');

        if (! Schema::hasTable('generated_nomor_surats') || blank($previousNomor)) {
            return;
        }

        GeneratedNomorSurat::query()
            ->where('nomor_surat', $previousNomor)
            ->where('surat_keluar_id', $suratKeluar->getKey())
            ->update([
                'status' => 'reserved',
                'used_at' => null,
                'used_by_id' => null,
                'surat_keluar_id' => null,
            ]);
    }

    private function sendToUsers(Collection $users, Notification $notification): void
    {
        $users
            ->filter(fn (?User $user): bool => filled($user) && $user->id !== Auth::id())
            ->unique('id')
            ->each(fn (User $user): mixed => $notification->sendToDatabase($user));
    }
}
