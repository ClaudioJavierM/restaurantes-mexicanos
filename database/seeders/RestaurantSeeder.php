<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            // Pedro's Tacos and Tequila - Multiple locations
            [
                'name' => "Pedro's Tacos and Tequila",
                'description' => 'Authentic Mexican cuisine with a modern twist. Famous for their handmade tacos and premium tequila selection.',
                'address' => '123 Main St',
                'city' => 'Gulfport',
                'state' => 'Mississippi',
                'zip_code' => '39501',
                'phone' => '(228) 555-0100',
                'email' => 'info@pedrostacos.com',
                'category' => 'Tacos',
                'is_featured' => true,
            ],
            [
                'name' => "Pedro's Tacos and Tequila",
                'description' => 'Authentic Mexican cuisine with a modern twist. Famous for their handmade tacos and premium tequila selection.',
                'address' => '456 Oak Ave',
                'city' => 'Mobile',
                'state' => 'Alabama',
                'zip_code' => '36602',
                'phone' => '(251) 555-0200',
                'email' => 'mobile@pedrostacos.com',
                'category' => 'Tacos',
                'is_featured' => true,
            ],

            // El Paso Mexican Grill
            [
                'name' => 'El Paso Mexican Grill',
                'description' => 'Traditional Mexican food with generous portions. Known for their sizzling fajitas and fresh margaritas.',
                'address' => '789 River Rd',
                'city' => 'Jackson',
                'state' => 'Mississippi',
                'zip_code' => '39201',
                'phone' => '(601) 555-0300',
                'email' => 'info@elpasogrill.com',
                'category' => 'Comida Regional',
                'is_featured' => true,
            ],
            [
                'name' => 'El Paso Mexican Grill',
                'description' => 'Traditional Mexican food with generous portions. Known for their sizzling fajitas and fresh margaritas.',
                'address' => '321 Bourbon St',
                'city' => 'New Orleans',
                'state' => 'Louisiana',
                'zip_code' => '70112',
                'phone' => '(504) 555-0400',
                'email' => 'nola@elpasogrill.com',
                'category' => 'Comida Regional',
                'is_featured' => false,
            ],

            // Los Parrileros Mexican Grill
            [
                'name' => 'Los Parrileros Mexican Grill',
                'description' => 'Grilled specialties and traditional Mexican dishes. Family-owned restaurant with authentic flavors.',
                'address' => '567 Highland Ave',
                'city' => 'Hattiesburg',
                'state' => 'Mississippi',
                'zip_code' => '39401',
                'phone' => '(601) 555-0500',
                'email' => 'info@losparrileros.com',
                'category' => 'Carnitas',
                'is_featured' => true,
            ],

            // 7 Leguas Mexican Restaurant
            [
                'name' => '7 Leguas Mexican Restaurant',
                'description' => 'Named after the famous revolutionary horse, serving authentic Mexican cuisine with a rich history.',
                'address' => '890 Washington Ave',
                'city' => 'Montgomery',
                'state' => 'Alabama',
                'zip_code' => '36104',
                'phone' => '(334) 555-0600',
                'email' => 'montgomery@7leguas.com',
                'category' => 'Comida Regional',
                'is_featured' => false,
            ],
            [
                'name' => '7 Leguas Mexican Restaurant',
                'description' => 'Named after the famous revolutionary horse, serving authentic Mexican cuisine with a rich history.',
                'address' => '234 Main St',
                'city' => 'Houston',
                'state' => 'Texas',
                'zip_code' => '77002',
                'phone' => '(713) 555-0700',
                'email' => 'houston@7leguas.com',
                'category' => 'Comida Regional',
                'is_featured' => true,
            ],

            // El Norte Mexican Restaurant
            [
                'name' => 'El Norte Mexican Restaurant',
                'description' => 'Northern Mexican specialties including cabrito and flour tortillas. Family recipes passed down for generations.',
                'address' => '456 22nd Ave',
                'city' => 'Meridian',
                'state' => 'Mississippi',
                'zip_code' => '39301',
                'phone' => '(601) 555-0800',
                'email' => 'info@elnorte.com',
                'category' => 'Comida Regional',
                'is_featured' => false,
            ],

            // El Jalisco
            [
                'name' => 'El Jalisco',
                'description' => 'Flavors from Jalisco, Mexico. Specializing in birria, tortas ahogadas, and traditional jalisco dishes.',
                'address' => '678 Monroe St',
                'city' => 'Tallahassee',
                'state' => 'Florida',
                'zip_code' => '32301',
                'phone' => '(850) 555-0900',
                'email' => 'tallahassee@eljalisco.com',
                'category' => 'Birria',
                'is_featured' => true,
            ],
            [
                'name' => 'El Jalisco',
                'description' => 'Flavors from Jalisco, Mexico. Specializing in birria, tortas ahogadas, and traditional jalisco dishes.',
                'address' => '123 S County Rd 267',
                'city' => 'Crawfordville',
                'state' => 'Florida',
                'zip_code' => '32327',
                'phone' => '(850) 555-1000',
                'email' => 'crawfordville@eljalisco.com',
                'category' => 'Birria',
                'is_featured' => false,
            ],

            // El Ranchito
            [
                'name' => 'El Ranchito',
                'description' => 'Traditional ranch-style Mexican food. Famous for their carne asada and homemade tortillas.',
                'address' => '789 Elm St',
                'city' => 'Dallas',
                'state' => 'Texas',
                'zip_code' => '75201',
                'phone' => '(214) 555-1100',
                'email' => 'info@elranchito.com',
                'category' => 'Barbacoa',
                'is_featured' => true,
            ],

            // Blue Margaritas Mexican Bar
            [
                'name' => 'Blue Margaritas Mexican Bar',
                'description' => 'Vibrant Mexican bar and grill. Known for their signature blue margaritas and lively atmosphere.',
                'address' => '234 Main St',
                'city' => 'Peoria',
                'state' => 'Illinois',
                'zip_code' => '61602',
                'phone' => '(309) 555-1200',
                'email' => 'info@bluemargaritas.com',
                'category' => 'Antojitos',
                'is_featured' => false,
            ],

            // Jalapeño Grill
            [
                'name' => 'Jalapeño Grill',
                'description' => 'Fresh Mexican grill with a spicy kick. Everything made fresh daily with quality ingredients.',
                'address' => '567 Beach Blvd',
                'city' => 'Gulfport',
                'state' => 'Mississippi',
                'zip_code' => '39501',
                'phone' => '(228) 555-1300',
                'email' => 'info@jalapenogrill.com',
                'category' => 'Tacos',
                'is_featured' => true,
            ],

            // Don Chuy
            [
                'name' => 'Don Chuy',
                'description' => "Don Chuy's authentic Mexican recipes. Family atmosphere with homestyle cooking.",
                'address' => '890 Thomas Rd',
                'city' => 'West Monroe',
                'state' => 'Louisiana',
                'zip_code' => '71291',
                'phone' => '(318) 555-1400',
                'email' => 'info@donchuy.com',
                'category' => 'Cocina Casera',
                'is_featured' => false,
            ],

            // Nuevo León
            [
                'name' => 'Nuevo León',
                'description' => 'Cuisine from Nuevo León, Mexico. Specializing in cabrito, carne asada, and northern Mexican favorites.',
                'address' => '345 Valley View Ln',
                'city' => 'Farmers Branch',
                'state' => 'Texas',
                'zip_code' => '75234',
                'phone' => '(972) 555-1500',
                'email' => 'info@nuevoleon.com',
                'category' => 'Comida Regional',
                'is_featured' => true,
            ],
        ];

        foreach ($restaurants as $restaurantData) {
            // Find state
            $state = \App\Models\State::where('name', $restaurantData['state'])->first();
            if (!$state) continue;

            // Find or create category
            $category = \App\Models\Category::where('name', $restaurantData['category'])->first();
            if (!$category) continue;

            // Create restaurant
            \App\Models\Restaurant::create([
                'state_id' => $state->id,
                'category_id' => $category->id,
                'name' => $restaurantData['name'],
                'description' => $restaurantData['description'],
                'address' => $restaurantData['address'],
                'city' => $restaurantData['city'],
                'zip_code' => $restaurantData['zip_code'],
                'phone' => $restaurantData['phone'],
                'email' => $restaurantData['email'],
                'status' => 'approved',
                'is_featured' => $restaurantData['is_featured'],
                'is_active' => true,
                'average_rating' => rand(35, 50) / 10, // Random rating between 3.5 and 5.0
                'total_reviews' => rand(10, 100),
            ]);
        }
    }
}
