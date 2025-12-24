<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    { 
        $now = Carbon::now();

        $roles = [
            ['id' => 1, 'role_name' => 'admin'],
            ['id' => 2, 'role_name' => 'employee'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['id' => $roleData['id']], 
                $roleData
            );
        }

        // Create users
        $users = [
            [
                'name' => 'Admin HRIS',
                'email' => 'admin@admin.com',
                'no_ktp' => 'admin',
                'employee_id' => '1',
                'email' => 'admin@admin.com',
                'role_id' => 1,
                'password' => Hash::make(env('ADMIN_PASSWORD', '123456')),
            ],
            [
                'name' => 'Employee HRIS',
                'email' => 'employee@admin.com',
                'no_ktp' => 'employee',
                'employee_id' => '2',
                'role_id' => 2,
                'password' => Hash::make(env('EMPLOYEE_PASSWORD', '123456')),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'email_verified_at' => now(),
                    'remember_token' => null,
                    'two_factor_secret' => null,
                    'two_factor_recovery_codes' => null,
                    'two_factor_confirmed_at' => null,
                ])
            );
        }
        
        // Display created data
        $this->command->table(
            ['Role ID', 'Role Name'],
            Role::all(['id', 'role_name'])->toArray()
        );

        $this->command->table(
            ['ID', 'Name', 'Email', 'Role ID'],
            User::all(['id', 'name', 'email', 'role_id'])->toArray()
        );

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

        $periods = [
            [
                'period_year'  => 2026,
                'period_month' => 1,
                'start_date'   => Carbon::create(2026, 1, 1),
                'end_date'     => Carbon::create(2026, 1, 31),
                'status'       => 'closed',
                'judul_periode'       => 'Periode Januari 2026',
            ],
            [
                'period_year'  => 2026,
                'period_month' => 2,
                'start_date'   => Carbon::create(2026, 2, 1),
                'end_date'     => Carbon::create(2026, 2, 29),
                'status'       => 'closed',
                'judul_periode'       => 'Periode Ferbuari 2026',
            ],
            [
                'period_year'  => 2026,
                'period_month' => 3,
                'start_date'   => Carbon::create(2026, 3, 1),
                'end_date'     => Carbon::create(2026, 3, 31),
                'status'       => 'open',
                'judul_periode'       => 'Periode Maret 2026',
            ],
        ];

        foreach ($periods as $period) {
            PayrollPeriod::updateOrCreate(
                [
                    'period_year'  => $period['period_year'],
                    'period_month' => $period['period_month'],
                ],
                [
                    'judul_periode' => $period['judul_periode'],
                    'start_date' => $period['start_date'],
                    'end_date'   => $period['end_date'],
                    'status'     => $period['status'],
                ]
            );
        }
    }
}
