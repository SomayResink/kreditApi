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
            ['email' => 'admin@a.com'], // cek apakah sudah ada
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'tgl_lahir' => '2001-01-01',
                'alamat' => 'Jl. Admin No. 1',
                'no_hp' => '081219192717',
                'foto_ktp' => null,
            ]
        );
    }
}
