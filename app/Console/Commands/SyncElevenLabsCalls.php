<?php

namespace App\Console\Commands;

use App\Models\Call;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncElevenLabsCalls extends Command
{
    protected $signature = "calls:sync";
    protected $description = "Sync calls from ElevenLabs API";

    private string $apiKey = "sk_5ea0e5060410ab8a0b1aed443779a8c0aeddc9e580915163";
    private string $agentId = "agent_0501kcsb7wm4embt6591gj1ppbat"; // Carmen

    public function handle(): int
    {
        $this->info("Syncing calls for Carmen: {$this->agentId}");

        $response = Http::withHeaders([
            "xi-api-key" => $this->apiKey,
        ])->get("https://api.elevenlabs.io/v1/convai/conversations", [
            "agent_id" => $this->agentId,
        ]);

        if (!$response->successful()) {
            $this->error("API Error: " . $response->status());
            return 1;
        }

        $conversations = $response->json("conversations") ?? [];
        $synced = 0;

        foreach ($conversations as $conv) {
            $callId = $conv["conversation_id"] ?? null;
            if (!$callId || Call::where("elevenlabs_call_id", $callId)->exists()) continue;

            $detail = Http::withHeaders(["xi-api-key" => $this->apiKey])
                ->get("https://api.elevenlabs.io/v1/convai/conversations/{$callId}");

            $data = $detail->json();
            $transcript = $this->buildTranscript($data["transcript"] ?? []);

            Call::create([
                "elevenlabs_call_id" => $callId,
                "agent_id" => $data["agent_id"] ?? $this->agentId,
                "status" => $data["status"] ?? "unknown",
                "transcript" => $transcript,
                "duration_seconds" => $data["metadata"]["call_duration_secs"] ?? null,
                "call_started_at" => isset($data["metadata"]["start_time_unix_secs"]) 
                    ? \Carbon\Carbon::createFromTimestamp($data["metadata"]["start_time_unix_secs"]) : null,
                "metadata" => $data["metadata"] ?? null,
                "category" => $this->categorizeCall($transcript),
            ]);
            $synced++;
        }

        $this->info("Synced {$synced} new calls");
        return 0;
    }

    private function buildTranscript(array $messages): string
    {
        return collect($messages)->map(fn($m) => 
            ($m["role"] === "agent" ? "Carmen" : "Cliente") . ": " . $m["message"]
        )->implode("\n");
    }

    private function categorizeCall(string $t): string
    {
        $t = strtolower($t);
        if (str_contains($t, "pedido") || str_contains($t, "order")) return "order_inquiry";
        if (str_contains($t, "reserv")) return "reservation";
        if (str_contains($t, "busca") || str_contains($t, "encuentra")) return "restaurant_search";
        if (str_contains($t, "dueno") || str_contains($t, "owner") || str_contains($t, "dashboard")) return "owner_support";
        if (str_contains($t, "reclam") || str_contains($t, "claim")) return "claim_status";
        return "other";
    }
}
