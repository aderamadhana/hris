<?php
// app/Models/EmployeeHealth.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeHealth extends Model
{
    protected $table = 'employee_health_records';
    protected $guarded = [];

    protected $casts = [
        'tgl_mcu' => 'date',
        'buta_warna' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
