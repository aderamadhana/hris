<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\EmployeePersonal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    { 
        $now = Carbon::now();
        User::factory()->create([
            'name' => 'Admin HRIS',
            'email' => 'admin@admin.com',
            'role_id' => 1,
            'password' => Hash::make('123456'),
        ]);

        User::factory()->create([
            'name' => 'Employee HRIS',
            'email' => 'employee@admin.com',
            'role_id' => 2,
            'password' => Hash::make('123456'),
        ]); 
        
        Role::factory()->create([
            'role_name' => 'admin',
        ]);
        
        Role::factory()->create([
            'role_name' => 'employee',
        ]);

        Employee::insert([
            [
                'nrp' => 'admin',
                'user_id' => 1,
                'nama' => 'Admin',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1993-04-12',
                'agama' => 'Islam',
                'status_perkawinan' => 'Menikah',
                'kewarganegaraan' => 'Indonesia',
                'status_active' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nrp' => 'employee',
                'user_id' => 2,
                'nama' => 'Employee',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Blitar',
                'tanggal_lahir' => '1990-09-25',
                'agama' => 'Islam',
                'status_perkawinan' => 'Menikah',
                'kewarganegaraan' => 'Indonesia',
                'status_active' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        EmployeePersonal::insert([
            [
                'employee_id' => 1,
                'no_ktp' => 'admin',
                'no_kk' => '3507120101010001',
                'npwp' => '12.345.678.9-012.000',
                'agama' => 'Islam',
                'status_perkawinan' => 'Menikah',
                'kewarganegaraan' => 'Indonesia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'employee_id' => 2,
                'no_ktp' => 'employee',
                'no_kk' => '3507050202020002',
                'npwp' => '98.765.432.1-987.000',
                'agama' => 'Islam',
                'status_perkawinan' => 'Menikah',
                'kewarganegaraan' => 'Indonesia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        /** ADDRESS */
        DB::table('employee_addresses')->insert([
            'employee_id'     => 2,
            'alamat_lengkap'  => 'Jl. Mawar No. 12, RT 02 RW 03',
            'desa'            => 'Sawojajar',
            'kecamatan'       => 'Kedungkandang',
            'kota'            => 'Kota Malang',
            'kode_pos'        => '65139',
            'tipe'            => 'Domisili',
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        /** EMPLOYMENT HISTORY */
        DB::table('employee_employment_histories')->insert([
            'employee_id'        => 2,
            'perusahaan'         => 'PT Maju Jaya Sejahtera',
            'jabatan'            => 'Staff Administrasi',
            'penempatan'         => 'Operational',
            'tgl_awal_kerja'     => '2021-03-01',
            'tgl_akhir_kerja'    => null,
            'jenis_kontrak'      => 'PKWTT',
            'status'             => 'Aktif',
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);

        /** HEALTH RECORD */
        DB::table('employee_health_records')->insert([
            'employee_id'        => 2,
            'tanggal_mcu'        => '2024-10-15',
            'tinggi_badan'       => 170,
            'berat_badan'        => 68,
            'gol_darah'          => 'O',
            'buta_warna'         => false,
            'hasil_drug_test'    => 'Negatif',
            'tanggal_drug_test'  => '2024-10-15',
            'riwayat_penyakit'   => 'Tidak ada',
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);

        /** FAMILY MEMBERS */
        DB::table('employee_family_members')->insert([
            [
                'employee_id' => 2,
                'nama'        => 'Siti Aminah',
                'hubungan'    => 'Istri',
                'tanggal_lahir'=> '1992-06-10',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'employee_id' => 2,
                'nama'        => 'Ahmad Rizki',
                'hubungan'    => 'Anak',
                'tanggal_lahir'=> '2018-11-04',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        /** EDUCATION */
        DB::table('employee_educations')->insert([
            'employee_id'   => 2,
            'jenjang'       => 'S1',
            'jurusan'       => 'Manajemen',
            'institusi'     => 'Universitas Negeri Malang',
            'tahun_lulus'   => 2015,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
    }
}
