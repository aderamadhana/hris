<template>
    <div v-if="loading" class="fullpage-loader">
        <div class="fullpage-loader__card">
            <div class="fullpage-loader__spinner"></div>
            <div class="fullpage-loader__title">Loading data dashboard</div>
            <div class="fullpage-loader__subtitle">Mohon tunggu sebentar</div>
        </div>
    </div>
    <!-- HEADER -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">Dashboard Kehadiran</h1>
            <p class="dashboard-subtitle">Rekap kehadiran hari ini</p>
        </div>
        <button
            @click="refreshData"
            class="btn-refresh-header"
            :disabled="loading"
        >
            <font-awesome-icon
                :icon="['fas', 'rotate']"
                :class="{ 'fa-spin': loading }"
            />
        </button>
    </div>
    <div class="card dashboard-page">
        <!-- CONTENT -->
        <div class="dashboard-content">
            <!-- Status Banner -->
            <div class="date-now">
                <p class="dashboard-subtitle">{{ todayDate }}</p>
                <div v-if="shiftInfo" class="shift-paneldsa">
                    <div class="shift-panel__head">
                        <font-awesome-icon :icon="['fa', 'briefcase']" />
                        <div class="shift-panel__headText">
                            <div class="shift-panel__title">
                                {{ shiftInfo.nama_shift }}
                            </div>
                            <div class="shift-panel__meta">
                                {{ shiftInfo.kode_shift }} •
                                {{
                                    shiftInfo.is_flexible ? 'Flexible' : 'Fixed'
                                }}
                            </div>
                        </div>
                    </div>

                    <div class="shift-panel__grid">
                        <div class="kv">
                            <div class="k">Jam Masuk</div>
                            <div class="v">{{ shiftInfo.jam_masuk }}</div>
                        </div>

                        <div class="kv">
                            <div class="k">Jam Pulang</div>
                            <div class="v">{{ shiftInfo.jam_pulang }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid: 2 columns on desktop -->
            <div class="main-grid">
                <!-- LEFT COLUMN -->
                <div class="left-column">
                    <!-- Combined Attendance Card -->
                    <div class="attendance-card">
                        <div class="card-header">
                            <h3 class="card-title">Presensi Hari Ini</h3>
                            <div class="realtime-badge">
                                <span class="pulse-dot"></span>
                                Live
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Clock Times Section -->
                            <div class="clock-times-section">
                                <div class="clock-times">
                                    <div
                                        class="clock-item clock-in-item"
                                        @click="goToAbsen()"
                                    >
                                        <div class="clock-icon clock-in">
                                            <font-awesome-icon
                                                :icon="[
                                                    'fas',
                                                    'arrow-right-to-bracket',
                                                ]"
                                            />
                                        </div>
                                        <div class="clock-details">
                                            <span class="clock-label"
                                                >Clock In</span
                                            >
                                            <span class="clock-value">{{
                                                clockInTime
                                            }}</span>
                                        </div>
                                    </div>

                                    <div class="clock-divider"></div>

                                    <div
                                        class="clock-item clock-out-item"
                                        @click="goToAbsen()"
                                    >
                                        <div class="clock-icon clock-out">
                                            <font-awesome-icon
                                                :icon="[
                                                    'fas',
                                                    'arrow-right-from-bracket',
                                                ]"
                                            />
                                        </div>
                                        <div class="clock-details">
                                            <span class="clock-label"
                                                >Clock Out</span
                                            >
                                            <span class="clock-value">{{
                                                clockOutTime
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="section-divider">
                                <span>Detail Presensi</span>
                            </div>

                            <!-- Timeline Section -->
                            <div class="timeline-section">
                                <div
                                    v-if="todayAttendance?.detail?.length"
                                    class="timeline"
                                >
                                    <div
                                        v-for="item in todayAttendance.detail"
                                        :key="item.id"
                                        class="timeline-item"
                                    >
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <div class="timeline-time">
                                                    <font-awesome-icon
                                                        :icon="['fas', 'clock']"
                                                    />
                                                    {{ item.waktu_formatted }}
                                                </div>
                                                <span
                                                    class="timeline-badge"
                                                    :class="
                                                        item.is_valid_location
                                                            ? 'valid'
                                                            : 'invalid'
                                                    "
                                                >
                                                    <font-awesome-icon
                                                        :icon="[
                                                            'fas',
                                                            item.is_valid_location
                                                                ? 'check-circle'
                                                                : 'exclamation-circle',
                                                        ]"
                                                    />
                                                    {{
                                                        item.is_valid_location
                                                            ? 'Valid'
                                                            : 'Invalid'
                                                    }}
                                                </span>
                                            </div>

                                            <div class="timeline-type">
                                                {{
                                                    item.jenis_presensi ===
                                                    'masuk'
                                                        ? 'Clock In'
                                                        : 'Clock Out'
                                                }}
                                            </div>

                                            <div
                                                v-if="item.foto_presensi"
                                                class="timeline-photo"
                                            >
                                                <img
                                                    :src="item.foto_presensi"
                                                    alt="Foto Presensi"
                                                />
                                            </div>

                                            <div
                                                v-if="
                                                    item.jarak_dari_lokasi ||
                                                    item.device_info
                                                "
                                                class="timeline-meta"
                                            >
                                                <span
                                                    v-if="
                                                        item.jarak_dari_lokasi
                                                    "
                                                >
                                                    <font-awesome-icon
                                                        :icon="[
                                                            'fas',
                                                            'location-dot',
                                                        ]"
                                                    />
                                                    {{
                                                        item.jarak_dari_lokasi
                                                    }}m
                                                </span>
                                                <span v-if="item.device_info">
                                                    <font-awesome-icon
                                                        :icon="[
                                                            'fas',
                                                            'mobile-screen',
                                                        ]"
                                                    />
                                                    {{ item.device_info }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="empty-state">
                                    <div class="empty-icon">
                                        <font-awesome-icon
                                            :icon="['fas', 'calendar-xmark']"
                                        />
                                    </div>
                                    <p class="empty-text">
                                        Belum ada presensi hari ini
                                    </p>
                                    <p class="empty-subtext">
                                        Silakan lakukan clock in untuk memulai
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="right-column">
                    <!-- Stats Cards -->
                    <div class="stats-section">
                        <h3 class="section-subtitle">Statistik Bulan Ini</h3>

                        <div class="stat-card card-success">
                            <div class="stat-icon-badge success">
                                <font-awesome-icon
                                    :icon="['fa', 'calendar-check']"
                                />
                            </div>
                            <div class="stat-body">
                                <div class="stat-value">
                                    {{ onTimePercentage }}%
                                </div>
                                <div class="stat-label">Ketepatan Waktu</div>
                                <div class="stat-sublabel">
                                    {{ onTimeCount }} dari
                                    {{ totalWorkingDays }} hari
                                </div>
                            </div>
                        </div>

                        <div class="stat-card card-warning">
                            <div class="stat-icon-badge warning">
                                <font-awesome-icon
                                    :icon="['fas', 'clock-rotate-left']"
                                />
                            </div>
                            <div class="stat-body">
                                <div class="stat-value">{{ lateCount }}x</div>
                                <div class="stat-label">Terlambat</div>
                                <div class="stat-sublabel">Bulan ini</div>
                            </div>
                        </div>

                        <div class="stat-card card-info">
                            <div class="stat-icon-badge info">
                                <font-awesome-icon
                                    :icon="['fas', 'calendar-check']"
                                />
                            </div>
                            <div class="stat-body">
                                <div class="stat-value">
                                    {{ onTimeCount + lateCount }}
                                </div>
                                <div class="stat-label">Total Hadir</div>
                                <div class="stat-sublabel">Bulan ini</div>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Section -->
                    <div class="contract-section">
                        <h3 class="section-subtitle">Informasi Kontrak</h3>

                        <div class="contract-card">
                            <div class="contract-icon">
                                <font-awesome-icon
                                    :icon="['fas', 'briefcase']"
                                />
                            </div>
                            <div class="contract-content">
                                <div class="contract-label">Status Kontrak</div>
                                <div class="contract-value">
                                    {{ contractType }}
                                </div>
                                <div class="contract-detail">
                                    <font-awesome-icon
                                        :icon="['fas', 'calendar']"
                                    />
                                    Berlaku hingga {{ contractEndDateLabel }}
                                </div>
                            </div>
                        </div>

                        <div class="contract-card clickable">
                            <div class="contract-icon">
                                <font-awesome-icon
                                    :icon="['fas', 'clock-rotate-left']"
                                />
                            </div>
                            <div
                                class="contract-content history-trigger"
                                role="button"
                                tabindex="0"
                                @click="openHistoryModal"
                                @keydown.enter.prevent="openHistoryModal"
                                @keydown.space.prevent="openHistoryModal"
                            >
                                <div class="contract-label">
                                    Riwayat Kontrak
                                </div>

                                <div class="contract-value">
                                    {{ contractHistoryTotal }}
                                </div>

                                <div class="contract-detail">
                                    <font-awesome-icon
                                        :icon="['fa', 'arrow-right']"
                                    />
                                    Lihat detail riwayat
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Modal v-if="showHistoryModal" @click.self="closeHistoryModal">
            <div class="history-modal">
                <div class="history-modal__header">
                    <div class="history-modal__title">Riwayat Kontrak</div>
                    <button
                        class="history-modal__close"
                        type="button"
                        @click="closeHistoryModal"
                    >
                        ×
                    </button>
                </div>

                <div class="history-modal__body">
                    <div v-if="historyLoading" class="loading-state">
                        <div class="spinner"></div>
                        <p>Memuat data riwayat...</p>
                    </div>

                    <div v-else-if="!histories.length" class="history-empty">
                        Tidak ada riwayat kontrak.
                    </div>

                    <div v-else class="history-list">
                        <div
                            v-for="h in histories"
                            :key="h.id"
                            class="history-item"
                        >
                            <div class="history-top">
                                <div class="history-main">
                                    <div class="history-title">
                                        {{ h.jenis_kontrak || 'Kontrak' }} •
                                        {{ h.status || '-' }}
                                    </div>
                                    <div class="history-sub">
                                        {{ h.perusahaan || '-' }}
                                        <span v-if="h.penempatan"
                                            >• {{ h.penempatan }}</span
                                        >
                                        <span v-if="h.jabatan"
                                            >• {{ h.jabatan }}</span
                                        >
                                    </div>
                                </div>

                                <div class="history-dates">
                                    <span>{{
                                        h.tgl_awal_kerja_label || '-'
                                    }}</span>
                                    <span>→</span>
                                    <span>{{
                                        h.tgl_akhir_kerja_label || '-'
                                    }}</span>
                                </div>
                            </div>

                            <div class="history-meta">
                                <span v-if="h.no_kontrak"
                                    ><b>No Kontrak:</b> {{ h.no_kontrak }}</span
                                >
                                <span v-if="h.cost_center"
                                    ><b>CC:</b> {{ h.cost_center }}</span
                                >
                                <span v-if="h.masa_kerja"
                                    ><b>Masa:</b> {{ h.masa_kerja }}</span
                                >
                                <span v-if="h.pola_kerja"
                                    ><b>Pola:</b> {{ h.pola_kerja }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script>
import Modal from '@/components/Modal.vue';
import { triggerAlert } from '@/utils/alert';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    name: 'DashboardPresensiKontrak',
    components: {
        Modal,
    },

    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,
            loading: true,
            todayAttendance: null,
            monthlyStats: null,
            contractSummary: null,
            _fetchSeq: 0,

            showHistoryModal: false,
            historyLoading: false,
            histories: [],
        };
    },
    watch: {
        showHistoryModal(v) {
            // lock scroll biar modal enak
            document.body.style.overflow = v ? 'hidden' : '';
        },
    },
    computed: {
        todayDate() {
            return (
                this.todayAttendance?.tanggal_formatted ||
                new Date().toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                })
            );
        },

        attendanceDetail() {
            return Array.isArray(this.todayAttendance?.detail)
                ? this.todayAttendance.detail
                : [];
        },

        hasClockIn() {
            return this.attendanceDetail.some(
                (i) => i.jenis_presensi === 'masuk',
            );
        },

        hasClockOut() {
            return this.attendanceDetail.some(
                (i) => i.jenis_presensi === 'pulang',
            );
        },

        attendanceStatus() {
            if (this.hasClockOut) return 'selesai';
            if (this.hasClockIn) return 'hadir';
            return 'belum';
        },

        statusBadge() {
            const status = this.attendanceStatus;
            const rekap = this.todayAttendance?.rekap;

            const badges = {
                hadir: {
                    label: 'Sedang Bekerja',
                    class: 'banner-success',
                    icon: 'briefcase',
                    time:
                        this.clockInTime !== '-'
                            ? `Masuk pukul ${this.clockInTime}`
                            : null,
                },
                selesai: {
                    label: 'Selesai Bekerja',
                    class: 'banner-info',
                    icon: 'circle-check',
                    time: rekap?.total_jam_kerja
                        ? `Total ${rekap.total_jam_kerja}`
                        : null,
                },
                belum: {
                    label: 'Belum Presensi',
                    class: 'banner-warning',
                    icon: 'clock',
                    time: 'Silakan lakukan clock in',
                },
            };
            return badges[status] || badges.belum;
        },

        clockInTime() {
            return this._pickTime('masuk', 'min');
        },

        clockOutTime() {
            return this._pickTime('pulang', 'max');
        },

        shiftInfo() {
            return this.todayAttendance?.shift || null;
        },

        lateCount() {
            return this.monthlyStats?.kehadiran?.terlambat ?? 0;
        },

        onTimeCount() {
            return this.monthlyStats?.kehadiran?.hadir ?? 0;
        },

        totalWorkingDays() {
            return this.monthlyStats?.kehadiran?.total_hari_kerja ?? 0;
        },

        onTimePercentage() {
            const total = this.totalWorkingDays;
            if (!total) return 0;
            return Math.round((this.onTimeCount / total) * 100);
        },

        contractType() {
            return this.contractSummary?.contract_type || '-';
        },

        contractEndDateLabel() {
            return this.contractSummary?.contract_end_date_label || '-';
        },

        contractHistoryTotal() {
            const v =
                this.contractSummary?.total_kontrak ??
                this.contractSummary?.history_total ??
                this.contractSummary?.history?.total ??
                this.contractSummary?.riwayat?.total ??
                this.contractSummary?.total ??
                0;
            return typeof v === 'number' ? v : Number(v) || 0;
        },

        contractHistoryUrl() {
            return (
                this.contractSummary?.history_url ||
                this.contractSummary?.riwayat_url ||
                '/kontrak/riwayat'
            );
        },
    },

    mounted() {
        this.fetchDashboardData();
        window.addEventListener('keydown', this.onKeydown);
    },

    beforeUnmount() {
        window.removeEventListener('keydown', this.onKeydown);
        document.body.style.overflow = '';
    },

    methods: {
        onKeydown(e) {
            if (!this.showHistoryModal) return;
            if (e.key === 'Escape') this.closeHistoryModal();
        },

        // supaya template lama @click="goToHistory" tetap jalan
        goToHistory() {
            this.openHistoryModal();
        },

        async openHistoryModal() {
            this.showHistoryModal = true;
            this.historyLoading = true;
            const employeeId = this.user?.employee_id;

            try {
                // Endpoint riwayat (harus ada): /kontrak/history?employee_id=...
                const { data } = await axios.get('/presensi/kontrak-summary', {
                    params: { employee_id: employeeId },
                });

                this.histories = data.histories ?? [];
            } catch (err) {
                this.histories = [];
            } finally {
                this.historyLoading = false;
            }
        },

        closeHistoryModal() {
            this.showHistoryModal = false;
        },
        _localISODate(d = new Date()) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        },

        _toMinutes(hhmm) {
            if (!hhmm || typeof hhmm !== 'string') return null;
            const [h, m] = hhmm.split(':').map((x) => Number(x));
            if (!Number.isFinite(h) || !Number.isFinite(m)) return null;
            return h * 60 + m;
        },

        _pickTime(jenis, mode) {
            const items = this.attendanceDetail.filter(
                (i) => i.jenis_presensi === jenis,
            );
            if (!items.length) return '-';

            let best = items[0];
            let bestMin = this._toMinutes(best.waktu_formatted);

            for (const it of items) {
                const curMin = this._toMinutes(it.waktu_formatted);
                if (curMin == null || bestMin == null) continue;

                if (mode === 'min' && curMin < bestMin) {
                    best = it;
                    bestMin = curMin;
                }
                if (mode === 'max' && curMin > bestMin) {
                    best = it;
                    bestMin = curMin;
                }
            }

            return best?.waktu_formatted || '-';
        },

        _normalizeMonthlyStats(rootPayload) {
            const data = rootPayload?.data;
            if (!data) return null;
            return data.summary ?? data;
        },

        async fetchDashboardData() {
            this.loading = true;
            const seq = ++this._fetchSeq;

            try {
                const employeeId = this.user?.employee_id;
                if (!employeeId) throw new Error('employee_id tidak ditemukan');

                const today = this._localISODate(new Date());
                const now = new Date();
                const currentMonth = now.getMonth() + 1;
                const currentYear = now.getFullYear();

                const CONTRACT_ENDPOINT = '/presensi/kontrak-summary';

                const results = await Promise.allSettled([
                    axios.get('/presensi/log', {
                        params: { employee_id: employeeId, tanggal: today },
                    }),
                    axios.get('/presensi/log', {
                        params: {
                            employee_id: employeeId,
                            bulan: currentMonth,
                            tahun: currentYear,
                        },
                    }),
                    axios.get(CONTRACT_ENDPOINT, {
                        params: { employee_id: employeeId },
                    }),
                ]);

                if (seq !== this._fetchSeq) return;

                const [attendanceRes, statsRes, contractRes] = results;

                if (
                    attendanceRes.status === 'fulfilled' &&
                    attendanceRes.value.data?.success
                ) {
                    this.todayAttendance =
                        attendanceRes.value.data.data ?? null;
                } else {
                    this.todayAttendance = null;
                }

                if (
                    statsRes.status === 'fulfilled' &&
                    statsRes.value.data?.success
                ) {
                    this.monthlyStats =
                        this._normalizeMonthlyStats(statsRes.value.data) ??
                        null;
                } else {
                    this.monthlyStats = null;
                }

                if (contractRes.status === 'fulfilled') {
                    const payload = contractRes.value.data;
                    if (payload?.success)
                        this.contractSummary = payload ?? null;
                    else this.contractSummary = payload ?? null;
                } else {
                    this.contractSummary = null;
                }

                console.log(this.contractSummary);
            } catch (error) {
                console.error('Error fetching dashboard:', error);
                triggerAlert('error', 'Gagal memuat data dashboard');
            } finally {
                if (seq === this._fetchSeq) this.loading = false;
            }
        },

        refreshData() {
            this.fetchDashboardData();
        },

        goToAbsen() {
            router.visit(`/attendance`);
        },
    },
};
</script>

