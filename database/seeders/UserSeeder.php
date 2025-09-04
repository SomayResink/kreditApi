<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // cek apakah sudah ada
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin', // pastikan kolom role ada di tabel users
            ]
        );
    }
}
