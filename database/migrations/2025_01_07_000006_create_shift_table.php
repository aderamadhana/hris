<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift', 100);
            $table->string('kode_shift', 20)->unique();
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->integer('toleransi_keterlambatan')->default(15)->comment('dalam menit');
            $table->integer('durasi_kerja')->comment('dalam menit');
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift');
    }
};