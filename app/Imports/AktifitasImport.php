<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Aktifitas;
use App\Models\LogAktifitas;
use App\Models\ImportLogAktifitas;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AktifitasImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    ShouldQueue,
    WithEvents
{
    private int $logId;

    // kalau mau tetap ada timeout internal
    private int $startTime;
    private int $maxExecutionTime = 540; // detik (lebih kecil dari timeout job)

    public int $timeout = 600; // Job timeout
    public int $tries = 1;     // Jangan retry otomatis

    public function __construct(int $logId)
    {
        $this->logId = $logId;
        $this->startTime = time();
    }

    public function collection(Collection $rows)
    {
        // chunk bisa kosong
        if ($rows->isEmpty()) {
            return;
        }

        $chunkProcessed = 0;
        $chunkSuccess   = 0;
        $chunkFailed    = 0;

        Log::info('Import chunk received', [
            'log_id' => $this->logId,
            'rows'   => $rows->count(),
        ]);

        foreach ($rows as $index => $row) {
            // Timeout internal (opsional)
            if ($this->isTimeout()) {
                $this->markTimeout("Import timeout after processing some rows in chunk");
                break;
            }

            try {
                $this->processRow($row, $index);
                $chunkSuccess++;
            } catch (\Throwable $e) {
                $chunkFailed++;
                $this->logError($row, $e, $index);
            }

            $chunkProcessed++;
        }

        // PENTING: akumulasi ke DB (atomic), bukan overwrite
        ImportLogAktifitas::where('id', $this->logId)->update([
            'total'   => DB::raw("total + {$chunkProcessed}"),
            'success' => DB::raw("success + {$chunkSuccess}"),
            'failed'  => DB::raw("failed + {$chunkFailed}"),
        ]);

        Log::info('Import chunk done', [
            'log_id'   => $this->logId,
            'total+'   => $chunkProcessed,
            'success+' => $chunkSuccess,
            'failed+'  => $chunkFailed,
        ]);
    }

    private function processRow($row, int $index): void
    {
        // Validasi minimal
        if (empty($row['nik'])) {
            throw new \Exception("NIK tidak boleh kosong pada baris " . ($index + 2));
        }

        if (empty($row['tanggal'])) {
            throw new \Exception("Tanggal tidak boleh kosong pada baris " . ($index + 2));
        }

        // Sanitize & parsing
        $reg      = !empty($row['reg']) ? trim((string)$row['reg']) : null;
        $nik      = trim((string)$row['nik']);
        $nama     = !empty($row['nama']) ? trim((string)$row['nama']) : null;
        $tanggal  = $this->parseDate($row['tanggal']);
        $shift    = !empty($row['shift']) ? trim((string)$row['shift']) : null;
        $bag      = !empty($row['bag']) ? trim((string)$row['bag']) : null;
        $lo       = !empty($row['lo']) ? trim((string)$row['lo']) : null;

        $jamMasuk  = $this->parseTime($row['jam_masuk'] ?? null);
        $jamPulang = $this->parseTime($row['jam_pulang'] ?? null);
        $jamKerja  = isset($row['jam_kerja']) && $row['jam_kerja'] !== '' ? (int)$row['jam_kerja'] : null;

        $kodeKerja = !empty($row['kode_kerja']) ? trim((string)$row['kode_kerja']) : null;

        $hasilKerja   = isset($row['hasil_kerja']) && $row['hasil_kerja'] !== '' ? (int)$row['hasil_kerja'] : null;
        $hasilLembur  = isset($row['hasil_lembur']) && $row['hasil_lembur'] !== '' ? (int)$row['hasil_lembur'] : null;
        $returnQty    = isset($row['return']) && $row['return'] !== '' ? (int)$row['return'] : null;

        $tolakQc   = isset($row['tolak_qc']) && $row['tolak_qc'] !== '' ? (int)$row['tolak_qc'] : null;
        $upahScf   = isset($row['upah_scf']) && $row['upah_scf'] !== '' ? (int)$row['upah_scf'] : null;
        $bantuScf  = isset($row['bantu_scf']) && $row['bantu_scf'] !== '' ? (int)$row['bantu_scf'] : null;
        $dendaScf  = isset($row['denda_scf']) && $row['denda_scf'] !== '' ? (int)$row['denda_scf'] : null;
        $totalScf  = isset($row['total_scf']) && $row['total_scf'] !== '' ? (int)$row['total_scf'] : null;

        $upahAct    = isset($row['upah_act']) && $row['upah_act'] !== '' ? (int)$row['upah_act'] : null;
        $upahBantu  = isset($row['upah_bantu']) && $row['upah_bantu'] !== '' ? (int)$row['upah_bantu'] : null;
        $returnAct  = isset($row['return_act']) && $row['return_act'] !== '' ? (int)$row['return_act'] : null;
        $dendaAct   = isset($row['denda_act']) && $row['denda_act'] !== '' ? (int)$row['denda_act'] : null;
        $totalAct   = isset($row['total_act']) && $row['total_act'] !== '' ? (int)$row['total_act'] : null;

        $keterangan = !empty($row['keterangan']) ? trim((string)$row['keterangan']) : null;

        try {
            DB::beginTransaction();

            // Cari employee berdasarkan NIK (no_ktp)
            $employee = Employee::where('no_ktp', $nik)->first();
            if (!$employee) {
                throw new \Exception("Karyawan dengan NIK {$nik} tidak ditemukan");
            }
            // Wajib ada kode kerja
            if ($kodeKerja === null || $kodeKerja === '') {
                throw new \Exception("Kode kerja kosong pada baris " . ($index + 2));
            }

            // Kode kerja harus ada di tabel aktifitas (kode = kode_kerja)
            $aktifitas = Aktifitas::where('kode', $kodeKerja)->first();
            if (!$aktifitas) {
                throw new \Exception("Kode kerja {$kodeKerja} tidak ditemukan di master aktifitas pada baris " . ($index + 2));
            }
            $aktifitasId = $aktifitas->id;
            $employeeId = $employee?->id;

            // Cari aktifitas berdasarkan kode_kerja
            $aktifitasId = null;
            if ($kodeKerja) {
                $aktifitas = Aktifitas::where('kode', $kodeKerja)->first();
                $aktifitasId = $aktifitas?->id;
            }

            $logData = [
                'employee_id'        => $employeeId,
                'aktifitas_id'       => $aktifitasId,
                'no_urut'            => null,
                'reg'                => $reg,
                'nama'               => $nama,
                'tgl'                => $tanggal,
                'shift'              => $employee->shift_id,
                'bag'                => $bag,
                'lo'                 => $lo,
                'jam_masuk'          => $jamMasuk,
                'jam_pulang'         => $jamPulang,
                'jam_kerja_menit'    => $jamKerja,
                'kode_kerja'         => $kodeKerja,
                'hasil_kerja'        => $hasilKerja,
                'hasil_lembur'       => $hasilLembur,
                'return_qty'         => $returnQty,
                'tolak_qc'           => $tolakQc,
                'upah_scf'           => $upahScf,
                'bantu_scf'          => $bantuScf,
                'denda_scf'          => $dendaScf,
                'total_scf'          => $totalScf,
                'upah_act'           => $upahAct,
                'upah_bantu_act'     => $upahBantu,
                'return_act'         => $returnAct,
                'denda_act'          => $dendaAct,
                'total_act'          => $totalAct,
                'ket'                => $keterangan,
            ];

            // Update atau insert
            LogAktifitas::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'tgl'         => $tanggal,
                    'shift'       => $shift,
                    'kode_kerja'  => $kodeKerja,
                ],
                $logData
            );

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();

            if ((int)$e->getCode() === 23000) {
                $errorInfo = $e->errorInfo[2] ?? $e->getMessage();
                throw new \Exception("Duplikasi data untuk NIK: {$nik}, tanggal: {$tanggal}, shift: {$shift} pada baris " . ($index + 2) . ". Detail: " . $errorInfo);
            }

            throw new \Exception("Database error pada baris " . ($index + 2) . ": " . $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }

            return Carbon::parse((string)$value)->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new \Exception("Format tanggal tidak valid: {$value}");
        }
    }

    private function parseTime($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('H:i:s');
            }

            return Carbon::parse((string)$value)->format('H:i:s');
        } catch (\Throwable $e) {
            Log::warning('Failed to parse time', [
                'value' => $value,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function logError($row, \Throwable $e, int $index): void
    {
        $rowNo   = $index + 2; // catatan: ini per chunk (kalau mau presisi global, perlu event BeforeChunk)
        $nik     = $row['nik'] ?? null;
        $reg     = $row['reg'] ?? null;
        $nama    = $row['nama'] ?? null;
        $tanggal = $row['tanggal'] ?? null;
        $errMsg  = $e->getMessage();

        $pdo = DB::connection()->getPdo();
        $q = function ($v) use ($pdo) {
            if ($v === null) return 'NULL';
            return $pdo->quote((string)$v);
        };

        ImportLogAktifitas::where('id', $this->logId)->update([
            'errors' => DB::raw(
                "JSON_ARRAY_APPEND(
                    COALESCE(errors, JSON_ARRAY()),
                    '$',
                    JSON_OBJECT(
                        'row', {$rowNo},
                        'nik', {$q($nik)},
                        'reg', {$q($reg)},
                        'nama', {$q($nama)},
                        'tanggal', {$q($tanggal)},
                        'error', {$q($errMsg)}
                    )
                )"
            ),
        ]);

        Log::error('Import row error', [
            'log_id' => $this->logId,
            'row'    => $rowNo,
            'error'  => $errMsg,
        ]);
    }

    private function markTimeout(string $message): void
    {
        $pdo = DB::connection()->getPdo();
        $msg = $pdo->quote($message);

        ImportLogAktifitas::where('id', $this->logId)->update([
            'status' => 'timeout',
            'errors' => DB::raw(
                "JSON_ARRAY_APPEND(
                    COALESCE(errors, JSON_ARRAY()),
                    '$',
                    JSON_OBJECT(
                        'type', 'timeout',
                        'message', {$msg}
                    )
                )"
            ),
        ]);

        Log::warning('Import timeout', [
            'log_id'  => $this->logId,
            'message' => $message,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                ImportLogAktifitas::where('id', $this->logId)->update([
                    'status' => 'processing',
                ]);

                Log::info('Import event: BeforeImport', [
                    'log_id' => $this->logId,
                ]);
            },

            AfterImport::class => function (AfterImport $event) {
                // PENTING: jangan overwrite total/success/failed di sini
                $log = ImportLogAktifitas::find($this->logId);

                if ($log && !in_array($log->status, ['timeout', 'failed'], true)) {
                    $log->update([
                        'status' => 'completed',
                    ]);
                }

                Log::info('Import event: AfterImport', [
                    'log_id' => $this->logId,
                    'status' => $log->status ?? 'unknown',
                ]);
            },
        ];
    }

    public function chunkSize(): int
    {
        return 50;
    }

    private function isTimeout(): bool
    {
        $elapsed = time() - $this->startTime;
        return $elapsed >= $this->maxExecutionTime;
    }

    // Dipanggil kalau job benar-benar gagal
    public function failed(\Throwable $exception): void
    {
        $pdo = DB::connection()->getPdo();
        $err = $pdo->quote($exception->getMessage());

        ImportLogAktifitas::where('id', $this->logId)->update([
            'status' => 'failed',
            'errors' => DB::raw(
                "JSON_ARRAY_APPEND(
                    COALESCE(errors, JSON_ARRAY()),
                    '$',
                    JSON_OBJECT(
                        'type', 'job_failed',
                        'error', {$err}
                    )
                )"
            ),
        ]);

        Log::error('Import job failed', [
            'log_id' => $this->logId,
            'error'  => $exception->getMessage(),
        ]);
    }
}
