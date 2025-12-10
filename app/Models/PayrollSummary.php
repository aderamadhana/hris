<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollSummary extends Model
{
    use HasFactory;

    protected $table = 'payroll_summary';
    protected $guarded = [];

    protected $casts = [
        'grand_total' => 'decimal:2',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function attendanceSummary(): BelongsTo
    {
        return $this->belongsTo(AttendanceSummary::class, 'employee_id', 'employee_id')
                    ->where('payroll_period_id', $this->payroll_period_id);
    }

    public function overtimeSummary(): BelongsTo
    {
        return $this->belongsTo(OvertimeSummary::class, 'employee_id', 'employee_id')
                    ->where('payroll_period_id', $this->payroll_period_id);
    }

    public function earning(): BelongsTo
    {
        return $this->belongsTo(Earning::class, 'employee_id', 'employee_id')
                    ->where('payroll_period_id', $this->payroll_period_id);
    }

    public function allowance(): BelongsTo
    {
        return $this->belongsTo(Allowance::class, 'employee_id', 'employee_id')
                    ->where('payroll_period_id', $this->payroll_period_id);
    }

    public function additionalEarning(): BelongsTo
    {
        return $this->belongsTo(AdditionalEarning::class, 'employee_id', 'employee_id')
                    ->where('payroll_period_id', $this->payroll_period_id);
    }

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(Deduction::class, 'employee_id', 'employee_id')
                    ->where('payroll_period_id', $this->payroll_period_id);
    }

    // Scopes
    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('payroll_period_id', $periodId);
    }

    public function scopeOrderByGrandTotal($query, $direction = 'desc')
    {
        return $query->orderBy('grand_total', $direction);
    }
}