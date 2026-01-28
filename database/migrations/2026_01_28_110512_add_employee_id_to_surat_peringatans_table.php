<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->foreignId('employee_id')
                ->after('tanggal_sp')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnDelete()
                ->index();

            // Kalau sudah tidak dipakai, hapus kolom lama:
            $table->dropColumn(['nama_karyawan', 'nip', 'jabatan', 'divisi']);
        });
    }

    public function down(): void
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_id');
            // balikin kolom lama (kalau tadi di-drop)
            $table->string('nama_karyawan')->nullable();
            $table->string('nip')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('divisi')->nullable();
        });
    }
};
