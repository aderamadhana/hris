<template>
    <AppLayout>
        <section class="page employees-page">
            <div v-if="isDownloadingGaji" class="fullpage-loader">
                <div class="fullpage-loader__card">
                    <div class="fullpage-loader__spinner"></div>
                    <div class="fullpage-loader__title">
                        Proses download gaji…
                    </div>
                    <div class="fullpage-loader__subtitle">
                        Mohon tunggu sebentar
                    </div>
                </div>
            </div>
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Periode Gaji</h2>
                    </div>

                    <p class="page-subtitle">
                        Kelola informasi periode penggajian untuk karyawan
                    </p>
                </div>
                <Button variant="primary" @click="tambahPeriode">
                    + Tambah Periode
                </Button>
            </div>
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
                            placeholder="Cari periode"
                        />
                    </div>
                </div>

                <!-- Row 2: Filters (collapsible) -->
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
                            <label>Filter Status</label>
                            <select
                                v-model="filtered_status"
                                class="filter-input"
                            >
                                <option value="">Semua Status</option>
                                <option value="open">Terbuka</option>
                                <option value="closed">Ditutup</option>
                                <option value="processed">Diproses</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input
                                type="date"
                                v-model="filtered_tanggal_mulai"
                                class="form-control"
                            />
                        </div>
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input
                                type="date"
                                v-model="filtered_tanggal_selesai"
                                :min="filtered_tanggal_mulai"
                                class="form-control"
                            />
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

                <!-- TABLE CARD -->
                <div class="table-card">
                    <div class="table-responsive-custom">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-no" style="width: 5%">No</th>
                                    <th style="width: 20%">Judul Periode</th>
                                    <th style="width: 5%">Tahun</th>
                                    <th style="width: 10%">Bulan</th>
                                    <th style="width: 20%">Tanggal Mulai</th>
                                    <th style="width: 20%">Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th class="col-action">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="loadingPeriods">
                                    <td colspan="8" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="periodsData.length === 0">
                                    <td colspan="8" class="empty-row">
                                        Tidak ada data ...
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(period, index) in periodsData"
                                    :key="period.id"
                                >
                                    <td style="text-align: center">
                                        {{ startIndex + index + 1 }}
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="cell-konfig"
                                            title="Klik untuk edit periode"
                                            @click="openPeriodeConfig(period)"
                                        >
                                            <span class="cell-konfig__title">{{
                                                period.judul || '-'
                                            }}</span>
                                            <span class="cell-konfig__hint"
                                                >Klik untuk edit periode</span
                                            >
                                        </button>
                                    </td>
                                    <td>{{ period.year }}</td>
                                    <td>{{ getMonthName(period.month) }}</td>
                                    <td>{{ period.start_date }}</td>
                                    <td>{{ period.end_date }}</td>
                                    <td>
                                        <span
                                            class="status-pill"
                                            :class="
                                                getStatusClass(period.status)
                                            "
                                        >
                                            {{ getStatusLabel(period.status) }}
                                        </span>
                                    </td>
                                    <td class="col-actions">
                                        <div class="actions-wrap">
                                            <Button
                                                @click="uploadGaji(period)"
                                                variant="success"
                                                title="Upload Gaji"
                                                style="width: 120px"
                                            >
                                                <font-awesome-icon
                                                    icon="upload"
                                                />
                                                Upload
                                            </Button>

                                            <Button
                                                @click="downloadGaji(period)"
                                                variant="primary"
                                                title="Download Gaji"
                                                style="width: 120px"
                                            >
                                                <font-awesome-icon
                                                    icon="download"
                                                />
                                                Download
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER -->
                    <div
                        class="dt-footer"
                        v-if="!loadingPeriods && periodsData.length > 0"
                    >
                        <div class="dt-info">
                            Menampilkan
                            <strong v-if="totalItems">{{
                                startIndex + 1
                            }}</strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> periode
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
                                v-for="(page, idx) in pages"
                                :key="idx"
                                class="dt-page-btn"
                                :class="{
                                    active: page === currentPage,
                                    dots: page === '...',
                                }"
                                @click="page !== '...' && goToPage(page)"
                                :disabled="page === '...'"
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
            <EditPayrollModal
                v-if="showEditPayrollModal"
                :period="selectedPeriod"
                @closeModal="closeEditPayrollModal"
                @refreshData="fetchPeriods"
            />

            <ImportGaji
                v-if="showImportGajiModal"
                :period="selectedPeriodGaji"
                @closeModal="closeModalImportGaji"
                @refreshData="fetchPeriods"
            />
        </section>
    </AppLayout>
