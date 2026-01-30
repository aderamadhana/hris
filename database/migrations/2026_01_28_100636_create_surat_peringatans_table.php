<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_peringatans', function (Blueprint $table) {
        $table->id();

        $table->string('nomor_sp')->index();
        $table->date('tanggal_sp')->index();

        // periode SP
        $table->unsignedTinyInteger('periode_bulan'); // contoh: 1, 3, 6
        $table->date('tanggal_berakhir')->index();

        // data karyawan
        $table->string('nama_karyawan');
        $table->string('nip')->nullable()->index();
        $table->string('jabatan')->nullable();
        $table->string('divisi')->nullable();

        // tingkat SP
        $table->enum('tingkat', ['SP1', 'SP2', 'SP3'])
            ->default('SP1')
            ->index();

        // isi
        $table->text('pelanggaran');
        $table->date('tanggal_kejadian')->nullable();

        // file upload
        $table->string('file_path');

        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_peringatans');
    }
};
