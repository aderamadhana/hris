<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surats', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_surat')->index();
            $table->date('tanggal_surat')->index();
            $table->string('perihal');

            // file upload (pdf/doc/jpg dll)
            $table->string('file_path'); // contoh: surats/abc123.pdf

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};
