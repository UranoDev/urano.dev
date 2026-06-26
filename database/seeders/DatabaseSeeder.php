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
        if (! User::where('email', 'admin@uranodev.com')->exists()) {
            User::factory()->admin()->create([
                'name' => 'Admin',
                'email' => 'admin@uranodev.com',
            ]);
        }

        if (! User::where('email', 'author@uranodev.com')->exists()) {
            User::factory()->author()->create([
                'name' => 'Autor Test',
                'email' => 'author@uranodev.com',
            ]);
        }

        if (! User::where('email', 'visitor@uranodev.com')->exists()) {
            User::factory()->visitor()->create([
                'name' => 'Visitante Test',
                'email' => 'visitor@uranodev.com',
            ]);
        }

        $this->call([
            PostSeeder::class,
            ServiceSeeder::class,        ]);
    }
}
