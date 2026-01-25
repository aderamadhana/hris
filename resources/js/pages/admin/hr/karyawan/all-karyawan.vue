<template>
    <AppLayout>
        <div v-if="isLoadingNonAktif" class="fullpage-loader">
            <div class="fullpage-loader__card">
                <div class="fullpage-loader__spinner"></div>
                <div class="fullpage-loader__title">
                    Loading nonaktifkan karyawan...
                </div>
                <div class="fullpage-loader__subtitle">
                    Mohon tunggu sebentar
                </div>
            </div>
        </div>
        <section class="page employees-page">
            <!-- HEADER + ACTIONS -->
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Data Karyawan</h2>
                    </div>
                    <p class="page-subtitle">
                        Kelola informasi karyawan, mulai dari penambahan data,
                        import massal, hingga penghapusan.
                    </p>
                </div>

                <div class="page-actions">
                    <DropdownButton label="Proses Data">
                        <a
                            @click="openImportKaryawanModal"
                            class="dropdown-item"
                        >
                            <font-awesome-icon icon="upload" class="icon" />
                            Upload Excel Karyawan
                        </a>

                        <a @click="downloadKaryawan" class="dropdown-item">
                            <font-awesome-icon icon="download" class="icon" />
                            Download Karyawan
                        </a>
                    </DropdownButton>
                </div>
            </div>

            <div class="overview-row">
                <div class="overview-card primary">
                    <div class="card-content">
                        <div class="card-left">
                            <div class="overview-label">Total Karyawan</div>
                            <div class="overview-value">
                                {{ totalAllKaryawan }}
                            </div>
                            <div class="overview-badge positive">
                                <span>●</span> Aktif
                            </div>
                        </div>
                        <div class="card-icon">
                            <font-awesome-icon icon="users" />
                        </div>
                    </div>
                </div>

                <div class="overview-card warning">
                    <div class="card-content">
                        <div class="card-left">
                            <div class="overview-label">
                                Kontrak Hampir Habis
                            </div>
                            <div class="overview-value">
                                {{ totalKontrakHampirHabis }}
                            </div>
                            <div class="overview-badge warning">
                                <span>●</span> {{ defaultExpiringDays }} hari
                                lagi
                            </div>
                        </div>
                        <div class="card-icon">
                            <font-awesome-icon icon="user-clock" />
                        </div>
                    </div>
                </div>

                <div
                    class="overview-card danger"
                    @click="showExpired"
                    @keydown.enter.prevent="showExpired"
                >
                    <div class="card-content">
                        <div class="card-left">
                            <div class="overview-label">
                                Total Kontrak Expired
                            </div>
                            <div class="overview-value">
                                {{ totalAllExpired }}
                            </div>
                            <div class="overview-badge negative">
                                <span>●</span> Perlu Ditindak
                            </div>
                        </div>
                        <div class="card-icon">
                            <font-awesome-icon icon="user-xmark" />
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="isDownloading" class="fullpage-loader">
                <div class="fullpage-loader__card">
                    <div class="fullpage-loader__spinner"></div>
                    <div class="fullpage-loader__title">
                        Download data karyawan…
                    </div>
                    <div class="fullpage-loader__subtitle">
                        Mohon tunggu sebentar
                    </div>
                </div>
            </div>
            <!-- TABEL DALAM CARD (Original) -->
            <div class="dt-toolbar-mobile">
                <!-- Row 1: Length & Search -->
                <div class="dt-row-main">
                    <label class="dt-length-compact">
                        Tampil
                        <select v-model.number="perPage">
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                        data
                    </label>

                    <div class="dt-search-compact">
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Cari NIK atau nama"
                        />
                    </div>
                </div>

                <!-- Row 2: Filters (collapsible on mobile) -->
                <div class="dt-filters-wrapper">
                    <button
                        class="filter-toggle-btn"
                        @click="showFilters = !showFilters"
                    >
                        <font-awesome-icon icon="filter" />
                        <span>Filter</span>
                        <font-awesome-icon
                            :icon="showFilters ? 'chevron-up' : 'chevron-down'"
                            class="toggle-icon"
                        />
                    </button>

                    <div class="dt-filters" :class="{ show: showFilters }">
                        <div class="form-group">
                            <label class="filter-label">Perusahaan</label>
                            <Select2
                                v-model="filtered_perusahaan"
                                :settings="{ width: '100%' }"
                            >
                                <option value="">Semua Perusahaan</option>
                                <option
                                    v-for="value in data_filtered_perusahaan"
                                    :key="value"
                                    :value="value"
                                >
                                    {{ value }}
                                </option>
                            </Select2>
                        </div>

                        <div class="form-group">
                            <label class="filter-label">Divisi / Dept</label>
                            <Select2
                                v-model="filtered_jabatan"
                                :settings="{ width: '100%' }"
                            >
                                <option value="">Semua Divisi / Dept</option>
                                <option
                                    v-for="value in data_filtered_jabatan"
                                    :key="value"
                                    :value="value"
                                >
                                    {{ value }}
                                </option>
                            </Select2>
                        </div>

                        <div class="form-group">
                            <label class="filter-label">Kontrak</label>
                            <Select2
                                v-model="contractExpiring"
                                :settings="{ width: '100%' }"
                            >
                                <option value="">Semua Kontrak</option>
                                <option value="7">≤ 7 Hari</option>
                                <option value="30">≤ 30 Hari</option>
                            </Select2>
                        </div>

                        <div class="form-group">
                            <label class="filter-label">Kontrak Expired</label>
                            <select
                                class="form-control"
                                v-model="contractExpired"
                                :settings="{ width: '100%' }"
                            >
                                <option value="">Pilih</option>
                                <option value="true">Ya</option>
                                <option value="false">Tidak</option>
                            </select>
                        </div>

                        <div class="form-group filter-actions">
                            <label class="filter-label">&nbsp;</label>
                            <Button
                                variant="secondary"
                                class="filter-apply-btn"
                                @click="filteredData"
                            >
                                <font-awesome-icon icon="filter" />
                                Terapkan Filter
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- TABEL DALAM CARD (Original) -->
                <div class="table-card">
                    <div class="table-responsive-custom">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-no">#</th>
                                    <th class="col-name">Nama</th>
                                    <th class="col-nik">NIK</th>
                                    <th class="col-perusahaan">Perusahaan</th>
                                    <th class="col-position">
                                        Divisi / Departemen
                                    </th>
                                    <th class="col-status">Awal Kontrak</th>
                                    <th class="col-status">Akhir Kontrak</th>
                                    <th class="col-action">Detail</th>
                                </tr>
                            </thead>

                            <tbody>
                                <!-- LOADING -->
                                <tr v-if="loadingUsers">
                                    <td colspan="8" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <!-- EMPTY -->
                                <tr v-else-if="users.length === 0">
                                    <td colspan="8" class="empty-row">
                                        Tidak ada data ...
                                    </td>
                                </tr>

                                <!-- DATA -->
                                <tr
                                    v-else
                                    v-for="(u, index) in users"
                                    :key="u.id"
                                >
                                    <td>{{ startIndex + index + 1 }}</td>

                                    <td>
                                        <button
                                            type="button"
                                            class="cell-user-btn"
                                            title="Klik untuk konfigurasi shift"
                                            @click="openShiftConfig(u)"
                                        >
                                            <div class="cell-user">
                                                <div class="cell-avatar">
                                                    {{
                                                        (u.name || '').charAt(0)
                                                    }}
                                                </div>
                                                <div class="cell-user-text">
                                                    <div class="cell-name">
                                                        {{ u.name }}
                                                        <span
                                                            class="cell-badge"
                                                        >
                                                            {{
                                                                u.shift
                                                                    .nama_shift
                                                            }}
                                                            (
                                                            {{
                                                                u.shift
                                                                    .jam_masuk
                                                            }}
                                                            -
                                                            {{
                                                                u.shift
                                                                    .jam_pulang
                                                            }}
                                                            )</span
                                                        >
                                                    </div>
                                                    <div class="cell-dept">
                                                        NRP: {{ u.nrp }}
                                                        <span class="cell-hint"
                                                            >Klik untuk atur
                                                            shift</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </td>

                                    <td>
                                        <span class="cell-nik">{{
                                            u.nik
                                        }}</span>
                                    </td>
                                    <td>
                                        <span class="cell-perusahaan">{{
                                            u.perusahaan
                                        }}</span>
                                    </td>
                                    <td>{{ u.position }}</td>
                                    <td>{{ u.awal_kontrak }}</td>
                                    <td>{{ u.akhir_kontrak }}</td>

                                    <td class="col-actions">
                                        <div class="actions-wrap">
                                            <button
                                                class="action-btn primary"
                                                title="Lihat Detail Karyawan"
                                                @click="openDetail(u.id)"
                                            >
                                                <font-awesome-icon icon="eye" />
                                            </button>

                                            <button
                                                class="action-btn danger"
                                                title="Non Aktifkan Karyawan"
                                                @click="deleteUser(u)"
                                            >
                                                <font-awesome-icon
                                                    icon="trash"
                                                />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER DATATABLE: INFO + PAGINATION -->
                    <div class="dt-footer" v-if="!loadingUsers">
                        <div class="dt-info">
                            Menampilkan
                            <strong v-if="totalItems">{{
                                startIndex + 1
                            }}</strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> karyawan
                        </div>

                        <div class="dt-pagination">
                            <button
                                class="dt-page-btn"
                                :disabled="currentPage === 1"
                                @click="goToPage(currentPage - 1)"
                            >
                                «
                            </button>

                            <button
                                v-for="page in pages"
                                :key="page"
                                class="dt-page-btn"
                                :class="{ active: page === currentPage }"
                                @click="goToPage(page)"
                            >
                                {{ page }}
                            </button>

                            <button
                                class="dt-page-btn"
                                :disabled="
                                    currentPage === totalPages ||
                                    totalPages === 0
                                "
                                @click="goToPage(currentPage + 1)"
                            >
                                »
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <ImportKaryawan
                v-if="showImportKaryawanModal"
                @closeModal="closeModalImportKaryawan"
                @refreshData="fetchEmployees"
            />
            <ImportGaji
                v-if="showImportGajiModal"
                @closeModal="closeModalImportGaji"
                @refreshData="fetchEmployees"
            />
        </section>

        <Modal
            v-if="shiftModalOpen"
            @close="closeShiftModal"
            class="shift-modal-root"
        >
            <div class="shift-config">
                <!-- HEADER -->
                <div class="shift-config__header">
                    <div>
                        <div class="shift-config__title">Konfigurasi Shift</div>
                        <div class="shift-config__subtitle">
                            Pilih shift untuk karyawan
                        </div>
                    </div>

                    <button
                        type="button"
                        class="shift-config__close"
                        @click="closeShiftModal"
                        :disabled="shiftProcessing"
                        aria-label="Tutup"
                        title="Tutup"
                    >
                        ✕
                    </button>
                </div>

                <!-- BODY -->
                <div class="shift-config__body">
                    <!-- User Card -->
                    <div class="shift-user">
                        <div class="shift-user__avatar">
                            {{ (selectedUser?.name || '-').charAt(0) }}
                        </div>
                        <div class="shift-user__meta">
                            <div class="shift-user__name">
                                {{ selectedUser?.name || '-' }}
                            </div>
                            <div class="shift-user__sub">
                                NIK: <b>{{ selectedUser?.nik || '-' }}</b>
                                <span class="dot">•</span>
                                NRP: <b>{{ selectedUser?.nrp || '-' }}</b>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="saveShiftConfig">
                        <!-- Select -->
                        <div class="form-group">
                            <label class="field-label">
                                Shift <span class="required">*</span>
                            </label>

                            <select
                                v-model="shiftForm.shift_id"
                                class="form-input"
                                required
                            >
                                <option value="" disabled>Pilih shift</option>

                                <option
                                    v-for="s in shiftOptions"
                                    :key="s.id"
                                    :value="String(s.id)"
                                >
                                    {{ s.nama_shift }}
                                    ({{ s.kode_shift }}) • {{ s.jam_masuk }}–{{
                                        s.jam_pulang
                                    }}
                                </option>
                            </select>

                            <small class="field-help">
                                Hanya shift aktif yang ditampilkan.
                            </small>
                        </div>

                        <!-- Detail Shift -->
                        <div class="shift-detail" v-if="selectedShift">
                            <div class="shift-detail__head">
                                <div class="shift-detail__title">
                                    Detail Shift
                                </div>

                                <span
                                    class="shift-pill"
                                    :class="
                                        selectedShift.is_active
                                            ? 'is-active'
                                            : 'is-inactive'
                                    "
                                >
                                    {{
                                        selectedShift.is_active
                                            ? 'Aktif'
                                            : 'Nonaktif'
                                    }}
                                </span>
                            </div>

                            <div class="shift-kv">
                                <div class="kv">
                                    <div class="kv__k">Nama Shift</div>
                                    <div class="kv__v">
                                        {{ selectedShift.nama_shift }}
                                    </div>
                                </div>

                                <div class="kv">
                                    <div class="kv__k">Kode Shift</div>
                                    <div class="kv__v">
                                        {{ selectedShift.kode_shift }}
                                    </div>
                                </div>

                                <div class="kv">
                                    <div class="kv__k">Jam Masuk</div>
                                    <div class="kv__v">
                                        {{ selectedShift.jam_masuk }}
                                    </div>
                                </div>

                                <div class="kv">
                                    <div class="kv__k">Jam Pulang</div>
                                    <div class="kv__v">
                                        {{ selectedShift.jam_pulang }}
                                    </div>
                                </div>

                                <div class="kv">
                                    <div class="kv__k">Toleransi Telat</div>
                                    <div class="kv__v">
                                        {{
                                            selectedShift.toleransi_keterlambatan
                                        }}
                                        menit
                                    </div>
                                </div>

                                <div class="kv kv--full">
                                    <div class="kv__k">Keterangan</div>
                                    <div class="kv__v kv__v--muted">
                                        {{
                                            selectedShift.keterangan ||
                                            'Tidak ada keterangan.'
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="shift-detail shift-detail--empty" v-else>
                            <div class="shift-detail__title">
                                Keterangan Shift
                            </div>
                            <div class="shift-detail__empty">
                                Pilih shift untuk melihat detailnya.
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="shift-config__footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                @click="closeShiftModal"
                                :disabled="shiftProcessing"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="
                                    shiftProcessing || !shiftForm.shift_id
                                "
                            >
                                <span
                                    v-if="shiftProcessing"
                                    class="btn-loading"
                                >
                                    <span
                                        class="btn-spinner"
                                        aria-hidden="true"
                                    ></span>
                                    <span>Memproses...</span>
                                </span>
                                <span v-else>Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import DropdownButton from '@/components/DropdownButton.vue';
import Modal from '@/components/Modal.vue';
import Select2 from '@/components/Select2.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import ImportGaji from '../../../import/ImportGaji.vue';
import ImportKaryawan from '../../../import/ImportKaryawan.vue';

export default {
    components: {
        Button,
        AppLayout,
        DropdownButton,
        ImportGaji,
        ImportKaryawan,
        Select2,
        Modal,
    },

    data() {
        return {
            users: [],
            loadingUsers: false,

            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,

            search: '',
            debouncedFetch: null,

            showImportGajiModal: false,
            showImportKaryawanModal: false,

            data_filtered_perusahaan: [],
            data_filtered_jabatan: [],

            filtered_jabatan: '',
            filtered_perusahaan: '',
            contractExpiring: '',

            isDownloading: false,
            totalAllKaryawan: 0,
            totalKontrakHampirHabis: 0,
            totalAllExpired: 0,
            defaultExpiringDays: 7,

            // modal
            shiftModalOpen: false,
            shiftProcessing: false,
            selectedUser: {
                id: null,
                name: '',
                nik: '',
                nrp: '',
            },

            // form shift
            shiftForm: {
                shift_id: '',
            },

            shiftOptions: [],
            showFilters: false,
            contractExpired: false,
            isLoadingNonAktif: false,
        };
    },

    computed: {
        startIndex() {
            if (this.totalItems === 0) return 0;
            return (this.currentPage - 1) * this.perPage;
        },
        endIndex() {
            if (this.totalItems === 0) return 0;
            return Math.min(
                this.startIndex + this.users.length,
                this.totalItems,
            );
        },
        pages() {
            if (this.totalPages <= 1) return [];
            const range = 2;
            const pages = [];
            const start = Math.max(1, this.currentPage - range);
            const end = Math.min(this.totalPages, this.currentPage + range);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },
        selectedShift() {
            const id = this.shiftForm.shift_id;
            if (!id) return null;
            return (
                this.shiftOptions.find((s) => String(s.id) === String(id)) ||
                null
            );
        },
    },

    watch: {
        search() {
            this.currentPage = 1;
            this.debouncedFetch?.();
        },
        perPage(newVal, oldVal) {
            if (newVal === oldVal) return;
            this.currentPage = 1;
            this.fetchEmployees(1);
        },
    },

    created() {
        this.debouncedFetch = this.debounce(() => {
            this.fetchEmployees(1);
        }, 400);

        // initial load sekali
        this.fetchEmployees(1);
    },

    mounted() {
        // jangan fetchEmployees lagi di sini (biar tidak double)
        this.fetchShiftOptions();
        this.getFilteredPerusahaanDanJabatan();
    },

    methods: {
        debounce(fn, delay) {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn.apply(this, args), delay);
            };
        },

        filteredData() {
            this.currentPage = 1;
            this.fetchEmployees(1);
        },

        resetFilters() {
            this.search = '';
            this.filtered_jabatan = null;
            this.filtered_perusahaan = null;

            this.contractExpired = false;
            this.contractExpiring = null; // atau default '7' kalau kamu mau
            // this.contractExpiring = '7';

            this.currentPage = 1;
            this.fetchEmployees(1);
        },

        fiturBelumTersedia() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },
        showExpired() {
            this.contractExpired = !this.contractExpired;

            if (this.contractExpired) {
                this.contractExpiring = null; // biar tidak tabrakan
            }

            this.currentPage = 1;
            this.fetchEmployees(1);
        },
        async fetchEmployees(page = this.currentPage) {
            this.loadingUsers = true;

            try {
                const res = await axios.get('/employee', {
                    params: {
                        page,
                        per_page: this.perPage,
                        search: this.search,
                        status_active: 1,
                        filtered_jabatan: this.filtered_jabatan,
                        filtered_perusahaan: this.filtered_perusahaan,
                        contract_expired: this.contractExpired,
                        contract_expiring: this.contractExpiring
                            ? Number(this.contractExpiring)
                            : null,
                    },
                });

                this.users = res.data.data || [];

                // meta
                this.currentPage = Number(res.data.meta?.current_page || page);
                this.totalItems = Number(res.data.meta?.total || 0);
                this.totalPages = Number(res.data.meta?.last_page || 0);

                // penting: paksa number supaya nggak trigger watcher loop
                const apiPerPage = Number(
                    res.data.meta?.per_page || this.perPage,
                );
                if (!Number.isNaN(apiPerPage)) this.perPage = apiPerPage;

                this.totalAllKaryawan = Number(res.data.total_all_active || 0);
                this.totalKontrakHampirHabis = Number(
                    res.data.total_contract_expiring || 0,
                );
                this.defaultExpiringDays = Number(
                    res.data.contract_expiring_days || 0,
                );
                this.totalAllExpired = Number(
                    res.data.total_contract_expired || 0,
                );
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memuat data karyawan.');
            } finally {
                this.loadingUsers = false;
            }
        },

        async getFilteredPerusahaanDanJabatan() {
            try {
                const res = await axios.get(
                    '/referensi/get-filter_perusahaan_dan_jabatan',
                );
                this.data_filtered_perusahaan = res.data.perusahaan || [];
                this.data_filtered_jabatan = res.data.position || [];
            } catch (err) {
                console.error(err);
                triggerAlert(
                    'error',
                    'Gagal memuat filter perusahaan/jabatan.',
                );
            }
        },

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            if (page === this.currentPage) return;
            this.fetchEmployees(page);
        },

        openImportKaryawanModal() {
            this.showImportKaryawanModal = true;
        },
        openImportPayslipModal() {
            this.showImportGajiModal = true;
        },
        closeModalImportKaryawan() {
            this.showImportKaryawanModal = false;
            this.selectedFileKaryawan = null;
        },
        closeModalImportGaji() {
            this.showImportGajiModal = false;
            this.selectedFilePayslip = null;
        },

        openDetail(id) {
            router.visit(`/hr/karyawan/detail-karyawan/${id}`);
        },
        openEdit(id) {
            router.visit(`/hr/karyawan/edit-karyawan/${id}`);
        },
        openPayslip(id) {
            router.visit(`/hr/karyawan/daftar-gaji/${id}`);
        },
        deleteUser(u) {
            if (!confirm(`Nonaktifkan ${u.name}?`)) return;
            this.isLoadingNonAktif = true;

            router.post(
                `/hr/karyawan/non-aktif/${u.id}`,
                {},
                {
                    onSuccess: () => {
                        triggerAlert(
                            'success',
                            'Data karyawan berhasil dinonaktifkan.',
                        );
                        this.fetchEmployees(1);
                        this.isLoadingNonAktif = false;
                    },
                },
            );
        },
        tambahKaryawan() {
            this.$inertia.visit('/hr/karyawan/tambah-karyawan');
        },

        async downloadKaryawan() {
            try {
                this.isDownloading = true;
                const response = await axios.get('/export/karyawan', {
                    responseType: 'blob',
                    params: {
                        search: this.search,
                        status_active: 1,
                        filtered_jabatan: this.filtered_jabatan,
                        filtered_perusahaan: this.filtered_perusahaan,
                        contract_expiring: this.contractExpiring
                            ? Number(this.contractExpiring)
                            : null,
                    },
                });

                const blob = new Blob([response.data], {
                    type: response.headers['content-type'],
                });

                const pad2 = (n) => String(n).padStart(2, '0');
                const d = new Date();
                const tgl = pad2(d.getDate());
                const bln = pad2(d.getMonth() + 1);
                const thn = d.getFullYear();
                const hari = pad2(d.getHours());
                const jam = pad2(d.getMinutes());
                const detik = pad2(d.getSeconds());

                const filename = `data_karyawan_${tgl}${bln}${thn}${hari}${jam}${detik}.xlsx`;

                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', filename);
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Download gagal', error);
            } finally {
                this.isDownloading = false;
            }
        },

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

        normalizeUser(u) {
            return {
                id: u?.id ?? null,
                name: u?.name ?? u?.nama ?? '',
                nik: u?.nik ?? u?.no_ktp ?? '',
                nrp: u?.nrp ?? '',
                shift_id: u?.shift_id ?? '',
                shift_start: u?.shift_start ?? '',
            };
        },

        openShiftConfig(u) {
            this.selectedUser = u;

            this.shiftForm = {
                shift_id: u.shift_id || '', // kalau list karyawan sudah bawa shift_id
            };

            this.shiftModalOpen = true;
        },
        closeShiftModal() {
            if (this.shiftProcessing) return;
            this.shiftModalOpen = false;
            this.selectedUser = null;
            this.shiftForm = { shift_id: '' };
        },

        async fetchCurrentShift(userId) {
            try {
                const { data } = await axios.get(`/employee/${userId}/shift`);
                this.shiftForm.shift_id = data?.shift_id || '';
                this.shiftForm.effective_date =
                    data?.effective_date || this.shiftForm.effective_date;
                this.shiftForm.note = data?.note || '';
            } catch (e) {
                console.warn('Gagal fetch current shift:', e);
            }
        },

        async saveShiftConfig() {
            if (!this.selectedUser.id) return;

            if (!this.shiftForm.shift_id) {
                triggerAlert('warning', 'Shift wajib diisi.');
                return;
            }

            this.shiftProcessing = true;

            try {
                await axios.post(`/hr/karyawan/shift/${this.selectedUser.id}`, {
                    shift_id: this.shiftForm.shift_id,
                });
                this.shiftProcessing = false;
                this.closeShiftModal();
                triggerAlert('success', 'Konfigurasi shift berhasil disimpan.');
                this.fetchEmployees();
            } catch (e) {
                console.error(e);
                triggerAlert('error', 'Gagal menyimpan konfigurasi shift.');
            } finally {
                this.shiftProcessing = false;
            }
        },
    },
};
</script>

