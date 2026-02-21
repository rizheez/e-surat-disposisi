<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disposisi extends Model
{
    protected $fillable = [
        'surat_masuk_id',
        'dari_user_id',
        'ke_user_id',
        'ke_unit_id',
        'instruksi',
        'catatan',
        'batas_waktu',
        'status',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'batas_waktu' => 'date',
        ];
    }

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    public function dariUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    public function keUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ke_user_id');
    }

    public function keUnit(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class, 'ke_unit_id');
    }

    public function balasans(): HasMany
    {
        return $this->hasMany(DisposisiBalasan::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Disposisi::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'parent_id');
    }

    /**
     * Auto-update surat masuk status when disposisi is created.
     */
    protected static function booted(): void
    {
        static::created(function (Disposisi $disposisi) {
            $suratMasuk = $disposisi->suratMasuk;
            if ($suratMasuk && $suratMasuk->status !== 'selesai') {
                $suratMasuk->update(['status' => 'didisposisi']);
            }
        });
    }
}
