<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Only account in the `users` table — the public site has no login.
        User::factory()->create([
            'name' => 'Admin Gunadarma',
            'email' => 'admin@gunadarma.ac.id',
            'password' => 'password',
        ]);

        $this->call([
            CategorySeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