<style scoped>
/* =========================================================
   1) CLICKABLE NAME CELL (untuk buka modal shift)
   ========================================================= */
.cell-user-btn {
    width: 100%;
    text-align: left;
    background: transparent;
    border: 0;
    padding: 0;
    cursor: pointer;
}

.cell-user-btn:focus {
    outline: none;
}

.cell-user-btn:focus-visible .cell-user {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18);
    border-radius: 12px;
}

.cell-user-btn .cell-user {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 6px 8px;
    border-radius: 12px;
    transition:
        background 160ms ease,
        transform 120ms ease;
}

.cell-user-btn:hover .cell-user {
    background: rgba(59, 130, 246, 0.06);
}

.cell-user-btn:active .cell-user {
    transform: translateY(1px);
}

.cell-avatar {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    background: rgba(59, 130, 246, 0.12);
    color: #1d4ed8;
    flex: 0 0 auto;
}

.cell-user-text {
    min-width: 0;
}

.cell-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cell-badge {
    font-size: 11px;
    font-weight: 900;
    padding: 3px 8px;
    border-radius: 999px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    color: #1d4ed8;
    flex: 0 0 auto;
}

.cell-dept {
    display: flex;
    gap: 10px;
    align-items: center;
    color: #64748b;
    font-size: 12px;
    margin-top: 2px;
    line-height: 1.25;
}

