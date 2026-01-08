<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('divisi', function (Blueprint $table) {
            $table->date('tanggal_awal_mou')->nullable()->after('keterangan');
            $table->date('tanggal_akhir_mou')->nullable()->after('tanggal_awal_mou');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisi', function (Blueprint $table) {
            $table->dropColumn(['tanggal_awal_mou', 'tanggal_akhir_mou']);
        });
    }
};
