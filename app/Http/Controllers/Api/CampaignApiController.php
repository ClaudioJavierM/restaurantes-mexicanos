<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CampaignApiController extends Controller
{
    public function sendBatch(Request $request)
    {
        $request->validate([
            "campaign_name" => "required|string",
            "contacts" => "required|array|max:50",
            "contacts.*.email" => "required|email",
            "contacts.*.name" => "nullable|string",
            "subject" => "required|string",
            "html_content" => "required|string",
            "from_name" => "nullable|string",
            "from_email" => "nullable|email",
            "api_key" => "required|string",
        ]);
        
        $validKey = env("N8N_API_KEY");
        if ($request->api_key !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }
        
        $resendKey = env("RESEND_API_KEY");
        if (empty($resendKey)) {
            return response()->json(["error" => "RESEND_API_KEY not configured"], 500);
        }
        
        $results = ["sent" => 0, "failed" => 0, "errors" => []];
        
        $fromName = $request->from_name ?? "Restaurantes Mexicanos Famosos";
        $fromEmail = $request->from_email ?? "noreply@restaurantesmexicanosfamosos.com";
        
        foreach ($request->contacts as $contact) {
            $email = $contact["email"];
            $name = $contact["name"] ?? "";
            $firstname = explode(" ", $name)[0] ?? "";
            
            $subject = str_replace(["{name}", "{firstname}"], [$name, $firstname], $request->subject);
            $htmlContent = str_replace(["{name}", "{firstname}", "{email}"], [$name, $firstname, $email], $request->html_content);
            
            try {
                $response = Http::withHeaders([
                    "Authorization" => "Bearer " . $resendKey,
                ])->post("https://api.resend.com/emails", [
                    "from" => "{$fromName} <{$fromEmail}>",
                    "to" => $email,
                    "subject" => $subject,
                    "html" => $htmlContent,
                ]);
                
                if ($response->successful()) {
                    $resendData = $response->json();
                    $messageId = $resendData["id"] ?? null;
                    
                    $restaurant = Restaurant::where("email", $email)->first();
                    
                    EmailLog::create([
                        "type" => "campaign",
                        "category" => $request->campaign_name,
                        "to_email" => $email,
                        "to_name" => $name,
                        "from_email" => $fromEmail,
                        "from_name" => $fromName,
                        "subject" => $subject,
                        "body_preview" => Str::limit(strip_tags($htmlContent), 200),
                        "status" => "sent",
                        "sent_at" => now(),
                        "message_id" => $messageId,
                        "provider" => "resend",
                        "restaurant_id" => $restaurant ? $restaurant->id : null,
                        "tracking_token" => Str::uuid(),
                    ]);
                    
                    $results["sent"]++;
                } else {
                    $results["failed"]++;
                    $results["errors"][] = ["email" => $email, "error" => $response->body()];
                }
            } catch (\Exception $e) {
                $results["failed"]++;
                $results["errors"][] = ["email" => $email, "error" => $e->getMessage()];
            }
        }
        
        return response()->json([
            "success" => true,
            "campaign" => $request->campaign_name,
            "results" => $results,
        ]);
    }
    
    public function getContacts(Request $request)
    {
        $validKey = env("N8N_API_KEY");
        if ($request->api_key !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }
        
        $limit = $request->limit ?? 100;
        $offset = $request->offset ?? 0;
        $campaign = $request->campaign ?? "famer-grader";
        
        $sentEmails = EmailLog::where("category", $campaign)
            ->whereIn("status", ["sent", "delivered", "opened", "clicked"])
            ->pluck("to_email")
            ->map(function($e) { return strtolower($e); })
            ->toArray();
        
        $contacts = Restaurant::whereNotNull("email")
            ->where("email", "LIKE", "%@%")
            ->get()
            ->filter(function($r) use ($sentEmails) {
                return !in_array(strtolower($r->email), $sentEmails);
            })
            ->skip($offset)
            ->take($limit)
            ->map(function($r) {
                return [
                    "email" => $r->email,
                    "name" => $r->name,
                    "city" => $r->city,
                ];
            })
            ->values();
        
        return response()->json([
            "success" => true,
            "campaign" => $campaign,
            "contacts" => $contacts,
            "count" => $contacts->count(),
            "already_sent" => count($sentEmails),
        ]);
    }
    
    public function getStats(Request $request)
    {
        $validKey = env("N8N_API_KEY");
        if ($request->api_key !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }
        
        $campaign = $request->campaign;
        
        $query = EmailLog::query();
        if ($campaign) {
            $query->where("category", $campaign);
        }
        
        return response()->json([
            "success" => true,
            "stats" => [
                "total" => (clone $query)->count(),
                "sent" => (clone $query)->where("status", "sent")->count(),
                "delivered" => (clone $query)->where("status", "delivered")->count(),
                "opened" => (clone $query)->where("status", "opened")->count(),
                "clicked" => (clone $query)->where("status", "clicked")->count(),
                "bounced" => (clone $query)->where("status", "bounced")->count(),
                "failed" => (clone $query)->where("status", "failed")->count(),
                "today" => EmailLog::whereDate("sent_at", today())->count(),
            ],
        ]);
    }

    /**
     * Get new restaurants from today for welcome campaign
     */
    public function newRestaurantsToday(Request $request)
    {
        // Validate API key from header
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        // Get restaurants created today that have not received welcome email
        $restaurants = Restaurant::whereDate("created_at", today())
            ->whereNull("welcome_email_sent_at")
            ->where("email", "!=", "")
            ->whereNotNull("email")
            ->limit(50)
            ->get(["id", "name", "email", "city", "state", "created_at"]);

        return response()->json([
            "success" => true,
            "count" => $restaurants->count(),
            "restaurants" => $restaurants
        ]);
    }

    /**
     * Get restaurants pending follow-up emails (7 days and 30 days after welcome)
     */
    public function pendingFollowups(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $followup7d = Restaurant::whereNotNull("welcome_email_sent_at")
            ->whereDate("welcome_email_sent_at", "<=", now()->subDays(7))
            ->whereNull("followup_7d_sent_at")
            ->where("email", "!=", "")
            ->whereNotNull("email")
            ->limit(25)
            ->get(["id", "name", "email", "city", "state", "welcome_email_sent_at"]);

        $followup30d = Restaurant::whereNotNull("followup_7d_sent_at")
            ->whereDate("followup_7d_sent_at", "<=", now()->subDays(23))
            ->whereNull("followup_30d_sent_at")
            ->where("email", "!=", "")
            ->whereNotNull("email")
            ->limit(25)
            ->get(["id", "name", "email", "city", "state", "followup_7d_sent_at"]);

        return response()->json([
            "success" => true,
            "followup_7d" => ["count" => $followup7d->count(), "restaurants" => $followup7d],
            "followup_30d" => ["count" => $followup30d->count(), "restaurants" => $followup30d]
        ]);
    }

    /**
     * Mark restaurant as having received a reminder email
     */
    public function markReminderSent(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $type = $request->input("reminder_type");
        $ids = $request->input("restaurant_ids", []);

        $column = match($type) {
            "welcome" => "welcome_email_sent_at",
            "followup_7d" => "followup_7d_sent_at",
            "followup_30d" => "followup_30d_sent_at",
            default => null
        };

        if (!$column) {
            return response()->json(["error" => "Invalid reminder type"], 400);
        }

        $updated = Restaurant::whereIn("id", $ids)->update([$column => now()]);

        return response()->json(["success" => true, "updated" => $updated, "reminder_type" => $type]);
    }

    public function newRestaurantsRange(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $startDate = $request->start_date ?? now()->subDays(7)->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        $restaurants = Restaurant::whereBetween("created_at", [$startDate, $endDate])
            ->where("email", "!=", "")->whereNotNull("email")
            ->get(["id", "name", "email", "city", "state", "created_at"]);

        return response()->json(["success" => true, "count" => $restaurants->count(), "restaurants" => $restaurants]);
    }

    public function platformStats(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        return response()->json([
            "success" => true,
            "stats" => [
                "total_restaurants" => Restaurant::count(),
                "with_email" => Restaurant::whereNotNull("email")->where("email", "!=", "")->count(),
                "claimed" => Restaurant::whereNotNull("claimed_at")->count(),
                "created_today" => Restaurant::whereDate("created_at", today())->count(),
            ]
        ]);
    }

    public function restaurantStats(Request $request, $id)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(["error" => "Restaurant not found"], 404);
        }

        return response()->json(["success" => true, "restaurant" => $restaurant]);
    }

    public function unclaimedAfterDays(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $days = $request->days ?? 30;
        $restaurants = Restaurant::whereNull("claimed_at")
            ->whereDate("created_at", "<=", now()->subDays($days))
            ->where("email", "!=", "")->whereNotNull("email")
            ->limit(50)->get(["id", "name", "email", "city", "state"]);

        return response()->json(["success" => true, "count" => $restaurants->count(), "restaurants" => $restaurants]);
    }

    public function famerPendingEmail1(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $restaurants = Restaurant::whereNotNull("email")->where("email", "!=", "")
            ->whereNull("famer_email1_sent_at")->limit(50)->get(["id", "name", "email", "city", "state"]);

        return response()->json(["success" => true, "count" => $restaurants->count(), "restaurants" => $restaurants]);
    }

    public function famerPendingEmail2(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $restaurants = Restaurant::whereNotNull("famer_email1_sent_at")
            ->whereDate("famer_email1_sent_at", "<=", now()->subDays(7))
            ->whereNull("famer_email2_sent_at")->where("email", "!=", "")
            ->limit(50)->get(["id", "name", "email", "city", "state"]);

        return response()->json(["success" => true, "count" => $restaurants->count(), "restaurants" => $restaurants]);
    }

    public function famerPendingEmail3(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $restaurants = Restaurant::whereNotNull("famer_email2_sent_at")
            ->whereDate("famer_email2_sent_at", "<=", now()->subDays(14))
            ->whereNull("famer_email3_sent_at")->where("email", "!=", "")
            ->limit(50)->get(["id", "name", "email", "city", "state"]);

        return response()->json(["success" => true, "count" => $restaurants->count(), "restaurants" => $restaurants]);
    }

    public function famerMarkSent(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        $emailNum = $request->input("email_number");
        $ids = $request->input("restaurant_ids", []);
        $column = "famer_email" . $emailNum . "_sent_at";

        $updated = Restaurant::whereIn("id", $ids)->update([$column => now()]);

        return response()->json(["success" => true, "updated" => $updated]);
    }

    public function famerStats(Request $request)
    {
        $apiKey = $request->header("X-API-Key");
        $validKey = env("N8N_API_KEY", env("FAMER_API_KEY"));

        if ($apiKey !== $validKey) {
            return response()->json(["error" => "Invalid API key"], 401);
        }

        return response()->json([
            "success" => true,
            "stats" => [
                "total_with_email" => Restaurant::whereNotNull("email")->where("email", "!=", "")->count(),
                "email1_sent" => Restaurant::whereNotNull("famer_email1_sent_at")->count(),
                "email2_sent" => Restaurant::whereNotNull("famer_email2_sent_at")->count(),
                "email3_sent" => Restaurant::whereNotNull("famer_email3_sent_at")->count(),
            ]
        ]);
    }
}
