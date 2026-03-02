<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$templates = [
    "Discover authentic Mexican flavors at {name} in {city}, {state}. A beloved local spot serving delicious traditional cuisine.",
    "{name} brings the taste of Mexico to {city}, {state}. Experience genuine Mexican hospitality and flavorful dishes.",
    "Located in {city}, {state}, {name} is your destination for authentic Mexican food. Fresh ingredients and traditional recipes.",
    "Experience the vibrant flavors of Mexico at {name} in {city}. A favorite among locals for its authentic Mexican cuisine.",
    "{name} in {city}, {state} serves up traditional Mexican dishes with a passion for authentic flavors and quality ingredients.",
    "Craving authentic Mexican food? Visit {name} in {city}, {state} for a true taste of Mexico.",
    "Family-friendly {name} offers delicious Mexican cuisine in the heart of {city}, {state}. Fresh, flavorful, and authentic.",
    "At {name}, located in {city}, {state}, every dish celebrates the rich culinary traditions of Mexico.",
    "{name} is {city}'s go-to spot for authentic Mexican food. Serving traditional favorites with fresh, quality ingredients.",
    "Welcome to {name} in {city}, {state} - where traditional Mexican recipes meet warm, friendly service."
];

$count = 0;

App\Models\Restaurant::with('state')->chunk(50, function($restaurants) use ($templates, &$count) {
    foreach($restaurants as $restaurant) {
        $template = $templates[array_rand($templates)];
        $description = str_replace(
            ['{name}', '{city}', '{state}'],
            [$restaurant->name, $restaurant->city, $restaurant->state->name],
            $template
        );
        $restaurant->update(['description' => $description]);
        $count++;
        echo ".";
    }
});

echo "\n✅ Updated {$count} restaurant descriptions.\n";
