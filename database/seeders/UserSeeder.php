<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Owner User',
            'email' => 'owner@gmail.com',
            'password' => 'password',
            'type' => 'owner',
        ]);

        User::create([
            'name' => 'Agency User',
            'email' => 'agency@gmail.com',
            'password' => 'password',
            'type' => 'agency',
        ]);

        User::create([
            'name' => 'Business User',
            'email' => 'business@gmail.com',
            'password' => 'password',
            'type' => 'business',
        ]);
    }
}
