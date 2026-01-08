<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_id')
                ->nullable()
                ->default(1)
                ->after('user_id');

            // opsional tapi disarankan untuk performa query
            $table->index('shift_id');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['shift_id']); // kalau kamu pakai index di up()
            $table->dropColumn('shift_id');
        });
    }
};