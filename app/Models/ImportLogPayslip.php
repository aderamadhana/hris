<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLogPayslip extends Model
{
    
    protected $table = 'import_payslip_logs';

    protected $fillable = [
        'file',
        'total',
        'success',
        'failed',
        'status',
        'errors',
    ];
}
