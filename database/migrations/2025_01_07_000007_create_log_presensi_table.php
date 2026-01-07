<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presensi_id')->constrained('presensi')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            
            $table->enum('aksi', ['create', 'update', 'delete', 'koreksi'])->comment('Jenis aksi yang dilakukan');
            $table->text('data_sebelum')->nullable()->comment('Data sebelum perubahan (JSON)');
            $table->text('data_sesudah')->nullable()->comment('Data setelah perubahan (JSON)');
            $table->text('alasan')->nullable()->comment('Alasan perubahan/koreksi');
            
            $table->unsignedBigInteger('user_id')->nullable()->comment('Admin yang melakukan perubahan');
            $table->string('ip_address', 45)->nullable();
            
            $table->timestamps();
            
            $table->index('presensi_id');
            $table->index('employee_id');
            $table->index('aksi');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_presensi');
    }
};