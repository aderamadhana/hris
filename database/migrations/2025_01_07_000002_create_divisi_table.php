<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->onDelete('cascade');
            $table->string('nama_divisi', 100);
            $table->string('kode_divisi', 20)->nullable();
            $table->text('alamat_penempatan')->nullable();
            
            // Koordinat GPS untuk validasi presensi
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude lokasi penempatan');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude lokasi penempatan');
            $table->integer('radius_presensi')->default(500)->comment('Radius dalam meter untuk validasi presensi');
            
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('perusahaan_id');
            $table->index('kode_divisi');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisi');
    }
};
