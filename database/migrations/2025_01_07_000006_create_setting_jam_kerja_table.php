<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_jam_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->nullable()->constrained('perusahaan')->onDelete('cascade');
            $table->foreignId('divisi_id')->nullable()->constrained('divisi')->onDelete('cascade');
            
            $table->time('jam_masuk')->default('08:00:00');
            $table->time('jam_pulang')->default('17:00:00');
            $table->integer('toleransi_terlambat')->default(15)->comment('Toleransi keterlambatan dalam menit');
            
            $table->boolean('is_default')->default(false)->comment('Setting default untuk semua');
            $table->enum('hari_kerja', ['senin-jumat', 'senin-sabtu', 'custom'])->default('senin-jumat');
            
            $table->timestamps();
            
            $table->index('perusahaan_id');
            $table->index('divisi_id');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_jam_kerja');
    }
};