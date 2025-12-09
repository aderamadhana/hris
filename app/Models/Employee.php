<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nrp',
        'user_id',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_active',
        'status_perkawinan',
        'kewarganegaraan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function personal()
    {
        return $this->hasOne(EmployeePersonal::class);
    }

    public function address()
    {
        return $this->hasOne(EmployeeAddress::class);
    }

    public function educations()
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function employments()
    {
        return $this->hasMany(EmployeeEmployment::class);
    }

    public function families()
    {
        return $this->hasMany(EmployeeFamily::class);
    }

    public function health()
    {
        return $this->hasOne(EmployeeHealth::class);
    }
}