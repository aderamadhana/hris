<?php
// app/Models/EmployeeHealth.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeHealth extends Model
{
    protected $table = 'employee_health_records';
    protected $fillable = [
        'employee_id',
        'tinggi_badan',
        'berat_badan',
        'golongan_darah',
        'tensi',
        'nadi',
        'buta_warna',
        'riwayat_penyakit',
        'tgl_mcu',
        'hasil_drug_test',
    ];

    protected $casts = [
        'tgl_mcu' => 'date',
        'buta_warna' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
