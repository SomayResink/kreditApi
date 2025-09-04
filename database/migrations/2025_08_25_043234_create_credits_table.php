<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->decimal('cicilan', 15, 2);
            $table->decimal('dp', 15, 2);
            $table->integer('tenor');
            $table->decimal('cicilan_per_bulan', 15, 2);
            $table->decimal('total_bayar', 15, 2);
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->date('tanggal_pengajuan');
            $table->integer('remaining_tenor')->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
