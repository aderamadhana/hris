<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEmployment extends Model
{
    protected $table = 'employee_employment_histories';

    protected $guarded = [];

    protected $casts = [
        'tgl_daftar' => 'date',
        'tgl_mulai_kerja' => 'date',
        'tgl_selesai_kerja' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTglAwalKerjaAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function getTglAkhirKerjaAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }
}
