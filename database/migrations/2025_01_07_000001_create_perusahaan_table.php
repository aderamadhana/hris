<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_perusahaan', 20)->unique()->comment('Kode inisial perusahaan untuk generate no kontrak');
            $table->string('nama_perusahaan', 200);
            $table->text('alamat');
            $table->date('tanggal_awal_mou')->nullable();
            $table->date('tanggal_akhir_mou')->nullable();
            $table->string('berkas_mou')->nullable()->comment('Path file MOU');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('kode_perusahaan');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan');
    }
};