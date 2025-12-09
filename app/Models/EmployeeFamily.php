<?php
// app/Models/EmployeeFamily.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFamily extends Model
{
    protected $table = 'employee_family_members';

    protected $guarded = [];

    // protected $casts = [
    //     'tanggal_lahir' => 'date',
    // ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
