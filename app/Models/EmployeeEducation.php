<?php
// app/Models/EmployeeEducation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $table = 'employee_educations';

    protected $fillable = [
        'employee_id',
        'pendidikan',
        'jurusan',
        'sekolah',
        'tahun_lulus',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
