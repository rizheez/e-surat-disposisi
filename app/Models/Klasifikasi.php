<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klasifikasi extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'kode_surat',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function suratMasuks(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'klasifikasi');
    }

    public function suratKeluars(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'klasifikasi');
    }

    /**
     * Get display label: "01.1 - Urusan Umum (Internal)"
     */
    public function getLabelAttribute(): string
    {
        return "{$this->kode} - {$this->nama}";
    }
}
