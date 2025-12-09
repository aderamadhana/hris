<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nrp', 30)->unique();
            $table->string('user_id', 30)->nullable();
            $table->string('nama', 150);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('status_perkawinan', 50)->nullable();
            $table->string('kewarganegaraan', 50)->nullable();
            $table->char('status_active', 1)->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 30);
            $table->timestamps();
        });

        Schema::create('employee_personals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('no_ktp', 25)->unique();
            $table->string('no_kk', 25)->nullable();
            $table->string('npwp', 30)->nullable();

            $table->string('agama', 50)->nullable();
            $table->string('status_perkawinan', 50)->nullable();
            $table->string('kewarganegaraan', 50)->nullable();

            $table->timestamps();
        });

        Schema::create('employee_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->text('alamat_lengkap');
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('kode_pos')->nullable();

            $table->enum('tipe', ['KTP', 'Domisili'])->default('Domisili');

            $table->timestamps();
        });

        Schema::create('employee_health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->date('tanggal_mcu')->nullable();

            $table->integer('tinggi_badan')->nullable(); // cm
            $table->integer('berat_badan')->nullable(); // kg

            $table->string('gol_darah', 10)->nullable();
            $table->boolean('buta_warna')->nullable();

            $table->string('hasil_drug_test')->nullable();
            $table->date('tanggal_drug_test')->nullable();

            $table->text('riwayat_penyakit')->nullable();

            $table->timestamps();
        });

        Schema::create('employee_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('nama');
            $table->string('hubungan'); // Ayah, Ibu, Istri, Anak
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            $table->timestamps();
        });

        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('jenjang')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('institusi')->nullable();
            $table->year('tahun_lulus')->nullable();

            $table->timestamps();
        });

        Schema::create('employee_employment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('perusahaan');
            $table->string('jabatan')->nullable();
            $table->string('penempatan')->nullable();
            $table->string('no_kontrak')->nullable();
            $table->string('cost_center')->nullable();
            $table->string('tgl_daftar')->nullable();
            $table->string('keterangan_status')->nullable();
            $table->string('job_roll')->nullable();
            $table->string('masa_kerja')->nullable();
            $table->string('pola_kerja')->nullable();
            $table->string('jenis_kerja')->nullable();
            $table->string('hari_kerja')->nullable();

            $table->date('tgl_awal_kerja')->nullable();
            $table->date('tgl_akhir_kerja')->nullable();

            $table->string('jenis_kontrak')->nullable(); // PKWT, PKWTT
            $table->string('status')->nullable(); // aktif, resign, dll

            $table->timestamps();
        });

        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->integer('total')->default(0);
            $table->integer('success')->default(0);
            $table->integer('failed')->default(0);
            $table->integer('expected_total')->default(0);
            $table->string('status')->default('processing'); // processing, completed, failed
            $table->json('errors')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};