.cell-hint {
    color: #3b82f6;
    font-weight: 800;
    font-size: 11.5px;
}

/* =========================================================
   2) MODAL OVERRIDE (HANYA UNTUK SHIFT MODAL)
   - Ini tidak mempengaruhi modal lain
   ========================================================= */
:deep(.shift-modal-root.modal-root) {
    /* optional: kalau mau backdrop lebih halus */
}

:deep(.shift-modal-root .modal-backdrop) {
    background: rgba(15, 23, 42, 0.45);
}

:deep(.shift-modal-root .modal-panel) {
    width: min(920px, 96vw);
    max-height: calc(100vh - 88px);
    padding: 0;
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
    box-shadow:
        0 24px 60px rgba(15, 23, 42, 0.18),
        0 8px 18px rgba(15, 23, 42, 0.1);
}

/* =========================================================
   3) SHIFT CONFIG MODAL LAYOUT
   ========================================================= */
.shift-config {
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 88px);
    background: #ffffff;
}

.shift-config__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding: 16px 18px;
    border-bottom: 1px solid #e2e8f0;
    background: #fff;
}

.shift-config__title {
    font-weight: 900;
    color: #0f172a;
    font-size: 16px;
    line-height: 1.2;
}

.shift-config__subtitle {
    margin-top: 4px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.4;
}

