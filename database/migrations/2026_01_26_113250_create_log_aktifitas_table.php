<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktifitas', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete()
                ->index();

            $table->foreignId('aktifitas_id')
                ->nullable()
                ->constrained('aktifitas')
                ->nullOnDelete()
                ->index();

            // Data dari Excel
            $table->unsignedInteger('no_urut')->nullable(); // kolom "No"
            $table->string('reg', 50)->nullable()->index();
            $table->string('nama', 120)->nullable();
            $table->date('tgl')->nullable()->index();
            $table->unsignedInteger('shift', 20)->nullable();
            $table->string('bag', 50)->nullable();
            $table->string('lo', 10)->nullable(); // kolom "L/O"

            // Jam kerja
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->unsignedInteger('jam_kerja_menit')->nullable(); // simpan menit biar gampang hitung

            // Hasil kerja
            $table->string('kode_kerja', 20)->nullable()->index();
            $table->unsignedInteger('hasil_kerja')->default(0);
            $table->unsignedInteger('hasil_lembur')->default(0);
            $table->unsignedInteger('return_qty')->default(0);
            $table->unsignedInteger('tolak_qc')->default(0);

            // SCF (rupiah)
            $table->unsignedBigInteger('upah_scf')->default(0);
            $table->unsignedBigInteger('bantu_scf')->default(0);
            $table->unsignedBigInteger('denda_scf')->default(0);
            $table->unsignedBigInteger('total_scf')->default(0);

            // ACT (rupiah)
            $table->unsignedBigInteger('upah_act')->default(0);
            $table->unsignedBigInteger('upah_bantu_act')->default(0);
            $table->unsignedBigInteger('return_act')->default(0);
            $table->unsignedBigInteger('denda_act')->default(0);
            $table->unsignedBigInteger('total_act')->default(0);

            $table->text('ket')->nullable();

            $table->timestamps();

            // Unik per karyawan + tanggal + shift + aktifitas
            $table->unique(
                ['employee_id', 'tgl', 'shift', 'aktifitas_id'],
                'uniq_log_aktifitas'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktifitas');
    }
};
