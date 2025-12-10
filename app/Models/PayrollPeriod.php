<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
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
    public function scopeByYear($query, $year)
    {
        return $query->where('period_year', $year);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->where('period_month', $month);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    // Accessor
    public function getPeriodNameAttribute()
    {
        return date('F Y', mktime(0, 0, 0, $this->period_month, 1, $this->period_year));
    }
}