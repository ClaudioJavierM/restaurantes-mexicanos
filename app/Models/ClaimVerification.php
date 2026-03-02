<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ClaimVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'owner_name',
        'owner_email',
        'owner_phone',
        'verification_method',
        'verification_code',
        'code_sent_at',
        'code_expires_at',
        'verification_attempts',
        'is_verified',
        'verified_at',
        'document_path',
        'status',
        'rejection_reason',
        'facebook_url',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'code_sent_at' => 'datetime',
        'code_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Generate verification code
    public static function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // Create new verification request
    public static function createVerification(
        Restaurant $restaurant,
        string $ownerName,
        string $ownerEmail,
        string $ownerPhone,
        string $method = 'email'
    ): self {
        $code = self::generateCode();

        return self::create([
            'restaurant_id' => $restaurant->id,
            'owner_name' => $ownerName,
            'owner_email' => $ownerEmail,
            'owner_phone' => $ownerPhone,
            'verification_method' => $method,
            'verification_code' => $code,
            'code_sent_at' => now(),
            'code_expires_at' => now()->addMinutes(30),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'pending',
        ]);
    }

    // Verify the code
    public function verifyCode(string $code): bool
    {
        // Check if already verified
        if ($this->is_verified) {
            return false;
        }

        // Check if expired
        if ($this->code_expires_at && $this->code_expires_at->isPast()) {
            $this->update(['status' => 'expired']);
            return false;
        }

        // Check attempts
        if ($this->verification_attempts >= 5) {
            $this->update(['status' => 'expired']);
            return false;
        }

        // Increment attempts
        $this->increment('verification_attempts');

        // Verify code
        if ($this->verification_code === $code) {
            $this->update([
                'is_verified' => true,
                'verified_at' => now(),
                'status' => 'verified',
            ]);
            return true;
        }

        return false;
    }

    // Resend verification code
    public function resendCode(): bool
    {
        // Can only resend if not verified and not expired
        if ($this->is_verified || $this->status === 'expired') {
            return false;
        }

        // Generate new code
        $newCode = self::generateCode();

        $this->update([
            'verification_code' => $newCode,
            'code_sent_at' => now(),
            'code_expires_at' => now()->addMinutes(30),
            'verification_attempts' => 0,
        ]);

        return true;
    }

    // Check if code is expired
    public function isExpired(): bool
    {
        return $this->code_expires_at && $this->code_expires_at->isPast();
    }

    // Get remaining attempts
    public function remainingAttempts(): int
    {
        return max(0, 5 - $this->verification_attempts);
    }

    // Approve claim (admin action)
    public function approve(): bool
    {
        if ($this->status !== 'verified') {
            return false;
        }

        // Create or find user account for the owner
        $user = \App\Models\User::firstOrCreate(
            ['email' => $this->owner_email],
            [
                'name' => $this->owner_name,
                'password' => bcrypt(Str::random(16)),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]
        );

        // Update user role if needed
        if ($user->role !== 'admin' && $user->role !== 'owner') {
            $user->update(['role' => 'owner']);
        }

        // Mark restaurant as claimed WITH user assignment
        $this->restaurant->update([
            'is_claimed' => true,
            'claimed_at' => now(),
            'user_id' => $user->id,
            'owner_name' => $this->owner_name,
            'owner_email' => $this->owner_email,
            'owner_phone' => $this->owner_phone,
            'verification_method' => $this->verification_method,
        ]);

        $this->update(['status' => 'approved']);

        return true;
    }

    // Reject claim (admin action)
    public function reject(string $reason): bool
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        return true;
    }
}
