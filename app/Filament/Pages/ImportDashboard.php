<?php

namespace App\Filament\Pages;

use App\Models\ApiCallLog;
use App\Models\EnrichmentStat;
use App\Models\ImportStat;
use App\Models\Restaurant;
use App\Models\State;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ImportDashboard extends Page
{
    protected static ?string $navigationIcon = "heroicon-o-chart-bar-square";
    protected static string $view = "filament.pages.import-dashboard";
    protected static ?string $navigationLabel = "Import Dashboard";
    protected static ?string $title = "Import & Analytics Dashboard";
    protected static ?string $navigationGroup = "Sistema";
    protected static ?int $navigationSort = 2;

    public string $period = "30days";

    public function mount(): void
    {
        $this->period = request()->get("period", "30days");
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function getHeading(): string
    {
        return "Import & Analytics Dashboard";
    }

    public function getSubheading(): string
    {
        return "Monitor imports, API usage, and manage Yelp data";
    }

    /**
     * Main statistics
     */
    public function getStats(): array
    {
        $days = match ($this->period) {
            "7days" => 7,
            "30days" => 30,
            "90days" => 90,
            default => 30,
        };

        $startDate = now()->subDays($days);

        $totalRestaurants = Restaurant::count();
        $newRestaurants = Restaurant::where("created_at", ">=", $startDate)->count();
        $withYelp = Restaurant::whereNotNull("yelp_id")->count();
        $withGoogle = Restaurant::whereNotNull("google_place_id")->count();
        $yelpImported = Restaurant::where("import_source", "yelp")->count();

        $lastImport = Restaurant::where("import_source", "yelp")
            ->latest("imported_at")
            ->first();

        $statesWithYelpData = Restaurant::where("import_source", "yelp")
            ->distinct("state_id")
            ->count("state_id");

        // API stats
        $apiCalls = ApiCallLog::where("called_at", ">=", $startDate)->count();
        $apiCost = ApiCallLog::where("called_at", ">=", $startDate)->sum("cost");

        // Import stats
        $importStats = ImportStat::where("stat_date", ">=", $startDate)->get();
        $totalImported = $importStats->sum("imported") ?: $newRestaurants;
        $totalDuplicates = $importStats->sum("duplicates_skipped");

        return [
            "total_restaurants" => $totalRestaurants,
            "new_restaurants" => $newRestaurants,
            "with_yelp" => $withYelp,
            "with_google" => $withGoogle,
            "yelp_imported" => $yelpImported,
            "total_imported" => $totalImported,
            "total_duplicates" => $totalDuplicates,
            "api_calls" => $apiCalls,
            "api_cost" => $apiCost,
            "states_covered" => $statesWithYelpData,
            "last_import_date" => $lastImport?->imported_at?->diffForHumans() ?? "Never",
            "yelp_coverage" => $totalRestaurants > 0 ? round(($withYelp / $totalRestaurants) * 100, 1) : 0,
            "google_coverage" => $totalRestaurants > 0 ? round(($withGoogle / $totalRestaurants) * 100, 1) : 0,
        ];
    }

    /**
     * Imports by day for chart
     */
    public function getImportsByDay(): array
    {
        $days = match ($this->period) {
            "7days" => 7,
            "30days" => 30,
            "90days" => 90,
            default => 30,
        };

        $imports = Restaurant::selectRaw("DATE(created_at) as date, COUNT(*) as count")
            ->where("created_at", ">=", now()->subDays($days))
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->keyBy("date");

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format("Y-m-d");
            $result[] = [
                "date" => now()->subDays($i)->format("M d"),
                "count" => $imports[$date]->count ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Imports by source for pie chart
     */
    public function getImportsBySource(): array
    {
        $days = match ($this->period) {
            "7days" => 7,
            "30days" => 30,
            "90days" => 90,
            default => 30,
        };

        return Restaurant::selectRaw("COALESCE(import_source, 'manual') as source, COUNT(*) as count")
            ->where("created_at", ">=", now()->subDays($days))
            ->groupBy("source")
            ->orderByDesc("count")
            ->get()
            ->map(fn($item) => [
                "source" => ucfirst($item->source ?? "manual"),
                "count" => $item->count,
            ])
            ->toArray();
    }

    /**
     * API calls by day for chart
     */
    public function getApiCallsByDay(): array
    {
        $days = match ($this->period) {
            "7days" => 7,
            "30days" => 30,
            "90days" => 90,
            default => 30,
        };

        $calls = ApiCallLog::selectRaw("DATE(called_at) as date, COUNT(*) as count, SUM(cost) as cost")
            ->where("called_at", ">=", now()->subDays($days))
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->keyBy("date");

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format("Y-m-d");
            $result[] = [
                "date" => now()->subDays($i)->format("M d"),
                "calls" => $calls[$date]->count ?? 0,
                "cost" => (float) ($calls[$date]->cost ?? 0),
            ];
        }

        return $result;
    }

    /**
     * Top states by imports
     */
    public function getTopStates(): array
    {
        $days = match ($this->period) {
            "7days" => 7,
            "30days" => 30,
            "90days" => 90,
            default => 30,
        };

        return Restaurant::selectRaw("state_id, COUNT(*) as count")
            ->with("state:id,name,code")
            ->where("created_at", ">=", now()->subDays($days))
            ->groupBy("state_id")
            ->orderByDesc("count")
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                "state" => $item->state?->name ?? "Unknown",
                "code" => $item->state?->code ?? "??",
                "count" => $item->count,
            ])
            ->toArray();
    }

    /**
     * Recent imports
     */
    public function getRecentImports(): array
    {
        return Restaurant::where("import_source", "yelp")
            ->with("state:id,name,code")
            ->latest("imported_at")
            ->limit(8)
            ->get()
            ->map(fn($r) => [
                "id" => $r->id,
                "name" => $r->name,
                "city" => $r->city,
                "state" => $r->state?->code ?? "N/A",
                "rating" => $r->yelp_rating ?? "N/A",
                "date" => $r->imported_at?->diffForHumans() ?? $r->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    /**
     * Scheduled tasks
     */
    public function getScheduledTasks(): array
    {
        return [
            ["day" => "Monday", "states" => "CA, TX, AZ, NM, NV, CO, FL", "count" => 7, "time" => "02:00 AM", "type" => "import"],
            ["day" => "Tuesday", "states" => "NY, IL, GA, NC, WA, OR, UT", "count" => 7, "time" => "02:00 AM", "type" => "import"],
            ["day" => "Wednesday", "states" => "OK, KS, NE, IA, MO, AR, LA", "count" => 7, "time" => "02:00 AM", "type" => "import"],
            ["day" => "Thursday", "states" => "MS, AL, TN, KY, IN, OH, MI", "count" => 7, "time" => "02:00 AM", "type" => "import"],
            ["day" => "Friday", "states" => "WI, MN, PA, VA, SC, NJ, MA", "count" => 7, "time" => "02:00 AM", "type" => "import"],
            ["day" => "Saturday", "states" => "MD, CT, DE, RI, NH, VT, ME", "count" => 7, "time" => "02:00 AM", "type" => "import"],
            ["day" => "Sunday", "states" => "WV, MT, WY, ND, SD, ID, AK, HI", "count" => 8, "time" => "02:00 AM", "type" => "import"],
        ];
    }

    /**
     * Daily tasks
     */
    public function getDailyTasks(): array
    {
        return [
            ["task" => "Duplicate Detection", "time" => "04:00 AM", "description" => "Merge and remove duplicates"],
            ["task" => "SEO Descriptions", "time" => "05:00 AM", "description" => "Generate for new restaurants"],
            ["task" => "Yelp Backfill", "time" => "06:00 AM", "description" => "Photos, hours, attributes"],
        ];
    }

    /**
     * Header actions
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make("importCity")
                ->label("Import City")
                ->icon("heroicon-o-plus-circle")
                ->color("success")
                ->form([
                    TextInput::make("city")
                        ->label("City Name")
                        ->required()
                        ->placeholder("e.g., Los Angeles"),
                    Select::make("state")
                        ->label("State")
                        ->required()
                        ->searchable()
                        ->options(State::orderBy("name")->pluck("name", "code")),
                    TextInput::make("limit")
                        ->label("Max Restaurants")
                        ->numeric()
                        ->default(50)
                        ->minValue(1)
                        ->maxValue(50),
                    TextInput::make("min_rating")
                        ->label("Min Rating")
                        ->numeric()
                        ->default(3.5)
                        ->minValue(1)
                        ->maxValue(5)
                        ->step(0.1),
                ])
                ->action(function (array $data) {
                    try {
                        Notification::make()
                            ->title("Import Started")
                            ->body("Importing from {$data["city"]}, {$data["state"]}...")
                            ->info()
                            ->send();

                        Artisan::call("yelp:import", [
                            "city" => $data["city"],
                            "state" => $data["state"],
                            "--limit" => $data["limit"],
                            "--min-rating" => $data["min_rating"],
                        ]);

                        Notification::make()
                            ->title("Import Completed")
                            ->body("Successfully imported from {$data["city"]}")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title("Import Failed")
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make("bulkImport")
                ->label("Bulk Import")
                ->icon("heroicon-o-cloud-arrow-down")
                ->color("primary")
                ->form([
                    Select::make("states")
                        ->label("States")
                        ->multiple()
                        ->searchable()
                        ->options(State::orderBy("name")->pluck("name", "code"))
                        ->required(),
                    TextInput::make("cities_per_state")
                        ->label("Cities per State")
                        ->numeric()
                        ->default(3)
                        ->minValue(1)
                        ->maxValue(5),
                ])
                ->action(function (array $data) {
                    try {
                        Notification::make()
                            ->title("Bulk Import Started")
                            ->body("This may take several minutes...")
                            ->info()
                            ->send();

                        Artisan::call("yelp:import-bulk", [
                            "--states" => $data["states"],
                            "--cities-per-state" => $data["cities_per_state"],
                            "--min-rating" => 3.5,
                            "--delay" => 3,
                        ]);

                        Notification::make()
                            ->title("Bulk Import Completed")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title("Bulk Import Failed")
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make("detectDuplicates")
                ->label("Detect Duplicates")
                ->icon("heroicon-o-document-duplicate")
                ->color("warning")
                ->requiresConfirmation()
                ->modalHeading("Detect and Remove Duplicates")
                ->modalDescription("This will analyze all restaurants and remove duplicates.")
                ->action(function () {
                    try {
                        Artisan::call("restaurants:detect-duplicates", [
                            "--remove" => true,
                            "--merge" => true,
                            "--similarity" => 80,
                        ]);

                        Notification::make()
                            ->title("Duplicates Removed")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title("Detection Failed")
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    /**
     * Get comprehensive enrichment statistics
     */
    public function getEnrichmentMetrics(): array
    {
        $total = Restaurant::count();
        
        return [
            "total" => $total,
            "platforms" => [
                ["name" => "Yelp", "count" => Restaurant::whereNotNull("yelp_id")->count(), "color" => "#ef4444"],
                ["name" => "Google", "count" => Restaurant::whereNotNull("google_place_id")->count(), "color" => "#22c55e"],
                ["name" => "TripAdvisor", "count" => Restaurant::whereNotNull("tripadvisor_id")->count(), "color" => "#06b6d4"],
                ["name" => "Foursquare", "count" => Restaurant::whereNotNull("foursquare_id")->count(), "color" => "#8b5cf6"],
            ],
            "contact" => [
                ["name" => "Phone", "count" => Restaurant::whereNotNull("phone")->count()],
                ["name" => "Email", "count" => Restaurant::whereNotNull("email")->where("email", "!=", "")->count()],
                ["name" => "Website", "count" => Restaurant::whereNotNull("website")->where("website", "!=", "")->count()],
            ],
            "content" => [
                ["name" => "Description", "count" => Restaurant::whereNotNull("description")->where("description", "!=", "")->count()],
                ["name" => "Images", "count" => Restaurant::whereNotNull("image")->count()],
                ["name" => "Hours", "count" => Restaurant::whereNotNull("opening_hours")->count()],
                ["name" => "Menu URL", "count" => Restaurant::whereNotNull("menu_url")->where("menu_url", "!=", "")->count()],
            ],
            "yelp_data" => [
                ["name" => "Yelp Photos", "count" => Restaurant::whereNotNull("yelp_photos")->count()],
                ["name" => "Yelp Hours", "count" => Restaurant::whereNotNull("yelp_hours")->count()],
                ["name" => "Yelp Attributes", "count" => Restaurant::whereNotNull("yelp_attributes")->count()],
            ],
        ];
    }

    public function getDataQualityScore(): int
    {
        $total = Restaurant::count();
        if ($total === 0) return 0;
        $score = 0;
        $score += (Restaurant::whereNotNull("phone")->count() / $total) * 15;
        $score += (Restaurant::whereNotNull("email")->where("email", "!=", "")->count() / $total) * 20;
        $score += (Restaurant::whereNotNull("website")->where("website", "!=", "")->count() / $total) * 15;
        $score += (Restaurant::whereNotNull("opening_hours")->count() / $total) * 15;
        $score += (Restaurant::whereNotNull("image")->count() / $total) * 15;
        $score += (Restaurant::whereNotNull("yelp_id")->count() / $total) * 10;
        $score += (Restaurant::whereNotNull("google_place_id")->count() / $total) * 10;
        return (int) round($score);
    }

    public function getApiCallStats(): array
    {
        $days = match ($this->period) {
            "7days" => 7,
            "30days" => 30,
            "90days" => 90,
            default => 30,
        };
        $startDate = now()->subDays($days);
        return [
            "total_calls" => ApiCallLog::where("called_at", ">=", $startDate)->count(),
            "successful" => ApiCallLog::where("called_at", ">=", $startDate)->where("success", true)->count(),
            "failed" => ApiCallLog::where("called_at", ">=", $startDate)->where("success", false)->count(),
            "total_cost" => ApiCallLog::where("called_at", ">=", $startDate)->sum("cost"),
        ];
    }

    public function getApiCallsByService(): array
    {
        $days = match ($this->period) {
            "7days" => 7,
            "30days" => 30,
            "90days" => 90,
            default => 30,
        };
        return ApiCallLog::selectRaw("service, COUNT(*) as calls, SUM(cost) as cost")
            ->where("called_at", ">=", now()->subDays($days))
            ->groupBy("service")
            ->orderByDesc("calls")
            ->get()
            ->map(fn($item) => ["service" => ucfirst($item->service), "calls" => $item->calls, "cost" => (float) $item->cost])
            ->toArray();
    }
}

