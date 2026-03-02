<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\User;
use App\Services\MenuSuggestionsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWeeklyMenuSuggestions extends Command
{
    protected $signature = "restaurants:weekly-menu-suggestions {--limit=50 : Limit number of restaurants}";
    protected $description = "Send weekly AI menu suggestions to restaurant owners";

    public function handle(): int
    {
        $this->info("Sending weekly menu suggestions...");
        
        $restaurants = Restaurant::where("status", "approved")
            ->whereNotNull("user_id")
            ->whereHas("user", function($q) {
                $q->whereNotNull("email");
            })
            ->with(["user", "category", "state"])
            ->limit($this->option("limit"))
            ->get();
        
        $this->info("Found {$restaurants->count()} restaurants with owners");
        
        $service = new MenuSuggestionsService();
        $sent = 0;
        $errors = 0;
        
        foreach ($restaurants as $restaurant) {
            try {
                $suggestions = $service->generateSuggestions($restaurant, "general");
                
                if ($suggestions["success"] && !empty($suggestions["suggestions"])) {
                    $this->sendEmail($restaurant, $suggestions["suggestions"]);
                    $sent++;
                    $this->line("  Sent to: {$restaurant->name} ({$restaurant->user->email})");
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("  Error for {$restaurant->name}: " . $e->getMessage());
                Log::error("Weekly menu suggestions error", [
                    "restaurant_id" => $restaurant->id,
                    "error" => $e->getMessage()
                ]);
            }
        }
        
        $this->info("Done! Sent: {$sent}, Errors: {$errors}");
        
        return self::SUCCESS;
    }
    
    protected function sendEmail(Restaurant $restaurant, array $suggestions): void
    {
        $user = $restaurant->user;
        if (!$user || !$user->email) return;
        
        $subject = "Sugerencias de Menu para {$restaurant->name}";
        
        $html = $this->buildEmailHtml($restaurant, $suggestions);
        
        Mail::html($html, function ($message) use ($user, $subject) {
            $message->to($user->email)
                ->subject($subject);
        });
    }
    
    protected function buildEmailHtml(Restaurant $restaurant, array $suggestions): string
    {
        $html = "<div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\">";
        $html .= "<h1 style=\"color: #ef4444;\">Sugerencias de Menu</h1>";
        $html .= "<p>Hola! Aqui tienes sugerencias personalizadas para <strong>{$restaurant->name}</strong>:</p>";
        $html .= "<hr style=\"border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;\">";
        
        foreach ($suggestions as $i => $suggestion) {
            $html .= "<div style=\"background: #f9fafb; padding: 15px; margin: 10px 0; border-radius: 8px;\">";
            $html .= "<h3 style=\"margin: 0 0 10px 0; color: #111827;\">" . ($i + 1) . ". {$suggestion["name"]}</h3>";
            $html .= "<p style=\"margin: 0 0 10px 0; color: #4b5563;\">{$suggestion["description"]}</p>";
            if (isset($suggestion["price_suggestion"])) {
                $html .= "<p style=\"margin: 0; color: #059669;\"><strong>Precio sugerido:</strong> \${$suggestion["price_suggestion"]}</p>";
            }
            if (isset($suggestion["tip"])) {
                $html .= "<p style=\"margin: 10px 0 0 0; padding: 10px; background: #fef3c7; border-radius: 4px; font-size: 14px;\"><strong>Tip:</strong> {$suggestion["tip"]}</p>";
            }
            $html .= "</div>";
        }
        
        $html .= "<hr style=\"border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;\">";
        $html .= "<p style=\"color: #6b7280; font-size: 14px;\">Visita tu panel de administracion para ver mas sugerencias de temporada, tendencias y opciones vegetarianas.</p>";
        $html .= "<p style=\"color: #6b7280; font-size: 12px;\">Restaurantes Mexicanos Famosos - Tu plataforma de exito</p>";
        $html .= "</div>";
        
        return $html;
    }
}