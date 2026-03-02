<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\Favorite;
use App\Models\MenuItem;
use App\Models\State;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean previous test data
        $this->command->info('Cleaning previous test data...');
        Restaurant::whereIn('slug', ['la-casa-de-tono', 'el-mariachi-loco', 'tacos-el-guero'])->forceDelete();
        User::whereIn('email', [
            'juan@example.com',
            'maria@example.com',
            'carlos@example.com',
            'roberto.owner@example.com',
            'laura.owner@example.com'
        ])->forceDelete();

        // Get some states and categories
        $california = State::where('code', 'CA')->first();
        $texas = State::where('code', 'TX')->first();
        $florida = State::where('code', 'FL')->first();

        // Check if states exist
        if (!$california || !$texas || !$florida) {
            $this->command->error('❌ States not found! Please run StatesSeeder first.');
            $this->command->info('Run: php artisan db:seed --class=StatesSeeder');
            return;
        }

        $mexicanCategory = Category::where('slug', 'mexican')->orWhere('slug', 'mexicana')->first();
        if (!$mexicanCategory) {
            $mexicanCategory = Category::create([
                'name' => 'Mexican',
                'slug' => 'mexican',
                'description' => 'Authentic Mexican cuisine',
            ]);
        }

        $texMexCategory = Category::where('slug', 'tex-mex')->first();
        if (!$texMexCategory) {
            $texMexCategory = Category::create([
                'name' => 'Tex-Mex',
                'slug' => 'tex-mex',
                'description' => 'Texan-Mexican fusion cuisine',
            ]);
        }

        // Create test users (regular users)
        $users = [];
        $users[] = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);

        $users[] = User::create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);

        $users[] = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);

        // Create test owner users
        $owners = [];
        $owners[] = User::create([
            'name' => 'Roberto González',
            'email' => 'roberto.owner@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'owner',
        ]);

        $owners[] = User::create([
            'name' => 'Laura Martínez',
            'email' => 'laura.owner@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'owner',
        ]);

        // Create test restaurants
        $restaurants = [];

        $restaurants[] = Restaurant::create([
            'name' => 'La Casa de Toño',
            'slug' => 'la-casa-de-tono',
            'user_id' => $owners[0]->id,
            'category_id' => $mexicanCategory?->id,
            'state_id' => $california?->id,
            'address' => '123 Main St',
            'city' => 'Los Angeles',
            'zip_code' => '90001',
            'phone' => '(323) 555-0101',
            'email' => 'info@lacasadetono.com',
            'website' => 'https://lacasadetono.com',
            'description' => 'Auténtica comida mexicana en el corazón de Los Angeles. Especialidades de Jalisco con recetas tradicionales.',
            'latitude' => 34.0522,
            'longitude' => -118.2437,
            'mexican_region' => 'Jalisco',
            'accepts_reservations' => true,
            'special_features' => json_encode(['delivery', 'takeout', 'outdoor_seating']),
            'atmosphere' => json_encode(['family_friendly', 'casual']),
            'dietary_options' => json_encode(['vegetarian', 'gluten_free']),
            'traditional_recipes' => true,
            'price_range' => '$$',
            'status' => 'approved',
        ]);

        $restaurants[] = Restaurant::create([
            'name' => 'El Mariachi Loco',
            'slug' => 'el-mariachi-loco',
            'user_id' => $owners[1]->id,
            'category_id' => $texMexCategory?->id ?? $mexicanCategory?->id,
            'state_id' => $texas?->id,
            'address' => '456 Oak Ave',
            'city' => 'Houston',
            'zip_code' => '77001',
            'phone' => '(713) 555-0202',
            'email' => 'contact@elmariachiloco.com',
            'website' => 'https://elmariachiloco.com',
            'description' => 'Tex-Mex tradicional con ambiente festivo y mariachi los fines de semana. Fajitas especiales y margaritas.',
            'latitude' => 29.7604,
            'longitude' => -95.3698,
            'mexican_region' => 'Tex-Mex',
            'accepts_reservations' => true,
            'special_features' => json_encode(['live_music', 'mariachi', 'bar', 'parking']),
            'atmosphere' => json_encode(['family_friendly', 'romantic']),
            'dietary_options' => json_encode(['vegetarian']),
            'price_range' => '$$$',
            'status' => 'approved',
        ]);

        $restaurants[] = Restaurant::create([
            'name' => 'Tacos El Güero',
            'slug' => 'tacos-el-guero',
            'user_id' => $owners[0]->id,
            'category_id' => $mexicanCategory?->id,
            'state_id' => $florida?->id,
            'address' => '789 Beach Blvd',
            'city' => 'Miami',
            'zip_code' => '33101',
            'phone' => '(305) 555-0303',
            'email' => 'info@tacoselguero.com',
            'description' => 'Los mejores tacos al pastor de Miami. Recetas familiares de Michoacán con tortillas hechas a mano.',
            'latitude' => 25.7617,
            'longitude' => -80.1918,
            'mexican_region' => 'Michoacán',
            'accepts_reservations' => false,
            'special_features' => json_encode(['delivery', 'takeout', 'outdoor_seating']),
            'atmosphere' => json_encode(['family_friendly', 'casual']),
            'dietary_options' => json_encode(['vegetarian']),
            'traditional_recipes' => true,
            'price_range' => '$',
            'status' => 'approved',
        ]);

        // Create menu items for each restaurant
        $menuItems = [
            // La Casa de Toño
            [
                'restaurant_id' => $restaurants[0]->id,
                'name' => 'Pozole Rojo',
                'description' => 'Traditional hominy soup with pork, garnished with lettuce, radish, and oregano',
                'price' => 14.99,
                'category' => 'Sopas',
                'dietary_options' => json_encode(['gluten_free']),
                'spice_level' => 3,
                'is_popular' => true,
            ],
            [
                'restaurant_id' => $restaurants[0]->id,
                'name' => 'Enchiladas Verdes',
                'description' => 'Rolled tortillas filled with chicken, topped with green tomatillo sauce',
                'price' => 13.99,
                'category' => 'Platillos Principales',
                'dietary_options' => json_encode(['gluten_free']),
                'spice_level' => 2,
            ],
            [
                'restaurant_id' => $restaurants[0]->id,
                'name' => 'Chiles Rellenos',
                'description' => 'Poblano peppers stuffed with cheese, egg battered and fried',
                'price' => 15.99,
                'category' => 'Platillos Principales',
                'dietary_options' => json_encode(['vegetarian', 'gluten_free']),
                'spice_level' => 3,
            ],
            // El Mariachi Loco
            [
                'restaurant_id' => $restaurants[1]->id,
                'name' => 'Fajitas Mixtas',
                'description' => 'Sizzling beef, chicken and shrimp with peppers and onions',
                'price' => 22.99,
                'category' => 'Fajitas',
                'dietary_options' => json_encode(['gluten_free']),
                'spice_level' => 3,
                'is_popular' => true,
            ],
            [
                'restaurant_id' => $restaurants[1]->id,
                'name' => 'Queso Fundido',
                'description' => 'Melted cheese with chorizo, served with tortillas',
                'price' => 9.99,
                'category' => 'Appetizers',
                'dietary_options' => json_encode([]),
                'spice_level' => 2,
            ],
            // Tacos El Güero
            [
                'restaurant_id' => $restaurants[2]->id,
                'name' => 'Tacos al Pastor',
                'description' => 'Marinated pork with pineapple on corn tortillas',
                'price' => 3.50,
                'category' => 'Tacos',
                'dietary_options' => json_encode(['gluten_free']),
                'spice_level' => 3,
                'is_popular' => true,
            ],
            [
                'restaurant_id' => $restaurants[2]->id,
                'name' => 'Tacos de Carnitas',
                'description' => 'Slow-cooked pork on corn tortillas with onion and cilantro',
                'price' => 3.25,
                'category' => 'Tacos',
                'dietary_options' => json_encode(['gluten_free']),
                'spice_level' => 2,
            ],
            [
                'restaurant_id' => $restaurants[2]->id,
                'name' => 'Tacos de Rajas',
                'description' => 'Poblano pepper strips with corn and cheese',
                'price' => 3.00,
                'category' => 'Tacos',
                'dietary_options' => json_encode(['vegetarian', 'gluten_free']),
                'spice_level' => 2,
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }

        // Create reviews
        Review::create([
            'restaurant_id' => $restaurants[0]->id,
            'user_id' => $users[0]->id,
            'rating' => 5,
            'comment' => '¡Excelente comida! El pozole me recordó a mi abuela. Muy auténtico y delicioso.',
            'status' => 'approved',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        Review::create([
            'restaurant_id' => $restaurants[0]->id,
            'user_id' => $users[1]->id,
            'rating' => 4,
            'comment' => 'Great authentic Mexican food. The enchiladas verdes were amazing!',
            'status' => 'approved',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        Review::create([
            'restaurant_id' => $restaurants[1]->id,
            'user_id' => $users[2]->id,
            'rating' => 5,
            'comment' => 'Best Tex-Mex in Houston! The mariachi on Friday nights is fantastic.',
            'status' => 'approved',
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ]);

        Review::create([
            'restaurant_id' => $restaurants[1]->id,
            'user_id' => $users[0]->id,
            'rating' => 4,
            'comment' => 'Fajitas were delicious and portions are huge. Great value!',
            'status' => 'approved',
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(7),
        ]);

        Review::create([
            'restaurant_id' => $restaurants[2]->id,
            'user_id' => $users[1]->id,
            'rating' => 5,
            'comment' => 'Los mejores tacos al pastor que he probado en Miami. ¡Increíbles!',
            'status' => 'approved',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        Review::create([
            'restaurant_id' => $restaurants[2]->id,
            'user_id' => $users[2]->id,
            'rating' => 5,
            'comment' => 'Quick service, authentic taste, and affordable prices. Perfect!',
            'status' => 'approved',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        // Create favorites
        Favorite::create([
            'user_id' => $users[0]->id,
            'restaurant_id' => $restaurants[0]->id,
        ]);

        Favorite::create([
            'user_id' => $users[0]->id,
            'restaurant_id' => $restaurants[2]->id,
        ]);

        Favorite::create([
            'user_id' => $users[1]->id,
            'restaurant_id' => $restaurants[0]->id,
        ]);

        Favorite::create([
            'user_id' => $users[1]->id,
            'restaurant_id' => $restaurants[1]->id,
        ]);

        Favorite::create([
            'user_id' => $users[2]->id,
            'restaurant_id' => $restaurants[1]->id,
        ]);

        $this->command->info('✅ Test data created successfully!');
        $this->command->info('📧 Test users created:');
        $this->command->info('   - juan@example.com (password: password)');
        $this->command->info('   - maria@example.com (password: password)');
        $this->command->info('   - carlos@example.com (password: password)');
        $this->command->info('👔 Test owners created:');
        $this->command->info('   - roberto.owner@example.com (password: password)');
        $this->command->info('   - laura.owner@example.com (password: password)');
        $this->command->info('🍴 Restaurants: ' . count($restaurants));
        $this->command->info('📋 Menu items: ' . count($menuItems));
        $this->command->info('⭐ Reviews: 6');
        $this->command->info('❤️ Favorites: 5');
    }
}
