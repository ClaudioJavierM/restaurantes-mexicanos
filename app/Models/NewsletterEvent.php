<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterEvent extends Model
{
    protected $fillable = [
        'source', 'event_type', 'email', 'name',
        'subscriber_id', 'campaign_id', 'campaign_name',
        'link_url', 'message_id', 'list_name',
        'raw_payload', 'occurred_at',
    ];

    protected $casts = [
        'raw_payload'  => 'array',
        'occurred_at'  => 'datetime',
    ];

    // Scopes
    public function scopeForEmail($query, string $email)      { return $query->where('email', $email); }
    public function scopeBySource($query, string $source)     { return $query->where('source', $source); }
    public function scopeByType($query, string $type)         { return $query->where('event_type', $type); }
    public function scopeForCampaign($query, int $campaignId) { return $query->where('campaign_id', $campaignId); }

    // Stats helpers
    public static function campaignStats(int $campaignId): array
    {
        $base = static::where('campaign_id', $campaignId);
        return [
            'sent'       => (clone $base)->where('event_type', 'sent')->count(),
            'delivered'  => (clone $base)->where('event_type', 'delivered')->count(),
            'opened'     => (clone $base)->where('event_type', 'opened')->distinct('email')->count('email'),
            'clicked'    => (clone $base)->where('event_type', 'clicked')->distinct('email')->count('email'),
            'bounced'    => (clone $base)->where('event_type', 'bounced')->count(),
            'unsubscribed' => (clone $base)->where('event_type', 'unsubscribed')->count(),
        ];
    }
}
