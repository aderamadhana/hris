<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lokers', function (Blueprint $table) {
            $table->id();

            // Informasi utama loker
            $table->string('judul'); // contoh: Security Guard
            $table->string('slug')->unique(); // untuk URL rapi

            $table->string('tipe_pekerjaan'); // Full-time / Kontrak
            $table->integer('perusahaan_id')->nullable();
            $table->string('perusahaan_nama')->nullable();
            $table->integer('penempatan_id')->nullable();
            $table->string('penempatan_nama')->nullable();
            $table->string('jam_kerja')->nullable(); // Shift / Office / Day

            // Informasi tambahan
            $table->string('ringkasan', 255)->nullable(); // untuk tampilan card
            $table->longText('deskripsi')->nullable(); // detail lengkap
            $table->longText('persyaratan')->nullable(); // syarat/ketentuan

            // Gaji (opsional)
            $table->unsignedInteger('gaji_min')->nullable();
            $table->unsignedInteger('gaji_max')->nullable();
            $table->string('mata_uang', 3)->default('IDR');

            // Link apply (opsional)
            $table->string('link_lamar')->nullable();
            $table->string('whatsapp_kontak')->nullable();

            // Status
            $table->boolean('aktif')->default(true);
            $table->timestamp('tanggal_publish')->nullable();
            $table->timestamp('tanggal_berakhir')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index(['aktif', 'tanggal_publish']);
            $table->index('tipe_pekerjaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokers');
    }
};
