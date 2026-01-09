<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_presensi_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->onDelete('cascade');
            $table->foreignId('divisi_id')->constrained('divisi')->onDelete('cascade');
            
            $table->date('tanggal');
            
            // Presensi Masuk
            $table->time('waktu_masuk')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->decimal('lat_masuk', 10, 8)->nullable();
            $table->decimal('long_masuk', 11, 8)->nullable();
            $table->boolean('valid_lokasi_masuk')->default(false);
            
            // Presensi Pulang
            $table->time('waktu_pulang')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->decimal('lat_pulang', 10, 8)->nullable();
            $table->decimal('long_pulang', 11, 8)->nullable();
            $table->boolean('valid_lokasi_pulang')->default(false);
            
            // Status Kehadiran
            $table->string('status_kehadiran')->nullable()->default('tidak_hadir');
            
            // Durasi Kerja
            $table->integer('total_jam_kerja')->nullable()->comment('dalam menit');
            $table->integer('durasi_kerja_menit')->nullable()->comment('Durasi kerja dalam menit');
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('employee_id');
            $table->index('tanggal');
            $table->index('status_kehadiran');
            $table->index(['perusahaan_id', 'tanggal']);
            $table->index(['divisi_id', 'tanggal']);
            
            // Unique constraint
            $table->unique(['employee_id', 'tanggal'], 'unique_rekap_harian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_presensi_harian');
    }
};