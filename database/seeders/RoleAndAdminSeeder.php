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
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Sales', 'slug' => 'sales'],
            ['name' => 'Purchasing', 'slug' => 'purchasing'],
            ['name' => 'Warehouse', 'slug' => 'warehouse'],
            ['name' => 'Route', 'slug' => 'route'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
        User::create([
            'name' => 'Victor',
            'email' => 'admin@halcon.com',
            'password' => Hash::make('12345678'),
            'role_id' => 1,
        ]);
    }
}