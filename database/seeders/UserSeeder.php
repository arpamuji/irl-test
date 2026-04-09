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
        // User with permissions to manage clients
        User::create([
            'name' => 'Admin',
            'email' => 'admin@irl.com',
            'password' => Hash::make('password'),
            'can_manage_clients' => true,
        ]);

        // User without permissions to manage clients
        User::create([
            'name' => 'User',
            'email' => 'user@irl.com',
            'password' => Hash::make('password'),
            'can_manage_clients' => false,
        ]);
    }
}
