<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktifitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktifitas';

    protected $fillable = [
        'employee_id',
        'aktifitas_id',

        'no_urut',
        'reg',
        'nama',
        'tgl',
        'shift',
        'bag',
        'lo',

        'jam_masuk',
        'jam_pulang',
        'jam_kerja_menit',

        'kode_kerja',
        'hasil_kerja',
        'hasil_lembur',
        'return_qty',
        'tolak_qc',

        'upah_scf',
        'bantu_scf',
        'denda_scf',
        'total_scf',

        'upah_act',
        'upah_bantu_act',
        'return_act',
        'denda_act',
        'total_act',

        'ket',
    ];

    protected $casts = [
        'tgl' => 'date',
        'jam_masuk' => 'datetime:H:i:s',
        'jam_pulang' => 'datetime:H:i:s',
    ];

    /**
     * Relasi: log aktifitas milik 1 employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Relasi: log aktifitas milik 1 aktifitas
     */
    public function aktifitas()
    {
        return $this->belongsTo(Aktifitas::class, 'aktifitas_id');
    }
}
