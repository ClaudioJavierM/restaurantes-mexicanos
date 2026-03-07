<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RestaurantTeamMember extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'role',
        'is_primary',
        'invited_by',
        'team_request_id',
        'accepted_at',
        'status',
        'permissions',
        'invitation_token',
        'invitation_expires_at',
        'revoked_at',
        'revoked_reason',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'accepted_at' => 'datetime',
        'invitation_expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'permissions' => 'array',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EDITOR = 'editor';
    const ROLE_VIEWER = 'viewer';

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_REVOKED = 'revoked';

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function teamRequest(): BelongsTo
    {
        return $this->belongsTo(TeamRequest::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function canEdit(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_EDITOR]);
    }

    public function canViewAnalytics(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_VIEWER]);
    }

    public function canManageTeam(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->is_primary;
    }

    public static function getRoleLabel(string $role): string
    {
        return match($role) {
            self::ROLE_ADMIN => 'Administrador',
            self::ROLE_MANAGER => 'Gerente',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_VIEWER => 'Solo Lectura',
            default => $role,
        };
    }

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrador',
            self::ROLE_MANAGER => 'Gerente',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_VIEWER => 'Solo Lectura',
        ];
    }

    public static function generateInvitationToken(): string
    {
        return Str::random(64);
    }

    public function revoke(?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REVOKED,
            'revoked_at' => now(),
            'revoked_reason' => $reason,
        ]);
    }

    public function accept(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'accepted_at' => now(),
            'invitation_token' => null,
        ]);
    }
}
