<template>
    <AppLayout>
        <section class="page employees-page">
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
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="25">25</option>
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
                            <select v-model="status" class="filter-input">
                                <option value="">Semua Status</option>
                                <option value="open">Terbuka</option>
                                <option value="closed">Ditutup</option>
                                <option value="processed">Diproses</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="table-card">
                    <div
                        class="table-hint"
                        role="note"
                        aria-label="Petunjuk tabel periode"
                    >
                        <div class="table-hint__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="2"
                                />
                                <path
                                    d="M12 16V12"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                                <circle
                                    cx="12"
                                    cy="8"
                                    r="1"
                                    fill="currentColor"
                                />
                            </svg>
                        </div>

                        <div class="table-hint__body">
                            <div class="table-hint__title">
                                Kelola periode
                                <span class="table-hint__badge">Tips</span>
                            </div>
                            <div class="table-hint__text">
                                Klik <b>Judul Periode</b> untuk <b>Edit</b>. dan
                                <b>Hapus</b> periode.
                            </div>
                        </div>

                        <div class="table-hint__actions"></div>
                    </div>

                    <div class="table-responsive-custom">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-no">No</th>
                                    <th style="width: 20%">Judul Periode</th>
                                    <th>Tahun</th>
                                    <th>Bulan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
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
                                    <td>{{ startIndex + index + 1 }}</td>
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
                                                @click="uploadGaji(period.id)"
                                                variant="success"
                                                title="Upload Gaji"
                                            >
                                                <font-awesome-icon
                                                    icon="upload"
                                                />
                                                Upload Gaji
                                            </Button>

                                            <Button
                                                @click="downloadGaji(period.id)"
                                                variant="primary"
                                                title="Download Gaji"
                                            >
                                                <font-awesome-icon
                                                    icon="download"
                                                />
                                                Download Gaji
                                            </Button>
                                        </div>
                                        <!-- <Link
                                            :href="`/hr/payroll/${period.id}/edit`"
                                            class="action-btn emoji primary"
                                            title="Edit"
                                        >
                                            <font-awesome-icon
                                                icon="pen-to-square"
                                            />
                                        </Link>

                                        <button
                                            @click="deletePeriod(period.id)"
                                            class="action-btn emoji danger"
                                            title="Hapus"
                                        >
                                            <font-awesome-icon icon="trash" />
                                        </button> -->
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
                @closeModal="closeEditPayrollModal"
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

export default {
    props: {
        periods: Object, // initial data dari Inertia (opsional)
        filters: Object,
    },

    components: {
        AppLayout,
        Link,
        Button,
    },

    data() {
        return {
            search: this.filters?.search || '',
            status: this.filters?.status || '',

            periodsData: [],
            loadingPeriods: false,

            currentPage: 1,
            perPage: 10,
            totalItems: 0,
            totalPages: 0,
            showFilters: false,
            selectedPeriod: false,
            periodForm: [],

            showEditPayrollModal: false,
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
        openPeriodeConfig(period_selected) {
            this.selectedPeriod = period_selected;

            this.periodForm = {
                // shift_id: u.shift_id || '', // kalau list karyawan sudah bawa shift_id
            };

            this.showEditPayrollModal = true;
        },
        closeEditPayrollModal() {
            this.showEditPayrollModal = false;
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
                        status: this.status,
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
    },

    mounted() {
        // jika halaman ini harus selalu load via API
        this.fetchPeriods(this.currentPage);
    },
};
</script>
