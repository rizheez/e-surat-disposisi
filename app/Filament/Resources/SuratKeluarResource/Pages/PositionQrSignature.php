<?php

namespace App\Filament\Resources\SuratKeluarResource\Pages;

use App\Filament\Resources\SuratKeluarResource;
use App\Models\SuratKeluar;
use App\Services\QrSignatureService;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Notifications\Notification;

class PositionQrSignature extends Page
{
    use InteractsWithRecord;

    protected static string $resource = SuratKeluarResource::class;

    protected string $view = 'filament.surat-keluar.position-qr-signature';

    protected static ?string $title = 'Posisi Tanda Tangan Digital';

    public string $qrDataUri = '';
    public int $posX = 450;
    public int $posY = 700;
    public int $qrSize = 96;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $service = new QrSignatureService();

        /** @var SuratKeluar $suratKeluar */
        $suratKeluar = $this->getRecord();

        // Generate QR if not yet
        if (!$suratKeluar->qr_token) {
            $suratKeluar->update([
                'qr_token' => $service->generateToken(),
            ]);
        }

        // Load saved position and size
        if ($suratKeluar->qr_position_x !== null) {
            $this->posX = $suratKeluar->qr_position_x;
        }
        if ($suratKeluar->qr_position_y !== null) {
            $this->posY = $suratKeluar->qr_position_y;
        }
        if ($suratKeluar->qr_size !== null) {
            $this->qrSize = $suratKeluar->qr_size;
        }

        $this->qrDataUri = $service->generateQrCode($suratKeluar->qr_token);
    }

    public function savePosition(int $x, int $y, int $size): void
    {
        $this->getRecord()->update([
            'qr_position_x' => $x,
            'qr_position_y' => $y,
            'qr_size' => $size,
        ]);

        $this->posX = $x;
        $this->posY = $y;
        $this->qrSize = $size;

        Notification::make()
            ->title('Posisi QR tersimpan')
            ->success()
            ->send();
    }

    public function approveAndSave(): void
    {
        /** @var SuratKeluar $suratKeluar */
        $suratKeluar = $this->getRecord();

        $suratKeluar->update([
            'status' => 'approved',
            'approved_at' => now(),
            'qr_position_x' => $this->posX,
            'qr_position_y' => $this->posY,
            'qr_size' => $this->qrSize,
        ]);

        Notification::make()
            ->title('Surat disetujui & QR ditempatkan')
            ->body("Nomor: {$suratKeluar->nomor_surat}")
            ->success()
            ->send();

        $this->redirect(SuratKeluarResource::getUrl('index'));
    }
}
