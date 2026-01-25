<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class InitialUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'あどみん',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'approver@example.com'],
            [
                'name' => 'あぷろーばー',
                'password' => Hash::make('password'),
                'role' => 'approver',
            ]
        );

        User::updateOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'めんばー',
                'password' => Hash::make('password'),
                'role' => 'member',
            ]
        );
    }
}