<style scoped>
/* ==================== BASE ==================== */
* {
    box-sizing: border-box;
}

/* ==================== PAGE WRAPPER ==================== */
.dashboard-page {
    width: 100%;
    padding: 1.5rem;
    min-height: 100vh;
    background: #f8fafc;
}

@media (max-width: 768px) {
    .dashboard-page {
        padding: 1rem;
    }
}

/* ==================== HEADER ==================== */
.dashboard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.header-content {
    flex: 1;
    min-width: 0; /* penting supaya text/shift panel bisa shrink tanpa overflow */
}

.dashboard-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
}

.dashboard-subtitle {
    font-size: 1rem;
    color: #64748b;
    margin: 0;
}

/* wrapper tanggal + shift panel (kamu taruh di sini) */
.date-now {
    padding-bottom: 15px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
}

.date-now .dashboard-subtitle {
    margin: 0;
    padding-top: 6px;
    line-height: 1.2;
    opacity: 0.85;
    font-size: 30px;
}

/* SHIFT PANEL di header */
.shift-paneldsa {
    margin-left: auto;
    min-width: 280px;
    max-width: 380px;
    width: clamp(280px, 32vw, 380px);

    padding: 12px 14px;
    border-radius: 14px;

    background: rgba(255, 255, 255, 0.75);
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}

