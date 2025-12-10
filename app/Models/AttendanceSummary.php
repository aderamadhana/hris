<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSummary extends Model
{
    use HasFactory;

    protected $table = 'attendance_summary';
    protected $guarded = [];

    protected $casts = [
        'jam_hk' => 'decimal:2',
        'jam_hl' => 'decimal:2',
        'jam_hr' => 'decimal:2',
        'terlambat_jam' => 'decimal:2',
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
}