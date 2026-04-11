<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedNomorSurat extends Model
{
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'klasifikasi',
        'tujuan',
        'perihal',
        'sifat_surat',
        'keterangan',
        'status',
        'generated_by',
        'used_at',
        'used_by_id',
        'surat_keluar_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'used_at' => 'datetime',
        ];
    }

    public function klasifikasiSurat(): BelongsTo
    {
        return $this->belongsTo(Klasifikasi::class, 'klasifikasi');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_id');
    }

    public function suratKeluar(): BelongsTo
    {
        return $this->belongsTo(SuratKeluar::class);
    }
}
