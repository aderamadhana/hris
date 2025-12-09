<template>
    <section class="dashboard">
        <!-- Header section -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard HRIS {{ user }}</h1>
                <p class="page-subtitle">
                    Ringkasan kehadiran dan status karyawan hari ini
                </p>
            </div>

            <button class="btn">⬇️ Download laporan</button>
        </div>

        <!-- GRID UTAMA -->
        <div class="grid">
            <!-- KEHADIRAN HARI INI -->
            <div class="card large">
                <div class="card-header">
                    <div>
                        <div class="card-title">Kehadiran Hari Ini</div>
                        <div class="meta">
                            Senin, 8 Desember 2025 · Shift Pagi
                        </div>
                    </div>
                    <span class="card-badge">Realtime</span>
                </div>

                <div class="value">19 / 24</div>
                <div class="meta">19 hadir · 3 cuti · 2 izin · 0 alpha</div>

                <div class="chart-placeholder">
                    Grafik check-in (placeholder)
                </div>
            </div>

            <!-- ON-TIME CHECK-IN -->
            <div class="card success">
                <div class="card-header">
                    <div class="card-title">On-Time Check-in</div>
                    <span class="card-badge">Target ≥ 80%</span>
                </div>

                <div class="circle">
                    <div class="circle-inner">82%</div>
                </div>

                <div class="meta">18 karyawan check-in sebelum jam 09.00</div>
            </div>

            <!-- TERLAMBAT -->
            <div class="card danger">
                <div class="card-header">
                    <div class="card-title">Terlambat</div>
                    <span class="card-badge">Hari ini</span>
                </div>

                <div class="circle">
                    <div class="circle-inner">5 org</div>
                </div>

                <div class="meta">Rata-rata keterlambatan 12 menit</div>
            </div>

            <!-- TOTAL KARYAWAN -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Total Karyawan</div>
                    <span class="card-badge">Aktif</span>
                </div>
                <div class="value">24</div>
                <div class="meta">Termasuk karyawan tetap & kontrak</div>
            </div>

            <!-- PENGAJUAN CUTI -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Pengajuan Cuti</div>
                    <span class="card-badge">Menunggu</span>
                </div>
                <div class="value">3</div>
                <div class="meta">Menunggu approval atasan/HR hari ini</div>
            </div>

            <!-- LEMBUR BULAN INI -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Lembur Bulan Ini</div>
                    <span class="card-badge">Disetujui</span>
                </div>
                <div class="value">42 jam</div>
                <div class="meta">Dari 9 karyawan · Perlu review payroll</div>
            </div>

            <!-- KONTRAK HAMPIR HABIS -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Kontrak Hampir Habis</div>
                    <span class="card-badge">&lt; 30 hari</span>
                </div>
                <div class="value">4</div>
                <div class="meta">
                    Perlu follow-up perpanjangan atau offboarding
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    props: {
        user: {
            type: Object,
            required: true,
        },
    },
};
</script>
