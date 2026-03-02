<?php

namespace App\Http\Controllers;

use App\Mail\FamerIntroduction;
use App\Mail\FamerHowItWorks;
use App\Mail\FamerReminder;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FamerWebhookController extends Controller
{
    /**
     * N8N webhook to trigger FAMER email sequence
     */
    public function trigger(Request $request)
    {
        $secret = $request->header("X-Webhook-Secret");
        
        // Simple secret validation
        if ($secret !== config("services.n8n.webhook_secret", "famer-webhook-secret-2024")) {
            return response()->json(["error" => "Unauthorized"], 401);
        }

        $action = $request->input("action", "all");
        $limit = min((int) $request->input("limit", 50), 200);
        
        Log::info("FAMER Webhook triggered", ["action" => $action, "limit" => $limit]);

        $results = [];

        switch ($action) {
            case "email1":
                $results = $this->sendEmail1($limit);
                break;
            case "email2":
                $results = $this->sendEmail2($limit);
                break;
            case "email3":
                $results = $this->sendEmail3($limit);
                break;
            case "all":
                $results["email1"] = $this->sendEmail1($limit);
                $results["email2"] = $this->sendEmail2($limit);
                $results["email3"] = $this->sendEmail3($limit);
                break;
            case "send_single":
                // Send specific email to specific restaurant
                $restaurantId = $request->input("restaurant_id");
                $emailType = $request->input("email_type", 1);
                $results = $this->sendSingleEmail($restaurantId, $emailType);
                break;
            default:
                return response()->json(["error" => "Invalid action"], 400);
        }

        return response()->json([
            "success" => true,
            "action" => $action,
            "results" => $results,
            "timestamp" => now()->toIso8601String(),
        ]);
    }

    private function sendEmail1(int $limit): array
    {
        $sent = 0;
        $failed = 0;

        $restaurants = Restaurant::query()
            ->where("status", "approved")
            ->whereNotNull("email")
            ->where("email", "!=", "")
            ->where("is_claimed", false)
            ->whereNull("famer_email_1_sent_at")
            ->limit($limit)
            ->get();

        foreach ($restaurants as $restaurant) {
            try {
                if (filter_var($restaurant->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($restaurant->email)->queue(new FamerIntroduction($restaurant));
                    $restaurant->update(["famer_email_1_sent_at" => now()]);
                    $sent++;
                }
            } catch (\Exception $e) {
                Log::error("FAMER Email 1 failed", ["id" => $restaurant->id, "error" => $e->getMessage()]);
                $failed++;
            }
        }

        return ["sent" => $sent, "failed" => $failed, "total_eligible" => $restaurants->count()];
    }

    private function sendEmail2(int $limit): array
    {
        $sent = 0;
        $failed = 0;

        $restaurants = Restaurant::query()
            ->where("status", "approved")
            ->where("is_claimed", false)
            ->whereNotNull("famer_email_1_sent_at")
            ->where("famer_email_1_sent_at", "<", now()->subDays(7))
            ->whereNull("famer_email_2_sent_at")
            ->limit($limit)
            ->get();

        foreach ($restaurants as $restaurant) {
            try {
                if (filter_var($restaurant->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($restaurant->email)->queue(new FamerHowItWorks($restaurant));
                    $restaurant->update(["famer_email_2_sent_at" => now()]);
                    $sent++;
                }
            } catch (\Exception $e) {
                Log::error("FAMER Email 2 failed", ["id" => $restaurant->id, "error" => $e->getMessage()]);
                $failed++;
            }
        }

        return ["sent" => $sent, "failed" => $failed, "total_eligible" => $restaurants->count()];
    }

    private function sendEmail3(int $limit): array
    {
        $sent = 0;
        $failed = 0;

        $restaurants = Restaurant::query()
            ->where("status", "approved")
            ->where("is_claimed", false)
            ->whereNotNull("famer_email_2_sent_at")
            ->where("famer_email_2_sent_at", "<", now()->subDays(7))
            ->whereNull("famer_email_3_sent_at")
            ->limit($limit)
            ->get();

        foreach ($restaurants as $restaurant) {
            try {
                if (filter_var($restaurant->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($restaurant->email)->queue(new FamerReminder($restaurant));
                    $restaurant->update(["famer_email_3_sent_at" => now()]);
                    $sent++;
                }
            } catch (\Exception $e) {
                Log::error("FAMER Email 3 failed", ["id" => $restaurant->id, "error" => $e->getMessage()]);
                $failed++;
            }
        }

        return ["sent" => $sent, "failed" => $failed, "total_eligible" => $restaurants->count()];
    }

    private function sendSingleEmail(int $restaurantId, int $emailType): array
    {
        $restaurant = Restaurant::find($restaurantId);
        
        if (!$restaurant) {
            return ["error" => "Restaurant not found"];
        }

        if (!filter_var($restaurant->email, FILTER_VALIDATE_EMAIL)) {
            return ["error" => "Invalid email"];
        }

        try {
            switch ($emailType) {
                case 1:
                    Mail::to($restaurant->email)->queue(new FamerIntroduction($restaurant));
                    $restaurant->update(["famer_email_1_sent_at" => now()]);
                    break;
                case 2:
                    Mail::to($restaurant->email)->queue(new FamerHowItWorks($restaurant));
                    $restaurant->update(["famer_email_2_sent_at" => now()]);
                    break;
                case 3:
                    Mail::to($restaurant->email)->queue(new FamerReminder($restaurant));
                    $restaurant->update(["famer_email_3_sent_at" => now()]);
                    break;
            }
            return ["success" => true, "restaurant_id" => $restaurantId, "email_type" => $emailType];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
