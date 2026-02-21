<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratKeluar extends Model
{
    protected $fillable = [
        'nomor_surat',
        'tanggal',
        'perihal',
        'tujuan',
        'isi_surat',
        'file_path',
        'status',
        'created_by',
        'unit_kerja_id',
        'approved_by',
        'template_surat_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function templateSurat(): BelongsTo
    {
        return $this->belongsTo(TemplateSurat::class);
    }

    /**
     * Generate nomor surat keluar otomatis.
     * Format: SK/001/II/2026
     */
    public static function generateNomorSurat(): string
    {
        $tahun = date('Y');
        $bulan = SuratMasuk::getRomanMonth((int) date('m'));

        $lastSurat = self::whereYear('created_at', $tahun)
            ->whereMonth('created_at', (int) date('m'))
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSurat) {
            preg_match('/SK\/(\d+)\//', $lastSurat->nomor_surat, $matches);
            $nextNumber = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('SK/%03d/%s/%s', $nextNumber, $bulan, $tahun);
    }

    protected static function booted(): void
    {
        static::creating(function (SuratKeluar $suratKeluar) {
            if (empty($suratKeluar->nomor_surat)) {
                $suratKeluar->nomor_surat = self::generateNomorSurat();
            }
        });
    }
}
