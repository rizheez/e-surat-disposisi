<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SuratKeluar extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nomor_surat', 'perihal', 'status', 'tanggal_surat', 'penandatangan_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Surat keluar {$eventName}")
            ->useLogName('surat-keluar');
    }

    protected $fillable = [
        'nomor_surat',
        'nomor_agenda',
        'tanggal_surat',
        'tujuan',
        'alamat_tujuan',
        'perihal',
        'lampiran',
        'isi_surat',
        'template_surat_id',
        'klasifikasi',
        'sifat_surat',
        'file_path',
        'surat_masuk_id',
        'status',
        'pembuat_id',
        'penandatangan_id',
        'tanggal_kirim',
        'keterangan',
        'tembusan',
        'qr_token',
        'approved_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tanggal_kirim' => 'date',
            'approved_at' => 'datetime',
            'archived_at' => 'datetime',
            'tembusan' => 'array',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penandatangan_id');
    }

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    public function templateSurat(): BelongsTo
    {
        return $this->belongsTo(TemplateSurat::class);
    }

    /**
     * Generate nomor surat keluar otomatis.
     * Format Umum: (Kode Jenis Surat)/(Nomor Urut)/UNU-KT/(bulan)/(tahun)
     * Format Khusus: (Nomor)/(Kode Surat)/UNU-KT/(bulan)/(tahun)
     */
    public static function generateNomorSurat(?int $klasifikasiId = null): string
    {
        $tahun = date('Y');
        $bulan = SuratMasuk::getRomanMonth((int) date('m'));

        // Get next sequence number
        $lastSurat = self::whereYear('created_at', $tahun)
            ->whereMonth('created_at', (int) date('m'))
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastSurat && $lastSurat->nomor_surat) {
            // Extract number from any format: look for /001/ or similar
            preg_match('/(\d{3})/', $lastSurat->nomor_surat, $matches);
            $nextNumber = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
        }

        $nomor = sprintf('%03d', $nextNumber);

        // Look up klasifikasi
        if ($klasifikasiId) {
            $klasifikasi = Klasifikasi::find($klasifikasiId);
            if ($klasifikasi) {
                // Special type (ND, SPTDD, S.Kep, etc): (nomor)/(kode_surat)/UNU-KT/(bulan)/(tahun)
                if ($klasifikasi->kode_surat) {
                    return "{$nomor}/{$klasifikasi->kode_surat}/UNU-KT/{$bulan}/{$tahun}";
                }
                // Normal type: (kode)/(nomor)/UNU-KT/(bulan)/(tahun)
                return "{$klasifikasi->kode}/{$nomor}/UNU-KT/{$bulan}/{$tahun}";
            }
        }

        // Fallback: generic format
        return "{$nomor}/UNU-KT/{$bulan}/{$tahun}";
    }

    protected static function booted(): void
    {
        static::creating(function (SuratKeluar $suratKeluar) {
            if (empty($suratKeluar->nomor_surat)) {
                $suratKeluar->nomor_surat = self::generateNomorSurat($suratKeluar->klasifikasi);
            }
        });
    }
}
