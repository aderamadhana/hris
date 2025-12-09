<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEmployment extends Model
{
    protected $table = 'employee_employment_histories';

    protected $fillable = [
        'employee_id',
        'perusahaan',
        'penempatan',
        'job_role',
        'cost_center',
        'no_kontrak',
        'jenis_kontrak',
        'status_karyawan',
        'tgl_daftar',
        'tgl_mulai_kerja',
        'tgl_selesai_kerja',
        'pola_kerja',
        'hari_kerja',
    ];

    protected $casts = [
        'tgl_daftar' => 'date',
        'tgl_mulai_kerja' => 'date',
        'tgl_selesai_kerja' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
