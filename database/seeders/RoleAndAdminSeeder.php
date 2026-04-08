<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear los Roles
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Sales', 'slug' => 'sales'],
            ['name' => 'Purchasing', 'slug' => 'purchasing'],
            ['name' => 'Warehouse', 'slug' => 'warehouse'],
            ['name' => 'Route', 'slug' => 'route'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }

        $adminId = Role::where('slug', 'admin')->first()->id;
        $salesId = Role::where('slug', 'sales')->first()->id;
        $purchasingId = Role::where('slug', 'purchasing')->first()->id;
        $warehouseId = Role::where('slug', 'warehouse')->first()->id;
        $routeId = Role::where('slug', 'route')->first()->id;

        
        // Victor - ADMIN
        User::firstOrCreate(
            ['email' => 'admin@halcon.com'],
            [
                'name' => 'Victor',
                'password' => Hash::make('12345678'),
                'role_id' => $adminId,
            ]
        );

        // Emiliano - SALES
        User::firstOrCreate(
            ['email' => 'ventas@halcon.com'],
            [
                'name' => 'Emiliano',
                'password' => Hash::make('12345678'),
                'role_id' => $salesId,
            ]
        );

        // Antonio - WAREHOUSE
        User::firstOrCreate(
            ['email' => 'almacen@halcon.com'],
            [
                'name' => 'Antonio',
                'password' => Hash::make('12345678'),
                'role_id' => $warehouseId,
            ]
        );

        // Jordan - ROUTE
        User::firstOrCreate(
            ['email' => 'ruta@halcon.com'],
            [
                'name' => 'Jordan',
                'password' => Hash::make('12345678'),
                'role_id' => $routeId,
            ]
        );

        // Karla - PURCHASING
        User::firstOrCreate(
            ['email' => 'compras@halcon.com'],
            [
                'name' => 'Karla',
                'password' => Hash::make('12345678'),
                'role_id' => $purchasingId,
            ]
        );
    }
}