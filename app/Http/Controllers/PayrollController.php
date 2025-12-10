<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\PayslipImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    /**
     * Import payroll data
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'payroll_period_id' => 'required|exists:payroll_periods,id'
        ]);

        try {
            // Generate unique import ID
            $importId = uniqid('import_', true);
            
            // Create import instance
            $import = new PayslipImport($request->payroll_period_id, $importId);
            
            // Start import (akan di-queue otomatis karena implements ShouldQueue)
            Excel::queueImport($import, $request->file('file'));
            
            return response()->json([
                'status' => 'processing',
                'message' => 'Import has been queued for processing',
                'import_id' => $importId,
                'check_progress_url' => route('payroll.import.progress', ['importId' => $importId])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check import progress
     */
    public function checkProgress($importId)
    {
        $progress = PayslipImport::getImportProgress($importId);
        
        // Tentukan status berdasarkan progress
        $status = 'processing';
        
        if ($progress['total'] > 0) {
            if ($progress['failed'] > 0 && $progress['success'] === 0) {
                $status = 'failed';
            } elseif ($progress['failed'] > 0) {
                $status = 'completed_with_errors';
            } else {
                $status = 'completed';
            }
        }
        
        return response()->json([
            'status' => $status,
            'data' => $progress
        ]);
    }

    /**
     * Import payroll synchronously (tanpa queue)
     * Gunakan ini untuk testing atau file kecil
     */
    public function importSync(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'payroll_period_id' => 'required|exists:payroll_periods,id'
        ]);

        try {
            // Generate unique import ID
            $importId = uniqid('import_', true);
            
            // Create import instance
            $import = new PayslipImport($request->payroll_period_id, $importId);
            
            // Import langsung (synchronous)
            Excel::import($import, $request->file('file'));
            
            // Get hasil import
            $results = $import->getResults();
            
            if ($results['failed'] > 0) {
                return response()->json([
                    'status' => 'warning',
                    'message' => "Import completed with errors",
                    'data' => $results
                ], 207);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => "Successfully imported {$results['success']} records",
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear import cache
     */
    public function clearImportCache($importId)
    {
        Cache::forget("import_{$importId}_success");
        Cache::forget("import_{$importId}_failed");
        Cache::forget("import_{$importId}_errors");
        
        return response()->json([
            'status' => 'success',
            'message' => 'Import cache cleared'
        ]);
    }
}