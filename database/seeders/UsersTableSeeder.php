<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verifica si el usuario administrador ya existe
        $adminUser = User::where('email', 'admin@admin.com')->first();

        if (!$adminUser) {
            // Crea el usuario administrador
            User::create([
                'name' => 'Alvaro',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin'),
                'role' => 'admin',
            ]);
        }
    }
}
