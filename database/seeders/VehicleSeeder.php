<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('vehicles')->insert([
            [
                'id' => 1,
                'merk' => 'Honda',
                'model' => 'CBR150R',
                'tahun' => 2023,
                'harga' => 35000000,
                'kelengkapan_surat' => 'complete',
                'kilometer' => 5000,
                'plat_asal' => 'B',
                'deskripsi' => 'Motor sport 150cc dengan desain agresif.',
                'stok' => 10,
                'gambar_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'merk' => 'Yamaha',
                'model' => 'NMAX 155',
                'tahun' => 2024,
                'harga' => 32000000,
                'kelengkapan_surat' => 'complete',
                'kilometer' => 3000,
                'plat_asal' => 'D',
                'deskripsi' => 'Skuter matik premium dengan mesin 155cc.',
                'stok' => 8,
                'gambar_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'merk' => 'Suzuki',
                'model' => 'GSX-R150',
                'tahun' => 2022,
                'harga' => 34000000,
                'kelengkapan_surat' => 'incomplete',
                'kilometer' => 8000,
                'plat_asal' => 'E',
                'deskripsi' => 'Motor sport full fairing 150cc dari Suzuki.',
                'stok' => 5,
                'gambar_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'merk' => 'Kawasaki',
                'model' => 'Ninja 250',
                'tahun' => 2023,
                'harga' => 64000000,
                'kelengkapan_surat' => 'complete',
                'kilometer' => 2000,
                'plat_asal' => 'F',
                'deskripsi' => 'Motor sport 250cc legendaris dari Kawasaki.',
                'stok' => 4,
                'gambar_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'merk' => 'Vespa',
                'model' => 'Primavera',
                'tahun' => 2024,
                'harga' => 48500000,
                'kelengkapan_surat' => 'complete',
                'kilometer' => 1000,
                'plat_asal' => 'H',
                'deskripsi' => 'Skuter ikonik dengan desain klasik modern.',
                'stok' => 6,
                'gambar_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'merk' => 'Honda',
                'model' => 'Beat Street',
                'tahun' => 2023,
                'harga' => 18500000,
                'kelengkapan_surat' => 'incomplete',
                'kilometer' => 4000,
                'plat_asal' => 'G',
                'deskripsi' => 'Motor matik entry-level populer di Indonesia.',
                'stok' => 12,
                'gambar_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