.shift-config__close {
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    color: #334155;
    font-weight: 900;
    transition:
        background 0.15s ease,
        transform 0.12s ease;
}

.shift-config__close:hover {
    background: #f8fafc;
}

.shift-config__close:active {
    transform: translateY(1px);
}

.shift-config__close:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* body bisa scroll kalau layar kecil */
.shift-config__body {
    padding: 16px 18px 18px;
    overflow: auto;
}

/* =========================================================
   4) USER CARD DI MODAL
   ========================================================= */
.shift-user {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 12px 14px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    margin-bottom: 14px;
}

.shift-user__avatar {
    width: 44px;
    height: 44px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    background: rgba(59, 130, 246, 0.12);
    color: #1d4ed8;
    flex: 0 0 auto;
}

.shift-user__name {
    font-weight: 900;
    color: #0f172a;
    line-height: 1.2;
}

.shift-user__sub {
    margin-top: 3px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.35;
}

/* =========================================================
   5) FORM (single field + keterangan)
   ========================================================= */
.shift-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.field-label {
    font-weight: 900;
    font-size: 12px;
    margin-bottom: 6px;
    color: #0f172a;
}

.required {
    color: #ef4444;
    margin-left: 2px;
}

.field-help {
    margin-top: 6px;
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.35;
    min-height: 16px;
}

