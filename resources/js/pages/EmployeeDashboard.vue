<template>
    <div v-if="loading" class="fullpage-loader">
        <div class="fullpage-loader__card">
            <div class="fullpage-loader__spinner"></div>
            <div class="fullpage-loader__title">Loading data dashboard</div>
            <div class="fullpage-loader__subtitle">Mohon tunggu sebentar</div>
        </div>
    </div>
    <div
        v-if="contractAlert.show"
        class="contract-alert"
        :class="`alert-${contractAlert.type}`"
    >
        <div class="alert-left">
            <div class="alert-badge">
                {{ contractAlert.badge }}
            </div>

            <div class="alert-text">
                <div class="alert-title">
                    {{ contractAlert.title }}
                </div>
                <div class="alert-message">
                    {{ contractAlert.message }}
                </div>
            </div>
        </div>
    </div>
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
            <font-awesome-icon icon="rotate" :class="{ 'fa-spin': loading }" />
        </button>
    </div>

    <div class="card dashboard-page">
        <div class="dashboard-content">
            <!-- Date & Shift -->
            <div class="date-now">
                <p class="dashboard-subtitle">{{ todayDate }}</p>

                <div v-if="shiftInfo" class="shift-paneldsa">
                    <div class="shift-panel__head">
                        <font-awesome-icon icon="briefcase" />

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

            <!-- MAIN GRID -->
            <div class="main-grid">
                <!-- LEFT -->
                <div class="left-column">
                    <div class="attendance-card">
                        <div class="card-header">
                            <h3 class="card-title">Presensi Hari Ini</h3>
                            <div class="realtime-badge">
                                <span class="pulse-dot"></span>
                                Live
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Clock -->
                            <div class="clock-times-section">
                                <div class="clock-times">
                                    <div
                                        class="clock-item clock-in-item"
                                        @click="goToAbsen()"
                                    >
                                        <div class="clock-icon clock-in">
                                            <font-awesome-icon
                                                icon="arrow-right-to-bracket"
                                            />
                                        </div>
                                        <div class="clock-details">
                                            <span class="clock-label"
                                                >Clock In</span
                                            >
                                            <span class="clock-value">
                                                {{ clockInTime }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="clock-divider"></div>

                                    <div
                                        class="clock-item clock-out-item"
                                        @click="goToAbsen()"
                                    >
                                        <div class="clock-icon clock-out">
                                            <font-awesome-icon
                                                icon="arrow-right-from-bracket"
                                            />
                                        </div>
                                        <div class="clock-details">
                                            <span class="clock-label"
                                                >Clock Out</span
                                            >
                                            <span class="clock-value">
                                                {{ clockOutTime }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider">
                                <span>Detail Presensi</span>
                            </div>

                            <!-- Timeline -->
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
                                                        icon="clock"
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
                                                        :icon="
                                                            item.is_valid_location
                                                                ? 'check-circle'
                                                                : 'exclamation-circle'
                                                        "
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
                                                        icon="location-dot"
                                                    />
                                                    {{
                                                        item.jarak_dari_lokasi
                                                    }}m
                                                </span>

                                                <span v-if="item.device_info">
                                                    <font-awesome-icon
                                                        icon="mobile-screen"
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
                                            icon="calendar-xmark"
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

                    <!-- Activity -->
                    <div class="activity-section">
                        <div class="section-header">
                            <h3 class="section-subtitle">Aktivitas Hari Ini</h3>
                            <button
                                @click="openActivityModal"
                                class="btn-add-activity"
                            >
                                <font-awesome-icon icon="plus" />
                                Tambah
                            </button>
                        </div>

                        <div
                            v-if="recentActivities.length"
                            class="activity-list"
                        >
                            <div
                                v-for="activity in recentActivities"
                                :key="activity.id"
                                class="activity-item"
                            >
                                <div class="activity-time">
                                    <font-awesome-icon icon="clock" />
                                    {{ activity.time }} • {{ activity.tgl }}
                                </div>

                                <div class="activity-description">
                                    <strong>{{ activity.kode_kerja }}</strong>
                                    <!-- • Shift {{ activity.shift }} -->
                                </div>

                                <div class="activity-meta">
                                    <span
                                        >Hasil: {{ activity.hasil_kerja }}</span
                                    >
                                    <span v-if="activity.hasil_lembur">
                                        • Lembur: {{ activity.hasil_lembur }}
                                    </span>
                                    <span class="activity-total">
                                        • ACT: Rp
                                        {{ formatCurrency(activity.total_act) }}
                                    </span>
                                </div>

                                <!-- ACTION -->
                                <div class="activity-actions">
                                    <button
                                        class="btn-edit"
                                        @click="editActivity(activity)"
                                    >
                                        <font-awesome-icon
                                            icon="pen-to-square"
                                        />
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-else class="empty-state">
                            <div class="empty-icon">
                                <font-awesome-icon icon="clipboard-list" />
                            </div>
                            <p class="empty-text">
                                Belum ada aktivitas hari ini
                            </p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="right-column">
                    <div class="stats-section">
                        <h3 class="section-subtitle">Statistik Bulan Ini</h3>

                        <div class="stat-card card-success">
                            <div class="stat-icon-badge success">
                                <font-awesome-icon icon="calendar-check" />
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
                                <font-awesome-icon icon="clock-rotate-left" />
                            </div>
                            <div class="stat-body">
                                <div class="stat-value">{{ lateCount }}x</div>
                                <div class="stat-label">Terlambat</div>
                                <div class="stat-sublabel">Bulan ini</div>
                            </div>
                        </div>
                    </div>

                    <div class="contract-section">
                        <h3 class="section-subtitle">Informasi Kontrak</h3>

                        <div class="contract-card clickable">
                            <div class="contract-icon">
                                <font-awesome-icon icon="clock-rotate-left" />
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
                                    <font-awesome-icon icon="arrow-right" />
                                    Lihat detail riwayat
                                </div>
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
                                <span>{{ h.tgl_awal_kerja_label || '-' }}</span>
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
    <!-- Modal Input Aktivitas -->
    <Modal v-if="showActivityModal" @click.self="closeActivityModal">
        <div class="activity-modal">
            <div class="activity-modal__header">
                <div class="activity-modal__title">
                    <font-awesome-icon :icon="['fas', 'clipboard-list']" />
                    Log Aktivitas
                </div>
                <button
                    class="activity-modal__close"
                    type="button"
                    @click="closeActivityModal"
                >
                    ×
                </button>
            </div>

            <div class="activity-modal__body">
                <form @submit.prevent="submitActivity">
                    <!-- Tanggal -->
                    <div class="form-group">
                        <label for="activity-date" class="form-label">
                            Tanggal
                            <span class="required">*</span>
                        </label>
                        <input
                            id="activity-date"
                            v-model="activityForm.tgl"
                            type="date"
                            class="form-input"
                            required
                        />
                    </div>

                    <!-- Shift -->
                    <div class="form-group">
                        <label for="activity-shift" class="form-label">
                            Shift
                            <span class="required">*</span>
                        </label>
                        <select
                            id="activity-shift"
                            v-model="activityForm.shift"
                            class="form-input"
                            style="pointer-events: none"
                            required
                        >
                            <option value="">-- Pilih Shift --</option>
                            <option
                                v-for="shift in shiftOptions"
                                :key="shift.id"
                                :value="shift.id"
                            >
                                {{ shift.nama_shift }} ( {{ shift.jam_masuk }} -
                                {{ shift.jam_pulang }} )
                            </option>
                        </select>
                    </div>

                    <!-- Aktivitas -->
                    <div class="form-group">
                        <label for="activity-type" class="form-label">
                            Jenis Aktivitas
                            <span class="required">*</span>
                        </label>
                        <Select2
                            id="activity-type"
                            v-model="activityForm.aktifitas_id"
                            class="form-input"
                            required
                            @change="onActivityChange"
                        >
                            <option value="">-- Pilih Aktivitas --</option>
                            <option
                                v-for="activity in availableActivities"
                                :key="activity.id"
                                :value="activity.id"
                            >
                                {{ activity.kode }} -
                                {{ activity.nama_aktifitas }}
                            </option>
                        </Select2>
                    </div>

                    <!-- Jam Kerja -->
                    <div class="form-row">
                        <div class="form-group form-group-half">
                            <label for="jam-masuk" class="form-label">
                                Waktu Mulai
                            </label>
                            <input
                                id="jam-masuk"
                                v-model="activityForm.jam_masuk"
                                type="time"
                                class="form-input"
                            />
                        </div>

                        <div class="form-group form-group-half">
                            <label for="jam-pulang" class="form-label">
                                Waktu Selesai
                            </label>
                            <input
                                id="jam-pulang"
                                v-model="activityForm.jam_pulang"
                                type="time"
                                class="form-input"
                            />
                        </div>
                    </div>

                    <!-- Hasil Kerja -->
                    <div class="form-section">
                        <h4 class="form-section-title">Hasil Kerja</h4>

                        <div class="form-row">
                            <div class="form-group form-group-half">
                                <label for="hasil-kerja" class="form-label">
                                    Hasil Kerja
                                </label>
                                <input
                                    id="hasil-kerja"
                                    v-model.number="activityForm.hasil_kerja"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>

                            <div class="form-group form-group-half">
                                <label for="hasil-lembur" class="form-label">
                                    Hasil Lembur
                                </label>
                                <input
                                    id="hasil-lembur"
                                    v-model.number="activityForm.hasil_lembur"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-half">
                                <label for="return-qty" class="form-label">
                                    Return Qty
                                </label>
                                <input
                                    id="return-qty"
                                    v-model.number="activityForm.return_qty"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>

                            <div class="form-group form-group-half">
                                <label for="tolak-qc" class="form-label">
                                    Tolak QC
                                </label>
                                <input
                                    id="tolak-qc"
                                    v-model.number="activityForm.tolak_qc"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- SCF -->
                    <div class="form-section">
                        <h4 class="form-section-title">SCF (Standard Cost)</h4>

                        <div class="form-row">
                            <div class="form-group form-group-half">
                                <label for="upah-scf" class="form-label">
                                    Upah SCF (Rp)
                                </label>
                                <input
                                    id="upah-scf"
                                    v-model.number="activityForm.upah_scf"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>

                            <div class="form-group form-group-half">
                                <label for="bantu-scf" class="form-label">
                                    Bantu SCF (Rp)
                                </label>
                                <input
                                    id="bantu-scf"
                                    v-model.number="activityForm.bantu_scf"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="denda-scf" class="form-label">
                                Denda SCF (Rp)
                            </label>
                            <input
                                id="denda-scf"
                                v-model.number="activityForm.denda_scf"
                                type="number"
                                class="form-input"
                                min="0"
                                placeholder="0"
                            />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Total SCF</label>
                            <div class="form-input-display">
                                Rp {{ formatCurrency(totalSCF) }}
                            </div>
                        </div>
                    </div>

                    <!-- ACT -->
                    <div class="form-section">
                        <h4 class="form-section-title">ACT (Actual Cost)</h4>

                        <div class="form-row">
                            <div class="form-group form-group-half">
                                <label for="upah-act" class="form-label">
                                    Upah ACT (Rp)
                                </label>
                                <input
                                    id="upah-act"
                                    v-model.number="activityForm.upah_act"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>

                            <div class="form-group form-group-half">
                                <label for="upah-bantu-act" class="form-label">
                                    Upah Bantu ACT (Rp)
                                </label>
                                <input
                                    id="upah-bantu-act"
                                    v-model.number="activityForm.upah_bantu_act"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group form-group-half">
                                <label for="return-act" class="form-label">
                                    Return ACT (Rp)
                                </label>
                                <input
                                    id="return-act"
                                    v-model.number="activityForm.return_act"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>

                            <div class="form-group form-group-half">
                                <label for="denda-act" class="form-label">
                                    Denda ACT (Rp)
                                </label>
                                <input
                                    id="denda-act"
                                    v-model.number="activityForm.denda_act"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Total ACT</label>
                            <div class="form-input-display">
                                Rp {{ formatCurrency(totalACT) }}
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="form-group">
                        <label for="keterangan" class="form-label">
                            Keterangan
                        </label>
                        <textarea
                            id="keterangan"
                            v-model="activityForm.ket"
                            class="form-textarea"
                            rows="3"
                            placeholder="Keterangan tambahan..."
                            maxlength="500"
                        ></textarea>
                        <div class="char-counter">
                            {{ activityForm.ket?.length || 0 }}/500
                        </div>
                    </div>

                    <div class="form-actions">
                        <button
                            type="button"
                            @click="closeActivityModal"
                            class="btn-cancel"
                            :disabled="activitySubmitting"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="btn-submit"
                            :disabled="activitySubmitting"
                        >
                            <font-awesome-icon
                                v-if="activitySubmitting"
                                :icon="['fas', 'spinner']"
                                class="fa-spin"
                            />
                            <span v-else>Simpan Aktivitas</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal.vue';
import Select2 from '@/components/Select2.vue';
import { triggerAlert } from '@/utils/alert';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    name: 'DashboardPresensiKontrak',
    components: {
        Modal,
        Select2,
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

            showActivityModal: false,
            activitySubmitting: false,
            availableActivities: [], // List aktivitas dari tabel 'aktifitas'
            activityForm: {
                id: null,
                tgl: '',
                shift: '',
                aktifitas_id: '',
                jam_masuk: '',
                jam_pulang: '',
                hasil_kerja: 0,
                hasil_lembur: 0,
                return_qty: 0,
                tolak_qc: 0,
                upah_scf: 0,
                bantu_scf: 0,
                denda_scf: 0,
                upah_act: 0,
                upah_bantu_act: 0,
                return_act: 0,
                denda_act: 0,
                ket: '',
            },
            recentActivities: [],
            shiftOptions: [],

            contractAlert: {
                show: true,
                type: 'info', // info | warning | error
                title: '',
                message: '',
            },
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
        totalSCF() {
            const upah = Number(this.activityForm.upah_scf) || 0;
            const bantu = Number(this.activityForm.bantu_scf) || 0;
            const denda = Number(this.activityForm.denda_scf) || 0;
            return upah + bantu - denda;
        },

        totalACT() {
            const upah = Number(this.activityForm.upah_act) || 0;
            const bantu = Number(this.activityForm.upah_bantu_act) || 0;
            const returnVal = Number(this.activityForm.return_act) || 0;
            const denda = Number(this.activityForm.denda_act) || 0;
            return upah + bantu - returnVal - denda;
        },
    },

    mounted() {
        this.fetchDashboardData();
        this.loadAvailableActivities();
        this.loadRecentActivities();
        this.fetchShiftOptions();
        this.setDefaultDate();
        window.addEventListener('keydown', this.onKeydown);
    },

    beforeUnmount() {
        window.removeEventListener('keydown', this.onKeydown);
        document.body.style.overflow = '';
    },

    methods: {
        async fetchShiftOptions() {
            try {
                // Pastikan endpoint return data dari table `shift`
                // format ideal: [{id, nama_shift, keterangan}]
                const { data } = await axios.get(
                    '/referensi/get-shift-options',
                );

                const arr = Array.isArray(data) ? data : data.data || [];

                this.shiftOptions = Array.isArray(data?.data) ? data.data : [];
            } catch (e) {
                console.warn('Gagal fetch shift options:', e);
                this.shiftOptions = [];
            }
        },
        setDefaultDate() {
            const today = new Date();
            this.activityForm.tgl = today.toISOString().split('T')[0];
        },
        async loadAvailableActivities() {
            try {
                const response = await axios.get('/referensi/get-aktifitas');
                if (response.data.success) {
                    this.availableActivities = response.data.data;
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        },

        openActivityModal() {
            this.showActivityModal = true;
            this.activityForm.shift = this.shiftInfo.id_shift;
            this.activityForm.jam_masuk = this.shiftInfo.jam_masuk;
            this.activityForm.jam_pulang = this.shiftInfo.jam_pulang;
            this.setDefaultDate();
        },

        closeActivityModal() {
            this.showActivityModal = false;
            this.resetActivityForm();
        },

        resetActivityForm() {
            this.activityForm = {
                id: null,
                tgl: '',
                shift: '',
                aktifitas_id: '',
                jam_masuk: '',
                jam_pulang: '',
                hasil_kerja: 0,
                hasil_lembur: 0,
                return_qty: 0,
                tolak_qc: 0,
                upah_scf: 0,
                bantu_scf: 0,
                denda_scf: 0,
                upah_act: 0,
                upah_bantu_act: 0,
                return_act: 0,
                denda_act: 0,
                ket: '',
            };
        },

        onActivityChange() {
            // Auto-fill data berdasarkan aktivitas yang dipilih
            const selectedActivity = this.availableActivities.find(
                (a) => a.id === this.activityForm.aktifitas_id,
            );

            if (selectedActivity) {
                // Bisa auto-fill upah SCF dari master data
                if (selectedActivity.upah_standar) {
                    this.activityForm.upah_scf = selectedActivity.upah_standar;
                }
            }
        },

        async submitActivity() {
            if (!this.user?.employee_id) {
                triggerAlert('error', 'Employee tidak ditemukan');
                return;
            }

            this.activitySubmitting = true;

            try {
                // =========================
                // Hitung jam kerja (menit)
                // =========================
                let jam_kerja_menit = null;
                const { jam_masuk, jam_pulang } = this.activityForm;

                if (jam_masuk && jam_pulang) {
                    const [jm, mm] = jam_masuk.split(':').map(Number);
                    const [jp, mp] = jam_pulang.split(':').map(Number);

                    if (
                        Number.isFinite(jm) &&
                        Number.isFinite(mm) &&
                        Number.isFinite(jp) &&
                        Number.isFinite(mp)
                    ) {
                        const masuk = jm * 60 + mm;
                        const pulang = jp * 60 + mp;

                        jam_kerja_menit = pulang - masuk;
                        if (jam_kerja_menit < 0) jam_kerja_menit += 1440;
                    }
                }

                // =========================
                // Payload
                // =========================
                const payload = {
                    ...this.activityForm,
                    jam_kerja_menit,
                    total_scf: this.totalSCF,
                    total_act: this.totalACT,
                    employee_id: this.user.employee_id,
                };

                // =========================
                // MODE: CREATE vs EDIT
                // =========================
                const isEdit = Boolean(this.activityForm.id);

                const endpoint = isEdit
                    ? `/logs/aktifitas/update/${this.activityForm.id}`
                    : '/logs/aktifitas/store';

                const method = isEdit ? 'put' : 'post';

                const { data } = await axios({
                    method,
                    url: endpoint,
                    data: payload,
                });

                if (data?.success) {
                    triggerAlert(
                        'success',
                        isEdit
                            ? 'Log aktivitas berhasil diperbarui'
                            : 'Log aktivitas berhasil disimpan',
                    );

                    this.closeActivityModal();
                    this.loadRecentActivities();
                } else {
                    triggerAlert(
                        'error',
                        data?.message || 'Gagal menyimpan log aktivitas',
                    );
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    const firstError = Object.values(errors)?.[0]?.[0];
                    if (firstError) triggerAlert('error', firstError);
                } else if (error.response?.data?.message) {
                    triggerAlert('error', error.response.data.message);
                } else {
                    triggerAlert('error', 'Gagal menyimpan log aktivitas');
                }
            } finally {
                this.activitySubmitting = false;
            }
        },

        async loadRecentActivities() {
            try {
                const employeeId = this.user?.employee_id;
                const response = await axios.get(
                    '/logs/aktifitas/recent?limit=5&employee_id=' + employeeId,
                );

                if (response.data.success) {
                    this.recentActivities = response.data.data;
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        },

        formatCurrency(value) {
            if (!value) return '0';
            return new Intl.NumberFormat('id-ID').format(value);
        },
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

                // =========================
                // PRESENSI HARI INI
                // =========================
                if (
                    attendanceRes.status === 'fulfilled' &&
                    attendanceRes.value.data?.success
                ) {
                    this.todayAttendance =
                        attendanceRes.value.data.data ?? null;
                } else {
                    this.todayAttendance = null;
                }

                // =========================
                // STATISTIK BULANAN
                // =========================
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

                // =========================
                // KONTRAK + ALERT
                // =========================
                if (contractRes.status === 'fulfilled') {
                    const payload = contractRes.value.data ?? null;

                    this.contractSummary = payload;

                    // ⬇⬇⬇ INI INTINYA ⬇⬇⬇
                    this.buildContractAlert(payload);
                } else {
                    this.contractSummary = null;

                    // fallback alert kalau gagal load kontrak
                    this.buildContractAlert(null);
                }
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

        getCurrentTime() {
            const now = new Date();
            return now.toTimeString().slice(0, 5); // Format HH:MM
        },

        editActivity(activity) {
            this.showActivityModal = true;

            // Simpan ID untuk mode edit
            this.activityForm.id = activity.id;

            this.activityForm = {
                id: activity.id,
                tgl: activity.tgl_raw ?? activity.tgl, // tergantung response
                shift: activity.shift,
                aktifitas_id: activity.aktifitas_id,
                jam_masuk: activity.jam_masuk ?? '',
                jam_pulang: activity.jam_pulang ?? '',
                hasil_kerja: activity.hasil_kerja ?? 0,
                hasil_lembur: activity.hasil_lembur ?? 0,
                return_qty: activity.return_qty ?? 0,
                tolak_qc: activity.tolak_qc ?? 0,
                upah_scf: activity.upah_scf ?? 0,
                bantu_scf: activity.bantu_scf ?? 0,
                denda_scf: activity.denda_scf ?? 0,
                upah_act: activity.upah_act ?? 0,
                upah_bantu_act: activity.upah_bantu_act ?? 0,
                return_act: activity.return_act ?? 0,
                denda_act: activity.denda_act ?? 0,
                ket: activity.ket ?? '',
            };
        },

        buildContractAlert(payload) {
            const current = payload?.current;

            if (!current || !payload?.contract_end_date) {
                this.contractAlert = {
                    show: true,
                    type: 'warning',
                    badge: 'KONTRAK',
                    title: 'Tidak ada kontrak aktif',
                    message: 'Silakan hubungi HR untuk kejelasan status kerja.',
                };
                return;
            }

            const today = new Date();
            const endDate = new Date(payload.contract_end_date);
            const diffDays = Math.ceil(
                (endDate - today) / (1000 * 60 * 60 * 24),
            );

            // EXPIRED
            if (diffDays < 0) {
                this.contractAlert = {
                    show: true,
                    type: 'error',
                    badge: 'EXPIRED',
                    title: `${current.jenis_kontrak} • Kontrak Berakhir`,
                    message: `Kontrak telah berakhir pada ${payload.contract_end_date_label}.`,
                };
                return;
            }

            // CRITICAL (≤ 30 hari)
            if (diffDays <= 30) {
                this.contractAlert = {
                    show: true,
                    type: 'warning',
                    badge: `H-${diffDays}`,
                    title: `${current.jenis_kontrak} • Hampir Berakhir`,
                    message: `Kontrak akan berakhir pada ${payload.contract_end_date_label}. Hubungi admin untuk tindak lanjut.`,
                };
                return;
            }

            // INFO (aman)
            this.contractAlert = {
                show: true,
                type: 'info',
                badge: current.jenis_kontrak,
                title: 'Kontrak Aktif',
                message: `Berlaku hingga ${payload.contract_end_date_label} (${diffDays} hari lagi).`,
            };
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
    min-width: 0;
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

/* SHIFT PANEL */
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

/* ==================== COMBINED ATTENDANCE CARD ==================== */
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

/* ==================== CLOCK TIMES ==================== */
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
    cursor: pointer;
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

/* ==================== SECTION DIVIDER ==================== */
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

/* ==================== TIMELINE ==================== */
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

/* ==================== EMPTY STATE ==================== */
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

.empty-state-small {
    text-align: center;
    padding: 2rem 1rem;
    color: #9ca3af;
}

.empty-state-small svg {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}

/* ==================== RIGHT COLUMN ==================== */
.section-subtitle {
    font-size: 1rem;
    font-weight: 600;
    color: #475569;
    margin: 0 0 1rem 0;
}

.stats-section,
.contract-section,
.activity-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* ==================== STAT CARDS ==================== */
.stat-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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

/* ==================== ACTIVITY SECTION ==================== */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.btn-add-activity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-add-activity:hover {
    background: #4338ca;
    transform: translateY(-1px);
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.activity-item {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 3px solid #4f46e5;
}

.activity-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.activity-description {
    font-size: 0.875rem;
    color: #1f2937;
    line-height: 1.5;
}

/* ==================== CONTRACT CARDS ==================== */
.contract-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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

/* ==================== HISTORY MODAL ==================== */
.history-modal {
    width: 100%;
    max-width: 700px;
    background: white;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    max-height: 80vh;
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
    transition: background 0.2s;
}

.history-modal__close:hover {
    background: #f8fafc;
}

.history-modal__body {
    padding: 16px 18px;
    overflow: auto;
    background: #fff;
}

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

.history-loading,
.history-empty {
    color: #64748b;
    padding: 6px 0;
}

/* ==================== ACTIVITY MODAL ==================== */
.activity-modal {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.activity-modal__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.activity-modal__title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.activity-modal__close {
    background: none;
    border: none;
    font-size: 2rem;
    color: #9ca3af;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
}

.activity-modal__close:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.activity-modal__body {
    padding: 1.5rem;
    overflow-y: auto;
}

/* ==================== FORM STYLES ==================== */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.required {
    color: #ef4444;
}

.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    font-family: inherit;
    resize: vertical;
    transition: border-color 0.2s;
}

.form-textarea:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.char-counter {
    text-align: right;
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 0.25rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-hint {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.form-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn-cancel {
    padding: 0.75rem 1.5rem;
    background: white;
    color: #6b7280;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover:not(:disabled) {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-submit {
    padding: 0.75rem 1.5rem;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 140px;
}

.btn-submit:hover:not(:disabled) {
    background: #4338ca;
    transform: translateY(-1px);
}

.btn-submit:disabled,
.btn-cancel:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* ==================== RECENT ACTIVITIES IN MODAL ==================== */
.recent-activities {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.recent-activities__header h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.recent-activities__list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.recent-activity-item {
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 3px solid #4f46e5;
}

.recent-activity-time {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.375rem;
}

.recent-activity-desc {
    font-size: 0.813rem;
    color: #1f2937;
    line-height: 1.5;
    margin-bottom: 0.375rem;
}

.recent-activity-date {
    font-size: 0.75rem;
    color: #9ca3af;
}

/* ==================== RESPONSIVE ==================== */
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

    .activity-modal {
        width: 95%;
        max-height: 85vh;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-cancel,
    .btn-submit {
        width: 100%;
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
.activity-actions {
    margin-top: 6px;
    display: flex;
    justify-content: flex-end;
}

.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 6px 10px;
    font-size: 12px;
    font-weight: 500;

    color: #2563eb; /* biru */
    background-color: #eff6ff;

    border: 1px solid #bfdbfe;
    border-radius: 6px;

    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-edit:hover {
    background-color: #dbeafe;
    border-color: #93c5fd;
    color: #1d4ed8;
}

.btn-edit:active {
    transform: translateY(1px);
}

.btn-edit svg {
    font-size: 13px;
}
.dashboard-alert {
    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 12px 14px;
    margin-bottom: 14px;
    border-radius: 10px;
    font-size: 14px;
}

.alert-info {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
}

.alert-warning {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert-action {
    background: transparent;
    border: none;
    font-size: 13px;
    font-weight: 600;
    color: inherit;
    cursor: pointer;
    text-decoration: underline;
}
.contract-alert {
    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 14px 16px;
    margin-bottom: 16px;

    border-radius: 12px;
    border-left: 6px solid;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
}

.alert-left {
    display: flex;
    gap: 14px;
    align-items: center;
}

.alert-badge {
    min-width: 64px;
    padding: 6px 10px;

    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    text-align: center;
    white-space: nowrap;
}

.alert-title {
    font-size: 15px;
    font-weight: 700;
}

.alert-message {
    font-size: 13px;
    margin-top: 2px;
    opacity: 0.9;
}

/* ACTION */
.alert-action {
    background: transparent;
    border: none;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: underline;
}

/* VARIANTS */
.alert-info {
    background: #eff6ff;
    border-color: #2563eb;
    color: #1e3a8a;
}
.alert-info .alert-badge {
    background: #2563eb;
    color: #fff;
}

.alert-warning {
    background: #fffbeb;
    border-color: #f59e0b;
    color: #92400e;
}
.alert-warning .alert-badge {
    background: #f59e0b;
    color: #fff;
}

.alert-error {
    background: #fef2f2;
    border-color: #dc2626;
    color: #991b1b;
}
.alert-error .alert-badge {
    background: #dc2626;
    color: #fff;
}
</style>
