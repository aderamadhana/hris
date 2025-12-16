<template>
    <section class="dashboard">
        <!-- HEADER -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard Saya</h1>
            </div>
        </div>

        <!-- GRID -->
        <div class="dashboard-container">
            <!-- KATEGORI: KEHADIRAN -->
            <div class="category-section">
                <h2 class="category-title">📅 Kehadiran</h2>
                <div class="dashboard-grid">
                    <!-- STATUS KEHADIRAN HARI INI -->
                    <div class="dashboard-card large">
                        <div class="dashboard-card-header">
                            <div>
                                <div class="dashboard-card-title">
                                    Kehadiran Hari Ini
                                </div>
                                <div class="dashboard-meta">
                                    16 Desember 2025 · Shift Pagi
                                </div>
                            </div>
                            <span class="dashboard-card-badge">Realtime</span>
                        </div>

                        <div class="dashboard-value">Hadir</div>

                        <div class="dashboard-meta">
                            Clock In: 08:15 · Clock Out: -
                        </div>

                        <div class="chart-placeholder">
                            Timeline kehadiran hari ini
                        </div>
                    </div>

                    <!-- ON-TIME -->
                    <div class="dashboard-card success">
                        <div class="dashboard-card-header">
                            <div class="dashboard-card-title">
                                Ketepatan Waktu
                            </div>
                            <span class="dashboard-card-badge">Bulan ini</span>
                        </div>

                        <div class="dashboard-circle">
                            <div class="dashboard-circle-inner">92%</div>
                        </div>

                        <div class="dashboard-meta">
                            18 hari datang tepat waktu
                        </div>
                    </div>

                    <!-- TERLAMBAT -->
                    <div class="dashboard-card danger">
                        <div class="dashboard-card-header">
                            <div class="dashboard-card-title">
                                Keterlambatan
                            </div>
                            <span class="dashboard-card-badge">Bulan ini</span>
                        </div>

                        <div class="dashboard-circle">
                            <div class="dashboard-circle-inner">3x</div>
                        </div>

                        <div class="dashboard-meta">
                            Rata-rata terlambat 12 menit
                        </div>
                    </div>
                </div>
            </div>

            <!-- KATEGORI: KONTRAK -->
            <div class="category-section">
                <h2 class="category-title">📋 Kontrak</h2>
                <div class="dashboard-grid">
                    <!-- STATUS KONTRAK -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <div class="dashboard-card-title">
                                Status Kontrak
                            </div>
                            <span class="dashboard-card-badge">PKWT</span>
                        </div>

                        <div class="dashboard-value">31 Mar 2026</div>
                        <div class="dashboard-meta">
                            Berlaku hingga tanggal tersebut
                        </div>
                    </div>

                    <!-- HISTORY KONTRAK KERJA -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <div class="dashboard-card-title">
                                History Kontrak
                            </div>
                            <span class="dashboard-card-badge">Riwayat</span>
                        </div>

                        <div class="dashboard-value">4</div>
                        <div class="dashboard-meta">Total kontrak tercatat</div>

                        <button class="dashboard-btn-link">
                            Lihat Detail History →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

