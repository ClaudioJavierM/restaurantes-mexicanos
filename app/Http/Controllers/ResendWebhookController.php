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

        try {
            $emailLog = null;

            // 1. Try exact match by Resend email_id
            if ($messageId) {
                $emailLog = EmailLog::where("message_id", $messageId)->first();
            }

            // 2. Fallback: match by recipient within a 48h window (covers Symfony Message-ID mismatch)
            if (!$emailLog && $toEmail) {
                $emailLog = EmailLog::where("to_email", $toEmail)
                    ->where("sent_at", ">=", now()->subHours(48))
                    ->whereNotIn("status", ["bounced", "complained"])
                    ->orderBy("sent_at", "desc")
                    ->first();

                // Save the real Resend email_id so future webhook events match directly
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

            // metadata is cast to array in EmailLog model, no json_decode needed
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
                    $metadata = $emailLog->metadata ?? [];
                    $metadata["open_count"] = ($metadata["open_count"] ?? 0) + 1;
                    $metadata["last_opened_at"] = now()->toISOString();
                    $emailLog->metadata = $metadata;
                    break;

                case "email.clicked":
                    $emailLog->status = "clicked";
                    $emailLog->clicked_at = $emailLog->clicked_at ?? now();
                    $metadata = $emailLog->metadata ?? [];
                    $metadata["click_count"] = ($metadata["click_count"] ?? 0) + 1;
                    $metadata["last_clicked_at"] = now()->toISOString();
                    if (isset($data["link"])) {
                        $metadata["clicked_links"][] = $data["link"];
                    }
                    $emailLog->metadata = $metadata;
                    break;

                case "email.bounced":
                    $emailLog->status = "bounced";
                    $emailLog->bounced_at = now();
                    $emailLog->error_message = $data["bounce"]["message"] ?? "Bounced";
                    break;

                case "email.complained":
                    $emailLog->status = "complained";
                    $metadata = $emailLog->metadata ?? [];
                    $metadata["complained_at"] = now()->toISOString();
                    $emailLog->metadata = $metadata;
                    break;
            }

            $emailLog->save();

            return response()->json(["status" => "processed", "email_log_id" => $emailLog->id], 200);
        } catch (\Throwable $e) {
            Log::error("Resend webhook processing failed", [
                "type" => $type,
                "to_email" => $toEmail,
                "message_id" => $messageId,
                "error" => $e->getMessage(),
            ]);

            return response()->json(["status" => "error", "message" => "Processing failed"], 500);
        }
    }
}
