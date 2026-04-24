<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Varilla', 'unit' => 'Pieza', 'stock' => 1000, 'price' => 150.00],
            ['name' => 'Block de concreto', 'unit' => 'Pieza', 'stock' => 1000, 'price' => 12.50],
            ['name' => 'Bulto de cemento', 'unit' => 'Bulto', 'stock' => 1000, 'price' => 210.00],
            ['name' => 'Bulto de arena', 'unit' => 'Bulto', 'stock' => 1000, 'price' => 45.50],
            ['name' => 'Bulto de grava', 'unit' => 'Bulto', 'stock' => 1000, 'price' => 50.00],
        ];

        foreach ($products as $p) {
            \App\Models\Product::create($p);
        }
    }
}
