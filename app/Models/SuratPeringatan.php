<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPeringatan extends Model
{
    protected $fillable = [
        'nomor_sp',
        'tanggal_sp',
        'employee_id',
        'no_ktp',
        'tingkat',
        'pelanggaran',
        'tanggal_kejadian',
        'file_path',
    ];

    protected $casts = [
        'tanggal_sp' => 'date',
        'tanggal_kejadian' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

}