.shift-paneldsa .shift-panel__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.shift-paneldsa .shift-panel__headText {
    min-width: 0;
    line-height: 1.2;
}

.shift-paneldsa .shift-panel__title {
    font-size: 14px;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.shift-paneldsa .shift-panel__meta {
    margin-top: 2px;
    font-size: 12px;
    opacity: 0.75;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.shift-paneldsa .shift-panel__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 14px;
}

.shift-paneldsa .kv .k {
    font-size: 11px;
    opacity: 0.7;
}

.shift-paneldsa .kv .v {
    margin-top: 2px;
    font-size: 13px;
    font-weight: 700;
}

/* header responsive */
@media (max-width: 768px) {
    .date-now {
        flex-direction: column;
        align-items: stretch;
    }

    .shift-paneldsa {
        margin-left: 0;
        width: 100%;
        max-width: none;
    }
    .date-now .dashboard-subtitle {
        font-size: 25px;
    }
}

@media (max-width: 420px) {
    .shift-paneldsa .shift-panel__grid {
        grid-template-columns: 1fr;
    }
    .date-now .dashboard-subtitle {
        font-size: 20px;
    }
}

.btn-refresh-header {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.btn-refresh-header:hover:not(:disabled) {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-refresh-header:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ==================== LOADING ==================== */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem;
    gap: 1rem;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e2e8f0;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.fa-spin {
    animation: spin 1s linear infinite;
}

/* ==================== STATUS BANNER ==================== */
.status-banner {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-left: 5px solid;
    margin-bottom: 2rem;
}

.banner-success {
    border-left-color: #10b981;
    background: linear-gradient(135deg, #ecfdf5 0%, white 100%);
}

.banner-info {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, white 100%);
}

.banner-warning {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, white 100%);
}

.status-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.banner-success .status-icon {
    background: #d1fae5;
    color: #059669;
}

.banner-info .status-icon {
    background: #dbeafe;
    color: #2563eb;
}

.banner-warning .status-icon {
    background: #fef3c7;
    color: #d97706;
}

.status-info {
    flex: 1;
    min-width: 0;
}

.status-label {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.status-time {
    font-size: 0.9rem;
    color: #64748b;
}

.shift-info {
    padding: 0.625rem 1.125rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .status-banner {
        flex-wrap: wrap;
    }

    .shift-info {
        width: 100%;
    }
}

/* ==================== MAIN GRID ==================== */
.main-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 1024px) {
    .main-grid {
        grid-template-columns: 1.5fr 1fr;
    }
}

.left-column,
.right-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ==================== CARD COMMON ==================== */
.clock-card,
.timeline-card,
.stat-card,
.contract-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.realtime-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.875rem;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #16a34a;
}

.pulse-dot {
    width: 6px;
    height: 6px;
    background: #16a34a;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.card-body {
    padding: 1.5rem;
}

/* ==================== CLOCK TIMES ==================== */
.clock-times {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 640px) {
    .clock-times {
        grid-template-columns: 1fr;
    }
}

.clock-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.clock-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.15);
    border-color: transparent;
}

