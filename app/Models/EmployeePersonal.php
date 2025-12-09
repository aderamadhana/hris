<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePersonal extends Model
{
    protected $fillable = [
        'employee_id',
        'no_ktp',
        'no_kk',
        'email',
        'phone',
        'npwp',
        'bpjs_ketenagakerjaan',
        'bpjs_kesehatan',
        'ptkp',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
