<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first 3 restaurants to add menus
        $restaurants = Restaurant::limit(3)->get();

        if ($restaurants->count() === 0) {
            $this->command->warn('No restaurants found. Please seed restaurants first.');
            return;
        }

        $menuTemplates = [
            // Tacos
            [
                'name' => 'Tacos al Pastor',
                'name_en' => 'Pastor Tacos',
                'description' => 'Carne de cerdo marinada con especias y piña, servida en tortillas de maíz con cilantro y cebolla.',
                'description_en' => 'Marinated pork with spices and pineapple, served in corn tortillas with cilantro and onion.',
                'price' => 3.99,
                'category' => 'tacos',
                'spice_level' => 2,
                'dietary_options' => [],
                'ingredients' => ['Carne de cerdo', 'Piña', 'Cilantro', 'Cebolla', 'Tortilla de maíz'],
                'is_popular' => true,
                'is_available' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Tacos de Carne Asada',
                'name_en' => 'Grilled Beef Tacos',
                'description' => 'Carne de res asada a la parrilla, servida con guacamole, pico de gallo y limón.',
                'description_en' => 'Grilled beef served with guacamole, pico de gallo, and lime.',
                'price' => 4.49,
                'category' => 'tacos',
                'spice_level' => 1,
                'dietary_options' => [],
                'ingredients' => ['Carne de res', 'Guacamole', 'Pico de gallo', 'Limón'],
                'is_popular' => true,
                'is_available' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Tacos de Pescado',
                'name_en' => 'Fish Tacos',
                'description' => 'Pescado empanizado o a la parrilla con col morada, crema y salsa de chipotle.',
                'description_en' => 'Breaded or grilled fish with purple cabbage, cream, and chipotle sauce.',
                'price' => 5.49,
                'category' => 'tacos',
                'spice_level' => 2,
                'dietary_options' => [],
                'ingredients' => ['Pescado', 'Col morada', 'Crema', 'Salsa chipotle'],
                'is_popular' => false,
                'is_available' => true,
                'sort_order' => 3,
            ],
            // Burritos
            [
                'name' => 'Burrito California',
                'name_en' => 'California Burrito',
                'description' => 'Burrito grande con carne asada, papas fritas, queso, guacamole y crema.',
                'description_en' => 'Large burrito with grilled beef, french fries, cheese, guacamole, and sour cream.',
                'price' => 12.99,
                'category' => 'burritos',
                'spice_level' => 1,
                'dietary_options' => [],
                'ingredients' => ['Carne asada', 'Papas fritas', 'Queso', 'Guacamole', 'Crema'],
                'is_popular' => true,
                'is_available' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Burrito Vegetariano',
                'name_en' => 'Vegetarian Burrito',
                'description' => 'Burrito con frijoles negros, arroz, verduras asadas, queso y guacamole.',
                'description_en' => 'Burrito with black beans, rice, grilled vegetables, cheese, and guacamole.',
                'price' => 10.99,
                'category' => 'burritos',
                'spice_level' => 0,
                'dietary_options' => ['vegetarian'],
                'ingredients' => ['Frijoles negros', 'Arroz', 'Verduras', 'Queso', 'Guacamole'],
                'is_popular' => false,
                'is_available' => true,
                'sort_order' => 11,
            ],
            // Quesadillas
            [
                'name' => 'Quesadilla de Queso',
                'name_en' => 'Cheese Quesadilla',
                'description' => 'Tortilla de harina con queso fundido, servida con crema y pico de gallo.',
                'description_en' => 'Flour tortilla with melted cheese, served with sour cream and pico de gallo.',
                'price' => 7.99,
                'category' => 'quesadillas',
                'spice_level' => 0,
                'dietary_options' => ['vegetarian'],
                'ingredients' => ['Queso', 'Tortilla de harina', 'Crema', 'Pico de gallo'],
                'is_popular' => false,
                'is_available' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Quesadilla de Pollo',
                'name_en' => 'Chicken Quesadilla',
                'description' => 'Tortilla de harina con pollo asado y queso, servida con guacamole.',
                'description_en' => 'Flour tortilla with grilled chicken and cheese, served with guacamole.',
                'price' => 9.99,
                'category' => 'quesadillas',
                'spice_level' => 1,
                'dietary_options' => [],
                'ingredients' => ['Pollo', 'Queso', 'Tortilla de harina', 'Guacamole'],
                'is_popular' => true,
                'is_available' => true,
                'sort_order' => 21,
            ],
            // Enchiladas
            [
                'name' => 'Enchiladas Verdes',
                'name_en' => 'Green Enchiladas',
                'description' => 'Tortillas rellenas de pollo con salsa verde, crema y queso.',
                'description_en' => 'Tortillas filled with chicken in green sauce, cream, and cheese.',
                'price' => 13.99,
                'category' => 'enchiladas',
                'spice_level' => 2,
                'dietary_options' => [],
                'ingredients' => ['Pollo', 'Salsa verde', 'Crema', 'Queso', 'Tortilla de maíz'],
                'is_popular' => true,
                'is_available' => true,
                'sort_order' => 30,
            ],
            // Appetizers
            [
                'name' => 'Guacamole Fresco',
                'name_en' => 'Fresh Guacamole',
                'description' => 'Aguacate fresco machacado con tomate, cebolla, cilantro y limón.',
                'description_en' => 'Fresh mashed avocado with tomato, onion, cilantro, and lime.',
                'price' => 8.99,
                'category' => 'appetizers',
                'spice_level' => 0,
                'dietary_options' => ['vegan', 'vegetarian', 'gluten_free'],
                'ingredients' => ['Aguacate', 'Tomate', 'Cebolla', 'Cilantro', 'Limón'],
                'is_popular' => true,
                'is_available' => true,
                'sort_order' => 100,
            ],
            [
                'name' => 'Nachos Supremos',
                'name_en' => 'Supreme Nachos',
                'description' => 'Totopos con queso fundido, frijoles, jalapeños, crema y guacamole.',
                'description_en' => 'Tortilla chips with melted cheese, beans, jalapeños, sour cream, and guacamole.',
                'price' => 11.99,
                'category' => 'appetizers',
                'spice_level' => 3,
                'dietary_options' => ['vegetarian'],
                'ingredients' => ['Totopos', 'Queso', 'Frijoles', 'Jalapeños', 'Crema', 'Guacamole'],
                'is_popular' => true,
                'is_available' => true,
                'sort_order' => 101,
            ],
            // Desserts
            [
                'name' => 'Churros con Chocolate',
                'name_en' => 'Churros with Chocolate',
                'description' => 'Churros crujientes con azúcar y canela, servidos con chocolate caliente.',
                'description_en' => 'Crispy churros with sugar and cinnamon, served with hot chocolate.',
                'price' => 6.99,
                'category' => 'desserts',
                'spice_level' => 0,
                'dietary_options' => ['vegetarian'],
                'ingredients' => ['Harina', 'Azúcar', 'Canela', 'Chocolate'],
                'is_popular' => true,
                'is_available' => true,
                'sort_order' => 200,
            ],
            [
                'name' => 'Flan Napolitano',
                'name_en' => 'Neapolitan Flan',
                'description' => 'Flan tradicional mexicano con caramelo suave.',
                'description_en' => 'Traditional Mexican flan with soft caramel.',
                'price' => 5.99,
                'category' => 'desserts',
                'spice_level' => 0,
                'dietary_options' => ['vegetarian', 'gluten_free'],
                'ingredients' => ['Leche', 'Huevos', 'Azúcar', 'Vainilla'],
                'is_popular' => false,
                'is_available' => true,
                'sort_order' => 201,
            ],
        ];

        foreach ($restaurants as $restaurant) {
            $this->command->info("Adding menu items for: {$restaurant->name}");

            foreach ($menuTemplates as $item) {
                MenuItem::create(array_merge(['restaurant_id' => $restaurant->id], $item));
            }
        }

        $this->command->info('Menu items seeded successfully!');
        $this->command->info('Total menu items created: ' . MenuItem::count());
    }
}
