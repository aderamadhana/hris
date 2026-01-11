<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_mou', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->onDelete('cascade');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->string('berkas_mou')->nullable()->comment('Path file MOU');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'expired'])->default('aktif');
            $table->timestamps();
            
            $table->index('perusahaan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_mou');
    }
};