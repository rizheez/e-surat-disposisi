<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Disposisi extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'instruksi', 'ke_user_id', 'ke_unit_id', 'parent_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Disposisi {$eventName}")
            ->useLogName('disposisi');
    }
    protected $fillable = [
        'surat_masuk_id',
        'dari_user_id',
        'ke_user_id',
        'ke_unit_id',
        'instruksi',
        'catatan',
        'batas_waktu',
        'status',
        'is_tembusan',
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

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive', 'keUser', 'keUnit', 'dariUser');
    }

    /**
     * Get the disposisi level (1 = root, 2 = first forward, etc.)
     */
    public function getLevel(): int
    {
        $level = 1;
        $current = $this;
        while ($current->parent_id) {
            $level++;
            $current = $current->parent;
        }
        return $level;
    }

    /**
     * Get the root disposisi of this chain.
     */
    public function getRoot(): self
    {
        $current = $this;
        while ($current->parent_id) {
            $current = $current->parent;
        }
        return $current;
    }

    /**
     * Get full chain from root with all children recursively loaded.
     */
    public function getFullChain(): self
    {
        $root = $this->getRoot();
        $root->load('childrenRecursive', 'dariUser', 'keUser', 'keUnit', 'suratMasuk');
        return $root;
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
