<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Tacos', 'description' => 'Restaurantes especializados en tacos'],
            ['name' => 'Mariscos', 'description' => 'Restaurantes de mariscos y pescados'],
            ['name' => 'Burritos', 'description' => 'Restaurantes especializados en burritos'],
            ['name' => 'Tortas', 'description' => 'Tortas mexicanas y sandwiches'],
            ['name' => 'Antojitos', 'description' => 'Antojitos mexicanos variados'],
            ['name' => 'Birria', 'description' => 'Especialistas en birria'],
            ['name' => 'Carnitas', 'description' => 'Restaurantes de carnitas'],
            ['name' => 'Barbacoa', 'description' => 'Especialistas en barbacoa'],
            ['name' => 'Comida Regional', 'description' => 'Comida de diferentes regiones de México'],
            ['name' => 'Taquería', 'description' => 'Taquerías tradicionales'],
            ['name' => 'Cocina Casera', 'description' => 'Comida casera mexicana'],
            ['name' => 'Panadería', 'description' => 'Pan dulce y panadería mexicana'],
            ['name' => 'Paletería', 'description' => 'Paletas, helados y snacks mexicanos'],
            ['name' => 'Tamales', 'description' => 'Especialistas en tamales'],
            ['name' => 'Pozole', 'description' => 'Restaurantes de pozole'],
            ['name' => 'Menudo', 'description' => 'Especialistas en menudo'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
