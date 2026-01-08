<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rekap_presensi_harian', function (Blueprint $table) {
            $table->foreignId('shift_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('shift')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('rekap_presensi_harian', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
    }
};