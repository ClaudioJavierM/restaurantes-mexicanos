<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use App\Services\MenuSuggestionsService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AiMenuSuggestionsWidget extends Widget
{
    protected static string $view = "filament.widgets.ai-menu-suggestions";
    protected int | string | array $columnSpan = "full";
    protected static ?int $sort = 5;
    
    public ?Restaurant $restaurant = null;
    public string $suggestionType = "general";
    public array $suggestions = [];
    public bool $loading = false;
    public ?string $error = null;
    
    public function mount(): void
    {
        $user = Auth::user();
        if ($user && method_exists($user, "isOwner") && $user->isOwner()) {
            $this->restaurant = Restaurant::where("user_id", $user->id)->where("status", "approved")->first();
            if ($this->restaurant) {
                $this->loadSuggestions();
            }
        }
    }
    
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && method_exists($user, "isOwner") && $user->isOwner();
    }
    
    public function loadSuggestions(): void
    {
        if (!$this->restaurant) return;
        $this->loading = true;
        $this->error = null;
        try {
            $service = new MenuSuggestionsService();
            $result = $service->generateSuggestions($this->restaurant, $this->suggestionType);
            if ($result["success"]) {
                $this->suggestions = $result["suggestions"];
            } else {
                $this->error = "No se pudieron generar sugerencias.";
            }
        } catch (\Exception $e) {
            $this->error = "Error: " . $e->getMessage();
        }
        $this->loading = false;
    }
    
    public function refreshSuggestions(): void
    {
        if (!$this->restaurant) return;
        $this->loading = true;
        $this->error = null;
        try {
            $service = new MenuSuggestionsService();
            $result = $service->generateFreshSuggestions($this->restaurant, $this->suggestionType);
            if ($result["success"]) {
                $this->suggestions = $result["suggestions"];
            }
        } catch (\Exception $e) {
            $this->error = "Error: " . $e->getMessage();
        }
        $this->loading = false;
    }
    
    public function changeSuggestionType(string $type): void
    {
        $this->suggestionType = $type;
        $this->loadSuggestions();
    }
    
    public function getSuggestionTypes(): array
    {
        return MenuSuggestionsService::getSuggestionTypes();
    }
}