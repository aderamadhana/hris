<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAddress extends Model
{
    protected $fillable = [
        'employee_id',
        'alamat_lengkap',
        'jalan',
        'desa',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'kode_pos',
        'provinsi',
        'alamat_domisili',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
