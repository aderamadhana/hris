<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 30);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nrp', 30)->unique();
            $table->string('user_id', 30)->nullable();
            $table->string('nama', 150);
            $table->string('bagian', 50)->nullable();
            $table->string('area_kerja', 50)->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->change();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('status_kary', 50)->nullable();
            $table->string('status_perkawinan', 50)->nullable();
            $table->string('kewarganegaraan', 50)->nullable();
            $table->char('status_active', 1)->nullable();
            
            // Gabungan dari employee_personals
            $table->string('no_ktp', 25)->unique()->nullable();
            $table->string('no_kk', 25)->nullable();
            $table->string('no_wa', 100)->nullable();
            $table->string('bpjs_tk', 100)->nullable();
            $table->string('jenis_bpjs_tk', 100)->nullable();
            $table->string('status_bpjs_ks', 100)->nullable();
            $table->string('x', 100)->nullable();
            $table->string('bpjs_kes', 100)->nullable();
            $table->string('x_ks', 100)->nullable();
            $table->string('nama_faskes', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('no_skck', 100)->nullable();
            $table->date('masa_berlaku_skck')->nullable();
            $table->string('jenis_lisensi', 100)->nullable();
            $table->string('no_lisensi', 100)->nullable();
            $table->string('masa_berlaku_lisensi', 100)->nullable();
            $table->string('no_rekening', 100)->nullable();
            $table->string('no_cif', 100)->nullable();
            $table->string('bank', 100)->nullable();
            $table->string('npwp', 100)->nullable();
            $table->string('ptkp', 100)->nullable();
            $table->string('shoe_size')->nullable();
            $table->string('uniform_size')->nullable();
            $table->string('gp')->nullable();
            $table->string('via')->nullable();
            $table->string('reg_digantikan')->nullable();
            $table->string('nama_digantikan')->nullable();
            
            // Gabungan dari employee_addresses - Alamat KTP
            $table->text('alamat_lengkap_ktp')->nullable();
            $table->string('desa_ktp')->nullable();
            $table->string('kecamatan_ktp')->nullable();
            $table->string('kota_ktp')->nullable();
            $table->string('kode_pos_ktp')->nullable();
            
            // Gabungan dari employee_addresses - Alamat Domisili
            $table->text('alamat_lengkap_domisili')->nullable();
            $table->string('desa_domisili')->nullable();
            $table->string('kecamatan_domisili')->nullable();
            $table->string('kota_domisili')->nullable();
            $table->string('kode_pos_domisili')->nullable();
            
            $table->timestamps();
        });

        Schema::create('employee_health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->date('tanggal_mcu')->nullable();
            $table->string('kesimpulan_hasil_mcu')->nullable();

            $table->integer('tinggi_badan')->nullable(); // cm
            $table->integer('berat_badan')->nullable(); // kg

            $table->string('gol_darah', 10)->nullable();
            $table->boolean('buta_warna')->nullable();

            $table->string('hasil_drug_test')->nullable();
            $table->date('tanggal_drug_test')->nullable();

            $table->text('riwayat_penyakit')->nullable();
            $table->string('darah')->nullable();
            $table->string('urine')->nullable();
            $table->string('f_hati')->nullable();
            $table->string('gula_darah')->nullable();
            $table->string('ginjal')->nullable();
            $table->string('thorax')->nullable();
            $table->string('tensi')->nullable();
            $table->string('nadi')->nullable();
            $table->string('od')->nullable();
            $table->string('os')->nullable();

            $table->timestamps();
        });

        Schema::create('employee_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('nama');
            $table->string('hubungan'); // Ayah, Ibu, Istri, Anak
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('tgl_perkawinan')->nullable();

            $table->timestamps();
        });

        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('jenjang')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('institusi')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->string('sekolah_asal')->nullable();

            $table->timestamps();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('pas_foto')->nullable();
            $table->string('dokumen_kk')->nullable();
            $table->string('dokumen_surat_pengalaman_kerja')->nullable();
            $table->string('dokumen_ktp')->nullable();
            $table->string('dokumen_bpjs_ketenagakerjaan')->nullable();
            $table->string('dokumen_sio_forklift')->nullable();
            $table->string('dokumen_formulir_bpjs_kesehatan')->nullable();
            $table->string('dokumen_ijazah_terakhir')->nullable();
            $table->string('dokumen_skck')->nullable();
            $table->string('dokumen_lisensi')->nullable();

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

            $table->string('tgl_awal_kerja')->nullable();
            $table->string('tgl_akhir_kerja')->nullable();

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

        Schema::create('import_payslip_logs', function (Blueprint $table) {
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

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('judul_periode')->nullable();
            $table->integer('period_year');
            $table->integer('period_month');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'closed', 'processed'])->default('open');
            $table->timestamps();
            
            // $table->unique(['period_year', 'period_month']);
            $table->index(['period_year', 'period_month']);
        });

        Schema::create('salary_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('gaji_hk', 15, 2)->nullable();
            $table->decimal('gaji_pokok', 15, 2)->nullable();
            $table->decimal('gaji_per_hari', 15, 2)->nullable();
            $table->decimal('gaji_train_hk', 15, 2)->nullable();
            $table->decimal('gaji_train_upah_per_jam', 15, 2)->nullable();
            $table->decimal('lembur_per_hari', 15, 2)->nullable();
            $table->decimal('lembur_per_jam', 15, 2)->nullable();
            $table->date('effective_date');
            $table->timestamps();
            
            $table->index(['employee_id', 'effective_date']);
        });

        Schema::create('attendance_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->integer('jam_kerja')->nullable();
            $table->decimal('jam_hk', 10, 2)->nullable();
            $table->decimal('jam_hl', 10, 2)->nullable();
            $table->decimal('jam_hr', 10, 2)->nullable();
            $table->integer('jml_hl')->nullable();
            $table->integer('jml_hr')->nullable();
            $table->integer('hadir')->nullable();
            $table->integer('mangkir_hari')->nullable();
            $table->integer('pot_tdk_masuk_hari')->nullable();
            $table->decimal('pot_tdk_masuk_upah', 15, 2)->nullable();
            $table->integer('terlambat_hari')->nullable();
            $table->integer('terlambat_menit')->nullable();
            $table->decimal('terlambat_jam', 10, 2)->nullable();
            $table->integer('ijin_pulang')->nullable();
            $table->integer('cuti_dibayar')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id']);
            $table->index('payroll_period_id');
        });

        Schema::create('overtime_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->decimal('overtime_jam', 10, 2)->nullable();
            $table->integer('lembur_hari')->nullable();
            $table->decimal('lembur_jam', 10, 2)->nullable();
            $table->decimal('lembur_jam_biasa', 10, 2)->nullable();
            $table->decimal('lembur_jam_khusus', 10, 2)->nullable();
            $table->decimal('lembur_minggu_2', 15, 2)->nullable();
            $table->decimal('lembur_minggu_3', 15, 2)->nullable();
            $table->decimal('lembur_minggu_4', 15, 2)->nullable();
            $table->decimal('lembur_minggu_5', 15, 2)->nullable();
            $table->decimal('lembur_minggu_6', 15, 2)->nullable();
            $table->decimal('lembur_minggu_7', 15, 2)->nullable();
            $table->decimal('lembur_libur', 15, 2)->nullable();
            $table->decimal('lembur_2', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id']);
            $table->index('payroll_period_id');
        });

        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->decimal('gaji_hk', 15, 2)->nullable();
            $table->decimal('gaji_hl', 15, 2)->nullable();
            $table->decimal('gaji_hr', 15, 2)->nullable();
            $table->decimal('gaji_jml', 15, 2)->nullable();
            $table->decimal('gaji_train_jml', 15, 2)->nullable();
            $table->decimal('gaji_rev', 15, 2)->nullable();
            $table->decimal('gaji_lbh_tgl23_bulan_lalu', 15, 2)->nullable();
            $table->decimal('lembur_jml', 15, 2)->nullable();
            $table->decimal('lembur_jml_hk', 15, 2)->nullable();
            $table->decimal('lembur_jml_hl', 15, 2)->nullable();
            $table->decimal('lembur_jml_hr', 15, 2)->nullable();
            $table->decimal('lembur_biasa_jml', 15, 2)->nullable();
            $table->decimal('lembur_khusus_jml', 15, 2)->nullable();
            $table->decimal('lembur_kurang_bulan_lalu', 15, 2)->nullable();
            $table->decimal('overtime', 15, 2)->nullable();
            $table->decimal('fee_lembur', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id']);
            $table->index('payroll_period_id');
        });

        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->decimal('tunj', 15, 2)->nullable();
            $table->decimal('tunj_sewa_motor', 15, 2)->nullable();
            $table->decimal('tunj_bbm', 15, 2)->nullable();
            $table->decimal('tunj_pulsa', 15, 2)->nullable();
            $table->decimal('tunj_penampilan', 15, 2)->nullable();
            $table->decimal('tunj_shift', 15, 2)->nullable();
            $table->decimal('tunj_makan', 15, 2)->nullable();
            $table->decimal('tunj_transport', 15, 2)->nullable();
            $table->decimal('tunj_kost', 15, 2)->nullable();
            $table->decimal('tunj_maintenance', 15, 2)->nullable();
            $table->decimal('tunj_posisi', 15, 2)->nullable();
            $table->decimal('tunj_fisik', 15, 2)->nullable();
            $table->decimal('tunj_loyalitas', 15, 2)->nullable();
            $table->decimal('tunj_operator', 15, 2)->nullable();
            $table->decimal('tunj_jabatan', 15, 2)->nullable();
            $table->decimal('tunj_bag', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id']);
            $table->index('payroll_period_id');
        });

        Schema::create('additional_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->decimal('anjem_jam', 10, 2)->nullable();
            $table->integer('anjem_hari')->nullable();
            $table->decimal('anjem_jml', 15, 2)->nullable();
            $table->decimal('borongan_kg', 10, 2)->nullable();
            $table->decimal('borongan_jml', 15, 2)->nullable();
            $table->decimal('retase', 15, 2)->nullable();
            $table->decimal('retase_bongkar', 15, 2)->nullable();
            $table->decimal('piket_l_biasa', 15, 2)->nullable();
            $table->decimal('piket_l_besar', 15, 2)->nullable();
            $table->decimal('piket_l_lain', 15, 2)->nullable();
            $table->decimal('piket_bbm', 15, 2)->nullable();
            $table->decimal('piket_reguler', 15, 2)->nullable();
            $table->decimal('piket_hari_raya', 15, 2)->nullable();
            $table->decimal('upah_hr_nasional', 15, 2)->nullable();
            $table->decimal('upah_hr_raya', 15, 2)->nullable();
            $table->decimal('lmbr_hr_nasional', 15, 2)->nullable();
            $table->decimal('bonus', 15, 2)->nullable();
            $table->decimal('premi', 15, 2)->nullable();
            $table->decimal('insentif', 15, 2)->nullable();
            $table->decimal('insentif_malam', 15, 2)->nullable();
            $table->decimal('perdin', 15, 2)->nullable();
            $table->decimal('pengiriman', 15, 2)->nullable();
            $table->decimal('uang_extra', 15, 2)->nullable();
            $table->decimal('accident', 15, 2)->nullable();
            $table->decimal('pelatihan_gaji', 15, 2)->nullable();
            $table->decimal('rapelan', 15, 2)->nullable();
            $table->decimal('kurang_bulan_lalu', 15, 2)->nullable();
            $table->decimal('koreksi_gaji_plus', 15, 2)->nullable();
            $table->decimal('koreksi_pph21', 15, 2)->nullable();
            $table->decimal('pengembalian_pph21', 15, 2)->nullable();
            $table->decimal('pembulatan', 15, 2)->nullable();
            $table->decimal('lain_lain', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id']);
            $table->index('payroll_period_id');
        });

        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->decimal('pot_makan', 15, 2)->nullable();
            $table->decimal('pot_bpjs_tk', 15, 2)->nullable();
            $table->decimal('pot_bpjs_kes', 15, 2)->nullable();
            $table->decimal('pot_bpjs', 15, 2)->nullable();
            $table->decimal('pot_koperasi', 15, 2)->nullable();
            $table->decimal('pot_bonus_gantung', 15, 2)->nullable();
            $table->decimal('pot_jam_kerja', 15, 2)->nullable();
            $table->decimal('pot_materai', 15, 2)->nullable();
            $table->decimal('pot_kerusakan', 15, 2)->nullable();
            $table->decimal('pot_admin', 15, 2)->nullable();
            $table->decimal('pot_apd', 15, 2)->nullable();
            $table->decimal('pot_alfa', 15, 2)->nullable();
            $table->decimal('pot_jamsos', 15, 2)->nullable();
            $table->decimal('pot_sptp', 15, 2)->nullable();
            $table->decimal('pot_payroll', 15, 2)->nullable();
            $table->decimal('pot_seragam', 15, 2)->nullable();
            $table->decimal('pot_tdk_masuk_jml', 15, 2)->nullable();
            $table->decimal('pot_tdk_finger', 15, 2)->nullable();
            $table->decimal('pot_pph21', 15, 2)->nullable();
            $table->decimal('pot_hari_mingu', 15, 2)->nullable();
            $table->decimal('pot_lain', 15, 2)->nullable();
            $table->decimal('klaim', 15, 2)->nullable();
            $table->decimal('denda', 15, 2)->nullable();
            $table->decimal('denda_telat_briefing', 15, 2)->nullable();
            $table->decimal('kas', 15, 2)->nullable();
            $table->decimal('kasbon', 15, 2)->nullable();
            $table->decimal('mangkir_jml', 15, 2)->nullable();
            $table->decimal('terlambat_jml', 15, 2)->nullable();
            $table->decimal('koreksi_gaji_minus', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id']);
            $table->index('payroll_period_id');
        });

        Schema::create('payroll_summary', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->decimal('grand_total', 15, 2);
            $table->text('exactsumef2ef10485761rincian_46ab761trainingn24')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id']);
            $table->index('payroll_period_id');
            $table->index('grand_total');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('import_logs');
        Schema::dropIfExists('employee_employment_histories');
        Schema::dropIfExists('employee_educations');
        Schema::dropIfExists('employee_family_members');
        Schema::dropIfExists('employee_health_records');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('employees');

        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('salary_configurations');
        Schema::dropIfExists('attendance_summary');
        Schema::dropIfExists('overtime_summary');
        Schema::dropIfExists('earnings');
        Schema::dropIfExists('allowances');
        Schema::dropIfExists('additional_earnings');
        Schema::dropIfExists('deductions');
        Schema::dropIfExists('payroll_summary');
    }

};