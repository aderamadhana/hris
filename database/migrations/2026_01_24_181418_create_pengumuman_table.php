<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();

            // Kategori badge: Recruitment / Info / Operasional
            $table->string('kategori');

            $table->string('judul');
            $table->string('slug')->unique();

            // Untuk card dan detail
            $table->string('ringkasan', 255)->nullable(); // untuk tampilan singkat
            $table->longText('isi')->nullable(); // detail lengkap

            // Status
            $table->boolean('diutamakan')->default(false); // pinned
            $table->boolean('aktif')->default(true);

            $table->timestamp('tanggal_publish')->nullable();
            $table->timestamp('tanggal_berakhir')->nullable();

            // Urutan tampil (opsional)
            $table->unsignedSmallInteger('urutan')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index(['aktif', 'tanggal_publish']);
            $table->index('kategori');
            $table->index('diutamakan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};
