<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use PDO;

class RecentCampaignsWidget extends Widget
{
    protected static string $view = "filament.widgets.recent-campaigns";
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = "full";
    protected static ?string $pollingInterval = "60s";
    
    public function getCampaigns(): array
    {
        return Cache::remember("listmonk_recent_campaigns", 60, function () {
            try {
                $pdo = new PDO(
                    "pgsql:host=127.0.0.1;port=5433;dbname=listmonk",
                    "listmonk",
                    "listmonk_s3cur3_2024",
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                $stmt = $pdo->query("
                    SELECT
                        c.id,
                        c.name,
                        c.status,
                        c.sent,
                        c.to_send,
                        c.started_at,
                        (SELECT COUNT(*) FROM campaign_views WHERE campaign_id = c.id) as opens,
                        (SELECT COUNT(*) FROM link_clicks WHERE campaign_id = c.id) as clicks
                    FROM campaigns c
                    WHERE c.status IN ('running', 'finished', 'paused')
                      AND NOT (
                        c.tags @> ARRAY['sdv']::varchar[]
                        OR c.tags @> ARRAY['beca-sdv']::varchar[]
                      )
                    ORDER BY c.created_at DESC
                    LIMIT 10
                ");
                
                $campaigns = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $openRate = $row["sent"] > 0 ? round(($row["opens"] / $row["sent"]) * 100, 1) : 0;
                    $clickRate = $row["sent"] > 0 ? round(($row["clicks"] / $row["sent"]) * 100, 1) : 0;
                    
                    $campaigns[] = [
                        "id" => $row["id"],
                        "name" => $row["name"],
                        "status" => $row["status"],
                        "sent" => $row["sent"],
                        "to_send" => $row["to_send"],
                        "opens" => $row["opens"],
                        "clicks" => $row["clicks"],
                        "open_rate" => $openRate,
                        "click_rate" => $clickRate,
                        "date" => $row["started_at"] ? date("M j, H:i", strtotime($row["started_at"])) : "-",
                    ];
                }
                
                return $campaigns;
            } catch (\Exception $e) {
                \Log::error("Listmonk campaigns error: " . $e->getMessage());
                return [];
            }
        });
    }
}
