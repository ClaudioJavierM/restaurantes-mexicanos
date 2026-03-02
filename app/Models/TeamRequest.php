<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeamRequest extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'requester_name',
        'requester_email',
        'requester_phone',
        'request_type',
        'requested_role',
        'message',
        'evidence_urls',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'token',
    ];

    protected $casts = [
        'evidence_urls' => 'array',
        'reviewed_at' => 'datetime',
    ];

    const TYPE_TEAM_JOIN = 'team_join';
    const TYPE_OWNERSHIP_DISPUTE = 'ownership_dispute';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DISPUTED = 'disputed';

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EDITOR = 'editor';
    const ROLE_VIEWER = 'viewer';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = Str::random(64);
            }
        });
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approve(User $reviewer, string $role = null, string $notes = null): bool
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
            'requested_role' => $role ?? $this->requested_role,
        ]);

        // Create or get user
        $user = User::firstOrCreate(
            ['email' => $this->requester_email],
            [
                'name' => $this->requester_name,
                'password' => bcrypt(Str::random(16)),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]
        );

        // Add to team
        RestaurantTeamMember::updateOrCreate(
            [
                'restaurant_id' => $this->restaurant_id,
                'user_id' => $user->id,
            ],
            [
                'role' => $role ?? $this->requested_role,
                'invited_by' => $reviewer->id,
                'team_request_id' => $this->id,
                'accepted_at' => now(),
            ]
        );

        return true;
    }

    public function reject(User $reviewer, string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isDispute(): bool
    {
        return $this->request_type === self::TYPE_OWNERSHIP_DISPUTE;
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

    public static function getRoleDescription(string $role): string
    {
        return match($role) {
            self::ROLE_ADMIN => 'Control total del restaurante',
            self::ROLE_MANAGER => 'Editar info, ver analytics, responder reseñas',
            self::ROLE_EDITOR => 'Solo editar información básica',
            self::ROLE_VIEWER => 'Ver analytics únicamente',
            default => '',
        };
    }
}
