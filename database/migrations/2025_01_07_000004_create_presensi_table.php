<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->onDelete('cascade');
            $table->foreignId('divisi_id')->constrained('divisi')->onDelete('cascade');
            
            $table->date('tanggal_presensi');
            $table->enum('jenis_presensi', ['masuk', 'pulang']);
            
            // Data Presensi
            $table->time('waktu_presensi');
            $table->string('foto_presensi')->nullable()->comment('Path foto selfie');
            
            // Data GPS
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('akurasi_gps', 10, 2)->nullable()->comment('Akurasi GPS dalam meter');
            
            // Validasi Lokasi
            $table->boolean('is_valid_location')->default(false)->comment('Apakah lokasi valid sesuai radius');
            $table->decimal('jarak_dari_lokasi', 10, 2)->nullable()->comment('Jarak dari titik penempatan dalam meter');
            
            // Status
            $table->string('status')->nullable()->default('tidak_hadir');;
            $table->text('keterangan')->nullable();
            
            // Metadata
            $table->string('device_info')->nullable()->comment('Info device yang digunakan');
            $table->string('ip_address', 45)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('employee_id');
            $table->index('tanggal_presensi');
            $table->index('jenis_presensi');
            $table->index('status');
            $table->index(['employee_id', 'tanggal_presensi', 'jenis_presensi'], 'idx_presensi_lookup');
            
            // Unique constraint: 1 karyawan hanya bisa presensi masuk/pulang 1x per hari
            // $table->unique(['employee_id', 'tanggal_presensi', 'jenis_presensi'], 'unique_presensi_harian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};