.clock-item:hover::before {
    opacity: 1;
}

.clock-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.clock-in {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.clock-out {
    background: linear-gradient(135deg, #ec4899, #db2777);
    color: white;
}

.clock-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.clock-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.clock-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
}

/* ==================== TIMELINE ==================== */
.timeline {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    position: relative;
}

.timeline-item {
    position: relative;
    padding-left: 2rem;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    background: #3b82f6;
    border: 3px solid white;
    border-radius: 50%;
    box-shadow: 0 0 0 2px #3b82f6;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 1.5rem;
    bottom: -1.25rem;
    width: 2px;
    background: #e2e8f0;
}

.timeline-content {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.timeline-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
}

.timeline-badge {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.timeline-badge.valid {
    background: #d1fae5;
    color: #065f46;
}

.timeline-badge.invalid {
    background: #fee2e2;
    color: #991b1b;
}

.timeline-type {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.75rem;
}

.timeline-photo {
    margin: 0.75rem 0;
    border-radius: 10px;
    overflow: hidden;
    border: 2px solid #e2e8f0;
}

.timeline-photo img {
    width: 100%;
    height: auto;
    max-height: 280px;
    object-fit: cover;
    display: block;
}

.timeline-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e2e8f0;
}

.timeline-meta span {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #94a3b8;
}

.empty-state svg {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    opacity: 0.4;
}

.empty-state p {
    margin: 0;
    font-size: 1rem;
}

/* ==================== RIGHT COLUMN ==================== */
.section-subtitle {
    font-size: 1rem;
    font-weight: 600;
    color: #475569;
    margin: 0 0 1rem 0;
}

.stats-section,
.contract-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* ==================== STAT CARDS ==================== */
.stat-card {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    border-left: 4px solid;
    transition: all 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.card-success {
    border-left-color: #10b981;
}

.card-warning {
    border-left-color: #f59e0b;
}

.card-info {
    border-left-color: #3b82f6;
}

.stat-icon-badge {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.stat-icon-badge.success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #059669;
}

.stat-icon-badge.warning {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #d97706;
}

.stat-icon-badge.info {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #2563eb;
}

.stat-body {
    flex: 1;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.375rem;
}

.stat-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.25rem;
}

.stat-sublabel {
    font-size: 0.8rem;
    color: #94a3b8;
}

/* ==================== CONTRACT CARDS ==================== */
.contract-card {
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
    border: 2px solid #e2e8f0;
    transition: all 0.2s;
}

.contract-card.clickable {
    cursor: pointer;
}

.contract-card.clickable:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.15);
}

