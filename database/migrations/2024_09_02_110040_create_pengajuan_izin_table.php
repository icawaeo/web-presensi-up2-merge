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
        // Saya ganti nama tabelnya menjadi 'pengajuan_izin' agar lebih jelas
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();
            $table->string('nik'); // Menggunakan NIK agar konsisten
            $table->date('tanggal_izin');
            $table->char('status', 1); // 'i' untuk izin, 's' untuk sakit
            $table->text('keterangan')->nullable();
            // Default '0' artinya PENDING
            $table->char('status_approved', 1)->default('0'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};