.form-input {
    width: 100%;
    height: 44px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 12px;
    outline: none;
    background: #fff;
    transition:
        border-color 0.15s ease,
        box-shadow 0.15s ease;
}

.form-input:focus {
    border-color: rgba(59, 130, 246, 0.55);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.14);
}

/* =========================================================
   6) KETERANGAN SHIFT (INFO CARD)
   ========================================================= */
.shift-desc {
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 12px 14px;
    position: relative;
    margin-top: 6px;
}

.shift-desc::before {
    content: '';
    position: absolute;
    left: 0;
    top: 12px;
    bottom: 12px;
    width: 4px;
    border-radius: 999px;
    background: rgba(59, 130, 246, 0.65);
}

.shift-desc__label {
    padding-left: 10px;
    font-size: 12px;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 6px;
}

.shift-desc__value {
    padding-left: 10px;
    font-size: 13px;
    line-height: 1.55;
    color: #334155;
    white-space: pre-wrap;
}

.shift-desc__value.muted {
    color: #64748b;
}

/* =========================================================
   7) FOOTER ACTIONS (sticky)
   ========================================================= */
.shift-config__footer {
    position: sticky;
    bottom: 0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 18px;
    margin-top: 14px;
    border-top: 1px solid #e2e8f0;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(6px);
}

