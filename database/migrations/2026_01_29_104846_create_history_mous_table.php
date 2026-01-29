<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('history_mous', function (Blueprint $table) {
            $table->id();

            // FK ke perusahaan
            $table->foreignId('perusahaan_id')
                ->constrained('perusahaan')
                ->cascadeOnDelete();

            $table->date('tanggal_awal_mou');
            $table->date('tanggal_akhir_mou');

            // path file (storage/app/public/...)
            $table->string('berkas_mou')->nullable();

            $table->text('keterangan')->nullable();
            $table->string('status')->default('aktif'); // opsional

            $table->timestamps();
            $table->softDeletes();

            $table->index(['perusahaan_id', 'tanggal_awal_mou']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_mous');
    }
};
