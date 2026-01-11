<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    public function run()
    {
        $shifts = [
            [
                'nama_shift' => 'Shift Fleksibel',
                'kode_shift' => 'FLEX',
                'jam_masuk' => '09:00:00',
                'jam_pulang' => '18:00:00',
                'toleransi_keterlambatan' => 30,
                'durasi_kerja' => 540, // 9 jam
                'is_active' => true,
                'keterangan' => 'Shift fleksibel dengan toleransi keterlambatan lebih lama.',
            ],
            [
                'nama_shift' => 'Regular (Default)',
                'kode_shift' => 'REG',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'toleransi_keterlambatan' => 15,
                'durasi_kerja' => 540,
                'is_active' => true,
                'keterangan' => 'Shift default untuk karyawan regular. Jam kerja 08:00 - 17:00 dengan istirahat 1 jam.',
            ],
            [
                'nama_shift' => 'Shift Pagi',
                'kode_shift' => 'SP',
                'jam_masuk' => '07:00:00',
                'jam_pulang' => '15:00:00',
                'toleransi_keterlambatan' => 15,
                'durasi_kerja' => 480, // 8 jam
                'is_active' => true,
                'keterangan' => 'Shift pagi untuk operasional pagi hari.',
            ],
            [
                'nama_shift' => 'Shift Siang',
                'kode_shift' => 'SS',
                'jam_masuk' => '14:00:00',
                'jam_pulang' => '22:00:00',
                'toleransi_keterlambatan' => 15,
                'durasi_kerja' => 480, // 8 jam
                'is_active' => true,
                'keterangan' => 'Shift siang untuk operasional sore hingga malam.',
            ],
            [
                'nama_shift' => 'Shift Malam',
                'kode_shift' => 'SM',
                'jam_masuk' => '22:00:00',
                'jam_pulang' => '06:00:00',
                'toleransi_keterlambatan' => 15,
                'durasi_kerja' => 480, // 8 jam
                'is_active' => true,
                'keterangan' => 'Shift malam untuk operasional 24 jam.',
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }

        // ✅ Set shift default (ID = 1) untuk semua employee yang belum punya shift
        DB::table('employees')
            ->whereNull('shift_id')
            ->update(['shift_id' => 1]);

        $this->command->info('✅ Shift seeder berhasil! Shift default (ID: 1) telah di-assign ke semua karyawan.');
    }
}