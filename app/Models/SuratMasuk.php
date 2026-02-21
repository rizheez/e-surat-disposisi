<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratMasuk extends Model
{
    protected $fillable = [
        'nomor_agenda',
        'nomor_surat',
        'tanggal_surat',
        'tanggal_terima',
        'pengirim',
        'perihal',
        'klasifikasi',
        'file_path',
        'status',
        'created_by',
        'unit_tujuan_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tanggal_terima' => 'date',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function unitTujuan(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class, 'unit_tujuan_id');
    }

    public function disposisis(): HasMany
    {
        return $this->hasMany(Disposisi::class);
    }

    /**
     * Generate nomor agenda otomatis.
     * Format: SM/001/II/2026
     */
    public static function generateNomorAgenda(): string
    {
        $tahun = date('Y');
        $bulan = self::getRomanMonth((int) date('m'));

        $lastSurat = self::whereYear('created_at', $tahun)
            ->whereMonth('created_at', (int) date('m'))
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSurat) {
            // Extract the number from format SM/001/II/2026
            preg_match('/SM\/(\d+)\//', $lastSurat->nomor_agenda, $matches);
            $nextNumber = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('SM/%03d/%s/%s', $nextNumber, $bulan, $tahun);
    }

    public static function getRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romans[$month] ?? 'I';
    }

    protected static function booted(): void
    {
        static::creating(function (SuratMasuk $suratMasuk) {
            if (empty($suratMasuk->nomor_agenda)) {
                $suratMasuk->nomor_agenda = self::generateNomorAgenda();
            }
        });
    }
}
