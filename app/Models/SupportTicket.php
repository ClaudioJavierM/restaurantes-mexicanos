<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'name',
        'email',
        'phone',
        'restaurant_name',
        'restaurant_slug',
        'issue_type',
        'message',
        'status',
        'source',
        'context',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'context' => 'array',
        'resolved_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (SupportTicket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'FAMER-' . strtoupper(substr(uniqid(), -5));
            }
        });
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }
}
