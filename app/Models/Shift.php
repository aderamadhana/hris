<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';

    protected $fillable = [
        'nama_shift',
        'kode_shift',
        'jam_masuk',
        'jam_pulang',
        'toleransi_keterlambatan',
        'durasi_kerja',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        // 'jam_masuk' => 'datetime:H:i',
        // 'jam_pulang' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    // Helpers
    public function getJamMasukFormatAttribute()
    {
        return $this->jam_masuk->format('H:i');
    }

    public function getJamPulangFormatAttribute()
    {
        return $this->jam_pulang->format('H:i');
    }

    public function getDurasiKerjaFormatAttribute()
    {
        $jam = floor($this->durasi_kerja / 60);
        $menit = $this->durasi_kerja % 60;
        return "{$jam} jam {$menit} menit";
    }
}