<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResendWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        Log::info("Resend webhook received", ["type" => $payload["type"] ?? "unknown"]);
        
        $type = $payload["type"] ?? null;
        $data = $payload["data"] ?? [];
        
        if (!$type || !$data) {
            return response()->json(["status" => "ignored", "reason" => "missing data"], 200);
        }
        
        $toEmail = $data["to"][0] ?? null;
        $messageId = $data["email_id"] ?? null;
        $createdAt = $data["created_at"] ?? null;
        
        if (!$toEmail) {
            return response()->json(["status" => "ignored", "reason" => "no recipient"], 200);
        }
        
        $emailLog = null;
        
        if ($messageId) {
            $emailLog = EmailLog::where("message_id", $messageId)->first();
        }
        
        if (!$emailLog) {
            $emailLog = EmailLog::where("to_email", $toEmail)
                ->whereNull("message_id")
                ->orderBy("created_at", "desc")
                ->first();
                
            if ($emailLog && $messageId) {
                $emailLog->message_id = $messageId;
            }
        }
        
        if (!$emailLog) {
            $emailLog = EmailLog::create([
                "type" => "campaign",
                "category" => "claim_invitation",
                "to_email" => $toEmail,
                "subject" => $data["subject"] ?? "Unknown",
                "status" => "sent",
                "message_id" => $messageId,
                "provider" => "resend",
                "sent_at" => $createdAt ? \Carbon\Carbon::parse($createdAt) : now(),
            ]);
        }
        
        switch ($type) {
            case "email.sent":
                $emailLog->status = "sent";
                $emailLog->sent_at = $emailLog->sent_at ?? now();
                break;
                
            case "email.delivered":
                $emailLog->status = "delivered";
                $emailLog->delivered_at = now();
                break;
                
            case "email.delivery_delayed":
                $emailLog->status = "delayed";
                break;
                
            case "email.opened":
                $emailLog->status = "opened";
                $emailLog->opened_at = $emailLog->opened_at ?? now();
                $metadata = json_decode($emailLog->metadata ?? "{}", true);
                $metadata["open_count"] = ($metadata["open_count"] ?? 0) + 1;
                $metadata["last_opened_at"] = now()->toISOString();
                $emailLog->metadata = json_encode($metadata);
                break;
                
            case "email.clicked":
                $emailLog->status = "clicked";
                $emailLog->clicked_at = $emailLog->clicked_at ?? now();
                $metadata = json_decode($emailLog->metadata ?? "{}", true);
                $metadata["click_count"] = ($metadata["click_count"] ?? 0) + 1;
                $metadata["last_clicked_at"] = now()->toISOString();
                if (isset($data["link"])) {
                    $metadata["clicked_links"][] = $data["link"];
                }
                $emailLog->metadata = json_encode($metadata);
                break;
                
            case "email.bounced":
                $emailLog->status = "bounced";
                $emailLog->bounced_at = now();
                $emailLog->error_message = $data["bounce"]["message"] ?? "Bounced";
                break;
                
            case "email.complained":
                $emailLog->status = "complained";
                $metadata = json_decode($emailLog->metadata ?? "{}", true);
                $metadata["complained_at"] = now()->toISOString();
                $emailLog->metadata = json_encode($metadata);
                break;
        }
        
        $emailLog->save();
        
        return response()->json(["status" => "processed", "email_log_id" => $emailLog->id], 200);
    }
}
