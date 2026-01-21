<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'City7gor',
            'email' => 'City7gor@gmail.com',
            'password' => Hash::make('123123123'),
            'isAdmin' => true,
        ]);

        User::factory(100)->create();
    }
}
