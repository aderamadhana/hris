<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateExpiredContracts extends Command
{
    protected $signature = 'employees:update-expired-contracts';
    
    protected $description = 'Update status karyawan yang kontraknya sudah expired menjadi tidak aktif';

    public function handle()
    {
        $this->info('Memulai proses update karyawan dengan kontrak expired...');

        try {
            DB::beginTransaction();

            $expiredEmployees = $this->getExpiredContractEmployees();
            $totalUpdated = 0;

            foreach ($expiredEmployees as $employee) {
                $updated = $employee->update(['status_active' => 0]);
                
                if ($updated) {
                    $totalUpdated++;
                    $this->line("✓ Employee ID {$employee->id} - {$employee->nama} dinonaktifkan");
                    
                    // Log untuk tracking
                    Log::info("Kontrak expired - Employee ID: {$employee->id}", [
                        'employee_id' => $employee->id,
                        'nama' => $employee->nama,
                        'tgl_akhir_kerja' => $employee->employments->first()?->tgl_akhir_kerja
                    ]);
                }
            }

            DB::commit();

            $this->info("\n✓ Proses selesai!");
            $this->info("Total karyawan yang diupdate: {$totalUpdated}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            Log::error('Error update expired contracts: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }

    private function getExpiredContractEmployees()
    {
        return Employee::query()
            ->where('status_active', 1) // Hanya yang masih aktif
            ->whereHas('employments', function ($query) {
                $query->whereNotNull('tgl_akhir_kerja')
                    ->where('tgl_akhir_kerja', '<', now()->startOfDay());
            })
            ->with(['employments' => function ($query) {
                $query->whereNotNull('tgl_akhir_kerja')
                    ->orderBy('tgl_akhir_kerja', 'desc');
            }])
            ->get();
    }
}