/* detail card */
.shift-detail {
    margin-top: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    border-radius: 14px;
    padding: 12px 14px;
}

.shift-detail__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
}

.shift-detail__title {
    font-weight: 900;
    color: #0f172a;
    font-size: 13px;
}
/* key-value grid */
.shift-kv {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px 14px;
}

.kv {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}

.kv__k {
    font-size: 11px;
    color: #64748b;
    font-weight: 800;
}

.kv__v {
    font-size: 13px;
    color: #0f172a;
    font-weight: 700;
    word-break: break-word;
}

.kv__v--muted {
    color: #475569;
    font-weight: 600;
    line-height: 1.5;
}

.kv--full {
    grid-column: 1 / -1;
}

.kv--meta .kv__v {
    font-weight: 600;
    color: #64748b;
}
/* =========================================================
   8) BUTTON SPINNER (untuk tombol simpan)
   ========================================================= */
.btn-loading {
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-spinner {
    width: 16px;
    height: 16px;
    border-radius: 999px;
    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: rgba(255, 255, 255, 0.95);
    animation: btnSpin 0.8s linear infinite;
}

@keyframes btnSpin {
    to {
        transform: rotate(360deg);
    }
}

/* =========================================================
   9) RESPONSIVE
   ========================================================= */
@media (max-width: 720px) {
    .shift-config__header {
        padding: 14px 14px;
    }

    .shift-config__body {
        padding: 14px 14px 16px;
    }

    :deep(.shift-modal-root .modal-panel) {
        width: min(96vw, 920px);
        max-height: calc(100vh - 56px);
        border-radius: 16px;
    }

    .shift-config {
        max-height: calc(100vh - 56px);
    }

    .shift-config__footer {
        padding: 12px 14px;
    }
}
.shift-pill {
    font-size: 11px;
    font-weight: 900;
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid transparent;
}

.shift-pill.is-active {
    background: rgba(34, 197, 94, 0.12);
    border-color: rgba(34, 197, 94, 0.24);
    color: #166534;
}

.shift-pill.is-inactive {
    background: rgba(239, 68, 68, 0.12);
    border-color: rgba(239, 68, 68, 0.24);
    color: #991b1b;
}
</style>
