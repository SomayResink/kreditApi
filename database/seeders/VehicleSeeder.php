<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicles')->delete(); // hapus semua data dulu

        DB::table('vehicles')->insert([
            [
                'merk' => 'Honda',
                'model' => 'CBR150R',
                'tahun' => 2023,
                'harga' => 35000000,
                'deskripsi' => 'Motor sport 150cc dengan desain agresif.',
                'stok' => 10,
                'gambar_url' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merk' => 'Yamaha',
                'model' => 'NMAX 155',
                'tahun' => 2024,
                'harga' => 32000000,
                'deskripsi' => 'Skuter matik premium dengan mesin 155cc.',
                'stok' => 8,
                'gambar_url' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merk' => 'Suzuki',
                'model' => 'GSX-R150',
                'tahun' => 2022,
                'harga' => 34000000,
                'deskripsi' => 'Motor sport full fairing 150cc dari Suzuki.',
                'stok' => 5,
                'gambar_url' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merk' => 'Kawasaki',
                'model' => 'Ninja 250',
                'tahun' => 2023,
                'harga' => 64000000,
                'deskripsi' => 'Motor sport 250cc legendaris dari Kawasaki.',
                'stok' => 4,
                'gambar_url' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merk' => 'Vespa',
                'model' => 'Primavera',
                'tahun' => 2024,
                'harga' => 48500000,
                'deskripsi' => 'Skuter ikonik dengan desain klasik modern.',
                'stok' => 6,
                'gambar_url' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merk' => 'Honda',
                'model' => 'Beat Street',
                'tahun' => 2023,
                'harga' => 18500000,
                'deskripsi' => 'Motor matik entry-level populer di Indonesia.',
                'stok' => 12,
                'gambar_url' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
