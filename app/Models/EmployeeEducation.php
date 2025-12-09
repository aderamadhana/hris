<?php
// app/Models/EmployeeEducation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $table = 'employee_educations';

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