</template>
<script>
import Button from '@/components/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import ImportGaji from '../../../import/ImportGaji.vue';
import EditPayrollModal from './modal-edit-payroll.vue';

export default {
    props: {
        periods: Object, // initial data dari Inertia (opsional)
        filters: Object,
    },

    components: {
        AppLayout,
        Link,
        ImportGaji,
        Button,
        EditPayrollModal,
    },

    data() {
        return {
            search: this.filters?.search || '',
            status: this.filters?.status || '',

            periodsData: [],
            loadingPeriods: false,

            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,
            showFilters: false,
            selectedPeriod: [],
            selectedPeriodGaji: [],
            periodForm: [],

            showEditPayrollModal: false,
            showImportGajiModal: false,

            isDownloadingGaji: false,

            filtered_status: '',
            filtered_tanggal_mulai: '',
            filtered_tanggal_selesai: '',
        };
    },

    watch: {
        search() {
            this.fetchPeriods();
        },
        status() {
            this.fetchPeriods();
        },
    },
    computed: {
        startIndex() {
            if (this.totalItems === 0) return 0;
            return (this.currentPage - 1) * this.perPage;
        },

        endIndex() {
            if (this.totalItems === 0) return 0;
            return Math.min(
                this.startIndex + this.periodsData.length,
                this.totalItems,
            );
        },
        pages() {
            if (this.totalPages <= 1) return [];

            const range = 2;
            const pages = [];

            let start = Math.max(1, this.currentPage - range);
            let end = Math.min(this.totalPages, this.currentPage + range);

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        },
    },
    methods: {
        async fetchPeriods(page = 1) {
            this.loadingPeriods = true;

            try {
                const res = await axios.get('/hr/payroll/all', {
                    params: {
                        search: this.search,
                        status: this.filtered_status,
                        tanggal_mulai: this.filtered_tanggal_mulai,
                        tanggal_selesai: this.filtered_tanggal_selesai,
                        page,
                    },
                });

                // asumsi response Laravel pagination
                this.periodsData = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.perPage = res.data.meta.per_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (error) {
                triggerAlert('error', 'Gagal load periode');
            } finally {
                this.loadingPeriods = false;
            }
        },

        async deletePeriod(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus periode ini?'))
                return;

            try {
                await axios.delete(`/hr/payroll/delete/${id}`);

                triggerAlert('success', 'Periode payroll berhasil dihapus');

                this.$inertia.visit('/hr/payroll');
            } catch (error) {
                console.error(error);
                triggerAlert('error', 'Gagal menghapus periode payroll');
            }
        },

        getStatusClass(status) {
            const classes = {
                open: 'status-open',
                closed: 'status-closed',
                processed: 'status-processed',
            };
            return classes[status] || '';
        },

        getStatusLabel(status) {
            const labels = {
                open: 'Terbuka',
                closed: 'Ditutup',
                processed: 'Diproses',
            };
            return labels[status] || status;
        },

        getMonthName(month) {
            const months = [
                '',
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            ];
            return months[month] || '';
        },
        tambahPeriode() {
            this.$inertia.visit('/hr/payroll/create');
        },

        openPeriodeConfig(period_selected) {
            this.selectedPeriod = period_selected;

            this.showEditPayrollModal = true;
        },

        closeEditPayrollModal() {
            this.showEditPayrollModal = false;
        },

        uploadGaji(period_selected) {
            this.selectedPeriodGaji = period_selected;
            this.showImportGajiModal = true;
        },

        closeModalImportGaji() {
            this.showImportGajiModal = false;
            this.selectedFilePayslip = null;
        },

        async downloadGaji(period) {
            try {
                this.isDownloadingGaji = true;
                const response = await axios.get('/export/payroll', {
                    responseType: 'blob',
                    params: {
                        periode_id: period.id,
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

                const filename = `PERIODE GAJI_${period.judul}_${tgl}${bln}${thn}${hari}${jam}${detik}.xlsx`;

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
                this.isDownloadingGaji = false;
            }
        },

        filteredData() {
            this.fetchPeriods();
        },
    },

    mounted() {
        // jika halaman ini harus selalu load via API
        this.fetchPeriods(this.currentPage);
    },
};
</script>
