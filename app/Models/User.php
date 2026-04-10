<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Laravel\Sanctum\HasApiTokens;
use Filament\Panel;
use App\Notifications\BilingualVerifyEmail;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'google_id',
        'facebook_id',
        'avatar',
        'provider',
        'sms_marketing_consent',
        'sms_consent_at',
        'sms_opted_out_at',
        'newsletter_subscribed',
        'newsletter_subscribed_at',
        'listmonk_subscriber_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sms_marketing_consent'  => 'boolean',
            'sms_consent_at'         => 'datetime',
            'sms_opted_out_at'       => 'datetime',
            'newsletter_subscribed'  => 'boolean',
            'newsletter_subscribed_at' => 'datetime',
        ];
    }

    /**
     * Opt-in to SMS marketing
     */
    public function optInToSmsMarketing(): void
    {
        $this->update([
            'sms_marketing_consent' => true,
            'sms_consent_at' => now(),
            'sms_opted_out_at' => null,
        ]);
    }

    /**
     * Opt-out from SMS marketing
     */
    public function optOutFromSmsMarketing(): void
    {
        $this->update([
            'sms_marketing_consent' => false,
            'sms_opted_out_at' => now(),
        ]);
    }

    /**
     * Check if user can receive SMS marketing
     */
    public function canReceiveSmsMarketing(): bool
    {
        return $this->sms_marketing_consent 
            && $this->phone 
            && !$this->sms_opted_out_at;
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function ownedRestaurants()
    {
        return $this->hasMany(Restaurant::class);
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteRestaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'favorites')->withTimestamps();
    }

    public function hasFavorited(Restaurant $restaurant): bool
    {
        return $this->favoriteRestaurants()->where('restaurant_id', $restaurant->id)->exists();
    }

    // Team memberships
    public function teamMemberships()
    {
        return $this->hasMany(RestaurantTeamMember::class);
    }

    public function activeTeamMemberships()
    {
        return $this->teamMemberships()->where('status', 'active');
    }

    public function teamRestaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_team')
            ->withPivot(['role', 'permissions', 'status'])
            ->wherePivot('status', 'active');
    }

    public function allAccessibleRestaurants()
    {
        // Owned restaurants + team restaurants
        return Restaurant::where('user_id', $this->id)
            ->orWhereHas('teamMembers', function ($q) {
                $q->where('user_id', $this->id)
                  ->where('status', 'active');
            });
    }

    public function hasAccessToRestaurant(Restaurant $restaurant): bool
    {
        // Is the owner
        if ($restaurant->user_id === $this->id) {
            return true;
        }

        // Is a team member
        return $restaurant->teamMembers()
            ->where('user_id', $this->id)
            ->where('status', 'active')
            ->exists();
    }

    public function getRoleForRestaurant(Restaurant $restaurant): ?string
    {
        // Primary owner
        if ($restaurant->user_id === $this->id) {
            return 'owner';
        }

        // Team member
        $membership = $restaurant->teamMembers()
            ->where('user_id', $this->id)
            ->where('status', 'active')
            ->first();

        return $membership?->role;
    }

    public function canManageTeamFor(Restaurant $restaurant): bool
    {
        // Only owners can manage team
        return $this->getRoleForRestaurant($restaurant) === 'owner';
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin panel - only admins
        if ($panel->getId() === 'admin') {
            return $this->role === 'admin';
        }

        // Owner panel - owners with restaurants OR team members with active memberships
        if ($panel->getId() === 'owner') {
            if (!$this->hasVerifiedEmail()) {
                return false;
            }

            // Is a restaurant owner
            if (($this->role === 'owner' || $this->role === 'admin') && $this->restaurants()->exists()) {
                return true;
            }

            // Has active team memberships (manager, staff, or additional owner)
            return $this->activeTeamMemberships()->exists();
        }

        return false;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a restaurant owner.
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new BilingualVerifyEmail);
    }

    /**
     * Send the password reset notification with FAMER branding.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
