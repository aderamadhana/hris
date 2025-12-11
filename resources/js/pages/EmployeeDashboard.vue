<template>
    <section class="dashboard">
        <!-- HEADER -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard Saya</h1>
                <p class="page-subtitle">
                    Ringkasan kehadiran dan aktivitas kerja Anda
                </p>
            </div>

            <button class="btn" @click="fiturBelumTersedia">
                ⬇️ Unduh Slip Kehadiran
            </button>
        </div>

        <!-- GRID -->
        <div class="grid">
            <!-- STATUS KEHADIRAN HARI INI -->
            <div class="card large">
                <div class="card-header">
                    <div>
                        <div class="card-title">Kehadiran Hari Ini</div>
                        <div class="meta">{{ today }} · {{ shift }}</div>
                    </div>
                    <span class="card-badge">Realtime</span>
                </div>

                <div class="value">{{ attendanceStatus }}</div>

                <div class="meta">
                    Clock In: {{ clockIn || '-' }} · Clock Out:
                    {{ clockOut || '-' }}
                </div>

                <div class="chart-placeholder">Timeline kehadiran hari ini</div>
            </div>

            <!-- ON-TIME -->
            <div class="card success">
                <div class="card-header">
                    <div class="card-title">Ketepatan Waktu</div>
                    <span class="card-badge">Bulan ini</span>
                </div>

                <div class="circle">
                    <div class="circle-inner">{{ onTimeRate }}%</div>
                </div>

                <div class="meta">
                    {{ onTimeCount }} hari datang tepat waktu
                </div>
            </div>

            <!-- TERLAMBAT -->
            <div class="card danger">
                <div class="card-header">
                    <div class="card-title">Keterlambatan</div>
                    <span class="card-badge">Bulan ini</span>
                </div>

                <div class="circle">
                    <div class="circle-inner">{{ lateCount }}x</div>
                </div>

                <div class="meta">Rata-rata terlambat {{ avgLate }} menit</div>
            </div>

            <!-- SISA CUTI -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Sisa Cuti</div>
                    <span class="card-badge">Tahun ini</span>
                </div>

                <div class="value">{{ leaveRemaining }} hari</div>
                <div class="meta">Digunakan {{ leaveUsed }} hari</div>
            </div>

            <!-- LEMBUR -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Lembur</div>
                    <span class="card-badge">Bulan ini</span>
                </div>

                <div class="value">{{ overtimeHours }} jam</div>
                <div class="meta">{{ overtimeDays }} hari lembur tercatat</div>
            </div>

            <!-- STATUS KONTRAK -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Status Kontrak</div>
                    <span class="card-badge">{{ contractType }}</span>
                </div>

                <div class="value">{{ contractEnd }}</div>
                <div class="meta">Berlaku hingga tanggal tersebut</div>
            </div>
        </div>
    </section>
</template>
<script>
import { triggerAlert } from '@/utils/alert';
export default {
    data() {
        return {
            today: 'Senin, 8 Desember 2025',
            shift: 'Shift Pagi',
            attendanceStatus: 'Hadir ✅',
            clockIn: '07:42',
            clockOut: null,

            onTimeRate: 82,
            onTimeCount: 18,
            lateCount: 5,
            avgLate: 12,

            leaveRemaining: 8,
            leaveUsed: 4,

            overtimeHours: 12,
            overtimeDays: 3,

            contractType: 'Kontrak',
            contractEnd: '31 Maret 2026',
        };
    },
    methods: {
        fiturBelumTersedia() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },
    },
};
</script>
