<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём тестового пользователя
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'driver',
        ]);

        // Генерируем ещё 10 пользователей
        User::factory(10)->create();
    }
}
