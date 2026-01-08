<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function educations()
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function employments()
    {
        return $this->hasOne(EmployeeEmployment::class);
    }

    public function employmentss()
    {
        return $this->hasMany(EmployeeEmployment::class);
    }

    public function families()
    {
        return $this->hasMany(EmployeeFamily::class);
    }

    public function documents()
    {
        return $this->hasOne(EmployeeDocument::class);
    }


    public function health()
    {
        return $this->hasOne(EmployeeHealth::class);
    }

    // PAYROLL
    public function salaryConfigurations(): HasMany
    {
        return $this->hasMany(SalaryConfiguration::class);
    }

    public function attendanceSummaries(): HasMany
    {
        return $this->hasMany(AttendanceSummary::class);
    }

    public function overtimeSummaries(): HasMany
    {
        return $this->hasMany(OvertimeSummary::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    public function allowances(): HasMany
    {
        return $this->hasMany(Allowance::class);
    }

    public function additionalEarnings(): HasMany
    {
        return $this->hasMany(AdditionalEarning::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(Deduction::class);
    }

    public function payrollSummaries(): HasMany
    {
        return $this->hasMany(PayrollSummary::class);
    }

    // Scopes
    public function scopeByBagian($query, $bagian)
    {
        return $query->where('bagian', $bagian);
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('status_kary');
    }

    public function getTanggalLahirAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }
}