.contract-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f0f9ff, #dbeafe);
    border: 2px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.contract-content {
    flex: 1;
}

.contract-label {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.contract-value {
    font-size: 1.375rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.contract-detail {
    font-size: 0.875rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.history-trigger {
    cursor: pointer;
}
.history-trigger:focus {
    outline: 2px solid rgba(59, 130, 246, 0.35);
    outline-offset: 6px;
    border-radius: 10px;
}

/* konten di dalam modal */
.history-modal {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.history-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;

    padding: 16px 18px;
    background: #fff;
    border-bottom: 1px solid #eef2f7;
}

.history-modal__title {
    font-weight: 800;
    color: #0f172a;
    font-size: 15px;
}

.history-modal__close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: center;
}

.history-modal__close:hover {
    background: #f8fafc;
}

.history-modal__body {
    padding: 16px 18px;
    overflow: auto; /* scroll di body */
    background: #fff;
}

/* list */
.history-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.history-item {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 14px;
    background: #f8fafc;
}

.history-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    flex-wrap: wrap;
}

.history-title {
    font-weight: 800;
    color: #0f172a;
    font-size: 13px;
}

.history-sub {
    margin-top: 4px;
    font-size: 12px;
    color: #64748b;
}

.history-dates {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #334155;
    white-space: nowrap;
    padding-top: 2px;
}

.history-meta {
    margin-top: 10px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    font-size: 12px;
    color: #475569;
}

/* empty/loading */
.history-loading,
.history-empty {
    color: #64748b;
    padding: 6px 0;
}

/* ==================== RESPONSIVE ==================== */
/* Mobile Small (< 380px) */
@media (max-width: 379px) {
    .dashboard-page {
        padding: 0.75rem;
    }

    .dashboard-header {
        margin-bottom: 1.25rem;
    }

    .dashboard-title {
        font-size: 1.25rem;
    }

    .dashboard-subtitle {
        font-size: 0.8rem;
    }

    .btn-refresh-header {
        width: 38px;
        height: 38px;
        font-size: 1rem;
    }

    .status-banner {
        padding: 1rem;
        gap: 0.875rem;
    }

    .status-icon {
        width: 44px;
        height: 44px;
        font-size: 1.4rem;
    }

    .status-label {
        font-size: 1rem;
    }

    .status-time {
        font-size: 0.8rem;
    }

    .card-header {
        padding: 1rem;
    }

    .card-title {
        font-size: 1rem;
    }

    .card-body {
        padding: 1rem;
    }

    .clock-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }

    .clock-value {
        font-size: 1.25rem;
    }

    .timeline-photo img {
        max-height: 200px;
    }

    .stat-icon-badge {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }

    .stat-value {
        font-size: 1.75rem;
    }

    .stat-label {
        font-size: 0.875rem;
    }

    .contract-icon {
        width: 44px;
        height: 44px;
        font-size: 1.25rem;
    }

    .contract-value {
        font-size: 1.125rem;
    }
}

