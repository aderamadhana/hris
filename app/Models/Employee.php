<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function currentEmployment()
    {
        return $this->hasOne(EmployeeEmployment::class, 'employee_id', 'id')
            ->orderByDesc('id')  // ✅ Gunakan orderByDesc
            ->limit(1);           // ✅ Pastikan hanya ambil 1
    }
    public function suratPeringatanTerakhir()
    {
        return $this->hasOne(SuratPeringatan::class, 'employee_id')
            ->ofMany(
                ['tanggal_sp' => 'max', 'id' => 'max'],
                function ($q) {
                    $q->whereNotNull('tanggal_sp');
                }
            );
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

    public function latestSalaryConfiguration(): HasOne
    {
        return $this->hasOne(SalaryConfiguration::class)->latestOfMany('effective_date');
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
        return $query->where('status_active', 1)->whereNotIn('id',[1,2]);
    }
    
    // Relasi ke employment history yang aktif
    public function activeEmployment()
    {
        return $this->hasOne(EmployeeEmployment::class)
            ->where('status', 'aktif')
            ->latest();
    }

    // Scope untuk karyawan tidak aktif
    public function scopeInactive($query)
    {
        return $query->where('status_active', '0');
    }

    public function getTanggalLahirAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }

    protected static function booted()
    {
        static::deleting(function ($employee) {

            // BASIC PROFILE
            $employee->educations()->delete();
            $employee->families()->delete();

            // EMPLOYMENT
            $employee->employments()->delete();
            $employee->employmentss()->delete();
            $employee->currentEmployment()->delete();
            $employee->activeEmployment()->delete();

            // DOCUMENTS & HEALTH
            $employee->documents()->delete();
            $employee->health()->delete();

            // PAYROLL (KRITIS)
            $employee->salaryConfigurations()->delete();
            $employee->attendanceSummaries()->delete();
            $employee->overtimeSummaries()->delete();
            $employee->earnings()->delete();
            $employee->allowances()->delete();
            $employee->additionalEarnings()->delete();
            $employee->deductions()->delete();
            $employee->payrollSummaries()->delete();

            // USER (OPTIONAL — PUTUSKAN DENGAN SADAR)
            // $employee->user()->delete();
        });
    }
}