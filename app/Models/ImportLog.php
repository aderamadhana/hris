<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'file',
        'total',
        'success',
        'failed',
        'status',
        'errors',
    ];
}
