<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'accepted_at' => 'datetime',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EDITOR = 'editor';
    const ROLE_VIEWER = 'viewer';

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
}
