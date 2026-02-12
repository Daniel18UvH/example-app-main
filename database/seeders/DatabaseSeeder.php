<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Definimos los administradores del sistema
        $admins = [
            [
                'name'  => 'Daniel Admin',
                'email' => 'satafykerplay@gmail.com',
            ],
            [
                'name'  => 'Admin Prueba',
                'email' => 'admin@prueba.com',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']], // Si ya existe, no lo duplica, solo lo actualiza
                [
                    'name'              => $admin['name'],
                    'password'          => Hash::make('12345678'),
                    'email_verified_at' => now(),
                    // Si tienes una columna 'role', descomenta la línea de abajo:
                    // 'role' => 'admin', 
                ]
            );
        }

        // Usuario de prueba adicional (opcional)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}