/* Mobile Medium (380px - 480px) */
@media (min-width: 380px) and (max-width: 480px) {
    .dashboard-page {
        padding: 0.875rem;
    }

    .dashboard-title {
        font-size: 1.375rem;
    }

    .dashboard-subtitle {
        font-size: 0.875rem;
    }

    .status-banner {
        padding: 1.125rem;
    }

    .status-icon {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }

    .status-label {
        font-size: 1.125rem;
    }

    .timeline-photo img {
        max-height: 220px;
    }

    .stat-value {
        font-size: 1.875rem;
    }

    .contract-value {
        font-size: 1.25rem;
    }
}

/* Mobile Large & Tablet (481px - 768px) */
@media (min-width: 481px) and (max-width: 768px) {
    .dashboard-title {
        font-size: 1.5rem;
    }

    .timeline-photo img {
        max-height: 240px;
    }

    .stat-value {
        font-size: 1.875rem;
    }

    .contract-value {
        font-size: 1.25rem;
    }
}

/* Tablet Large (769px - 1023px) */
@media (min-width: 769px) and (max-width: 1023px) {
    .main-grid {
        grid-template-columns: 1fr;
    }

    .stats-section {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .contract-section {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .section-subtitle {
        grid-column: 1 / -1;
    }
}

/* Desktop Small (1024px - 1279px) */
@media (min-width: 1024px) and (max-width: 1279px) {
    .dashboard-page {
        padding: 1.25rem;
    }
}

/* All Mobile Devices */
@media (max-width: 768px) {
    .main-grid {
        gap: 1.25rem;
    }

    .left-column,
    .right-column {
        gap: 1.25rem;
    }

    /* Fix clock times to always be 2 columns on very small screens */
    .clock-times {
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .clock-item {
        padding: 0.875rem;
        gap: 0.75rem;
    }

    .clock-label {
        font-size: 0.75rem;
    }

    .clock-value {
        font-size: 1.125rem;
    }

    /* Timeline adjustments */
    .timeline-item {
        padding-left: 1.5rem;
    }

    .timeline-marker {
        width: 10px;
        height: 10px;
        border-width: 2px;
    }

    .timeline-item:not(:last-child)::before {
        left: 4px;
    }

    .timeline-content {
        padding: 1rem;
    }

    .timeline-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .timeline-type {
        font-size: 0.95rem;
    }

    .timeline-meta {
        gap: 0.75rem;
        font-size: 0.75rem;
    }

    /* Stats cards - stack vertically */
    .stat-card {
        padding: 1.25rem;
        gap: 1rem;
    }

    /* Contract cards */
    .contract-card {
        padding: 1.25rem;
        gap: 1rem;
    }

    .contract-label {
        font-size: 0.75rem;
    }

    .contract-detail {
        font-size: 0.8rem;
    }
}

/* Very small screens optimization */
@media (max-width: 360px) {
    .clock-times {
        grid-template-columns: 1fr;
    }

    .status-banner {
        flex-direction: column;
        align-items: flex-start;
    }

    .shift-info {
        width: 100%;
        justify-content: center;
    }
}

/* Combined Attendance Card */
.attendance-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.1),
        0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.realtime-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #059669;
}

.pulse-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.card-body {
    padding: 24px;
}

/* Clock Times Section */
.clock-times-section {
    margin-bottom: 24px;
}

.clock-times {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 20px;
    align-items: center;
}

.clock-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.clock-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.1);
}