// Props dari controller
const props = defineProps({
    today: {
        type: String,
        default: 'Jumat, 13 Desember 2024',
    },
    shift: {
        type: String,
        default: 'Shift Pagi (08:00 - 17:00)',
    },
    attendanceStatus: {
        type: String,
        default: 'Hadir',
    },
    clockIn: {
        type: String,
        default: '07:58',
    },
    clockOut: {
        type: String,
        default: '17:15',
    },
    onTimeRate: {
        type: Number,
        default: 95,
    },
    onTimeCount: {
        type: Number,
        default: 19,
    },
    lateCount: {
        type: Number,
        default: 3,
    },
    avgLate: {
        type: Number,
        default: 12,
    },
    leaveRemaining: {
        type: Number,
        default: 8,
    },
    leaveUsed: {
        type: Number,
        default: 4,
    },
    overtimeHours: {
        type: Number,
        default: 16,
    },
    overtimeDays: {
        type: Number,
        default: 4,
    },
    contractType: {
        type: String,
        default: 'PKWT',
    },
    contractEnd: {
        type: String,
        default: '31 Maret 2025',
    },
    contractHistory: {
        type: Array,
        default: () => [
            {
                id: 1,
                type: 'PKWT',
                startDate: '01 April 2024',
                endDate: '31 Maret 2025',
                status: 'Aktif',
                duration: '12 bulan',
            },
            {
                id: 2,
                type: 'PKWT',
                startDate: '01 April 2023',
                endDate: '31 Maret 2024',
                status: 'Selesai',
                duration: '12 bulan',
            },
            {
                id: 3,
                type: 'Probation',
                startDate: '01 Januari 2023',
                endDate: '31 Maret 2023',
                status: 'Selesai',
                duration: '3 bulan',
            },
        ],
    },
});

// Functions
const fiturBelumTersedia = () => {
    alert('Fitur ini sedang dalam pengembangan');
};

const showContractHistory = () => {
    // Redirect ke halaman history atau buka modal
    router.visit(route('contract.history'));
    // Atau bisa pakai modal jika prefer
};
</script>

<style>
/* .dashboard {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
} */

/* PAGE HEADER */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.page-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0.25rem 0 0 0;
}

.btn {
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: white;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

/* DASHBOARD GRID */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
}

.dashboard-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.dashboard-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.dashboard-card.large {
    grid-column: span 2;
}

/* Card variants */
.dashboard-card.danger {
    border-left: 4px solid #ef4444;
    background: linear-gradient(to right, #fef2f2 0%, white 100%);
}

.dashboard-card.warning {
    border-left: 4px solid #f59e0b;
    background: linear-gradient(to right, #fffbeb 0%, white 100%);
}

.dashboard-card.success {
    border-left: 4px solid #10b981;
    background: linear-gradient(to right, #f0fdf4 0%, white 100%);
}

/* Card header */
.dashboard-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    gap: 0.75rem;
}

.dashboard-card-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    letter-spacing: -0.01em;
}

/* Card badge */
.dashboard-card-badge {
    padding: 0.25rem 0.625rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 9999px;
    background: #f3f4f6;
    color: #6b7280;
    white-space: nowrap;
}

.dashboard-card.danger .dashboard-card-badge {
    background: #fee2e2;
    color: #dc2626;
}

.dashboard-card.success .dashboard-card-badge {
    background: #d1fae5;
    color: #059669;
}

/* Value */
.dashboard-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.dashboard-card.danger .dashboard-value {
    color: #dc2626;
}

.dashboard-card.warning .dashboard-value {
    color: #d97706;
}

.dashboard-card.success .dashboard-value {
    color: #059669;
}

/* Meta */
.dashboard-meta {
    font-size: 0.813rem;
    color: #6b7280;
    line-height: 1.4;
}

/* Chart Placeholder */
.chart-placeholder {
    margin-top: 1rem;
    padding: 2rem;
    background: #f9fafb;
    border-radius: 8px;
    text-align: center;
    color: #9ca3af;
    font-size: 0.875rem;
    border: 2px dashed #e5e7eb;
}

/* Button Link */
.dashboard-btn-link {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    font-size: 0.813rem;
    font-weight: 600;
    color: #3b82f6;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
    text-align: left;
}

.dashboard-btn-link:hover {
    color: #2563eb;
    text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dashboard {
        padding: 1rem;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .dashboard-card {
        padding: 1.25rem;
    }

    .dashboard-card.large {
        grid-column: span 1;
    }

    .dashboard-value {
        font-size: 2rem;
    }
}

@media (min-width: 1400px) {
    .dashboard-grid {
        grid-template-columns: repeat(4, 1fr);
    }

    .dashboard-card.large {
        grid-column: span 2;
    }
}
</style>
