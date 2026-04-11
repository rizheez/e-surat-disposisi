<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    private const ADMIN_ROLE = 'admin';

    private const LEADERSHIP_ROLES = [
        'rektor',
        'wr',
        'kabiro',
        'dekan',
        'kaprodi',
    ];

    private const PANEL_ROLES = [
        self::ADMIN_ROLE,
        'rektor',
        'wr',
        'kabiro',
        'dekan',
        'kaprodi',
        'staf_administrasi',
        'staf',
        'dosen',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'jabatan',
        'unit_kerja_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(self::PANEL_ROLES);
    }

    public function canManageSuratMasuk(): bool
    {
        return $this->hasAnyRole([self::ADMIN_ROLE, 'staf_administrasi']);
    }

    public function canCreateSuratKeluar(): bool
    {
        return $this->hasAnyRole(self::PANEL_ROLES);
    }

    public function canManageDisposisi(): bool
    {
        return $this->hasAnyRole([
            self::ADMIN_ROLE,
            ...self::LEADERSHIP_ROLES,
        ]);
    }

    public function isAdminRole(): bool
    {
        return $this->hasRole(self::ADMIN_ROLE);
    }

    public static function leadershipRoleNames(): array
    {
        return self::LEADERSHIP_ROLES;
    }

    public static function panelRoleNames(): array
    {
        return self::PANEL_ROLES;
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function disposisisDari(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'dari_user_id');
    }

    public function disposisisKe(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'ke_user_id');
    }

    public function suratMasuks(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'created_by');
    }

    public function suratKeluars(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'created_by');
    }
}
