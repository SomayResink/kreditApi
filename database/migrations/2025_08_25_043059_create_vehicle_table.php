<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('merk', 50);
            $table->string('model', 50);
            $table->year('tahun');
            $table->decimal('harga', 15, 2);
            $table->enum('kelengkapan_surat', ['complete', 'incomplete']);
            $table->decimal('kilometer', 10, 2);
            $table->string('plat_asal', 15);
            $table->text('deskripsi')->nullable();
            $table->integer('stok')->default(0);
            $table->string('gambar_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