.clock-icon {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    transition: all 0.3s ease;
}

.clock-icon.clock-in {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.clock-icon.clock-out {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
    box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
}

.clock-item:hover .clock-icon {
    transform: scale(1.1);
}

.clock-details {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.clock-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.clock-value {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

.clock-divider {
    width: 1px;
    height: 60px;
    background: linear-gradient(
        to bottom,
        transparent,
        #e2e8f0 20%,
        #e2e8f0 80%,
        transparent
    );
}

/* Section Divider */
.section-divider {
    position: relative;
    text-align: center;
    margin: 32px 0 24px 0;
}

.section-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(
        to right,
        transparent,
        #e2e8f0 20%,
        #e2e8f0 80%,
        transparent
    );
}

.section-divider span {
    position: relative;
    background: #ffffff;
    padding: 0 16px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Timeline Section */
.timeline-section {
    padding-top: 8px;
}

.timeline {
    position: relative;
    padding-left: 32px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #3b82f6, #ec4899);
    opacity: 0.3;
}

.timeline-item {
    position: relative;
    padding-bottom: 24px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -28px;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: white;
    border: 3px solid #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    z-index: 1;
}

.timeline-item:last-child .timeline-marker {
    border-color: #ec4899;
    box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.1);
}

.timeline-content {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
    border: 1px solid #e2e8f0;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}

.timeline-time {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
}

.timeline-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.timeline-badge.valid {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.timeline-badge.invalid {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.timeline-type {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
}

.timeline-photo {
    margin: 12px 0;
    border-radius: 8px;
    overflow: hidden;
}

.timeline-photo img {
    width: 100%;
    height: auto;
    display: block;
}

.timeline-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e2e8f0;
}

.timeline-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #64748b;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 48px 24px;
}

.empty-icon {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-text {
    font-size: 15px;
    font-weight: 600;
    color: #64748b;
    margin: 0 0 8px 0;
}

.empty-subtext {
    font-size: 13px;
    color: #94a3b8;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .clock-times {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .clock-divider {
        display: none;
    }

    .card-header {
        padding: 16px 20px;
    }

    .card-body {
        padding: 20px;
    }

    .clock-item {
        padding: 16px;
    }

    .clock-icon {
        width: 44px;
        height: 44px;
        min-width: 44px;
        font-size: 18px;
    }

    .clock-value {
        font-size: 20px;
    }
}
</style>
