<?php

namespace App\Jobs;

use App\Imports\EmployeesImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ImportEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle(): void
    {
         if (!file_exists($this->path)) {
            throw new Exception("Import file missing: {$this->path}");
        }
        Excel::import(new EmployeesImport, $this->path, null, \Maatwebsite\Excel\Excel::XLSX);
    }
}
