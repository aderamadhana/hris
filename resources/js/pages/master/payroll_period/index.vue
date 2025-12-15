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
            <div class="dt-toolbar">
                <div class="dt-length">
                    <label>
                        Tampil
                        <select v-model.number="perPage">
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                        </select>
                        data
                    </label>
                </div>

                <div class="dt-search">
                    <label>
                        Cari:
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Cari periode"
                        />
                    </label>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="filter-bar">
                        <div class="filter-right">
                            <label for="">Filter Status</label>
                            <select v-model="status" class="filter-input">
                                <option value="">Semua Status</option>
                                <option value="open">Terbuka</option>
                                <option value="closed">Ditutup</option>
                                <option value="processed">Diproses</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Periode</th>
                                <th>Tahun</th>
                                <th>Bulan</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
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
                                <td>{{ period.judul || '-' }}</td>
                                <td>{{ period.year }}</td>
                                <td>{{ getMonthName(period.month) }}</td>
                                <td>{{ period.start_date }}</td>
                                <td>{{ period.end_date }}</td>
                                <td>
                                    <span
                                        class="status-pill"
                                        :class="getStatusClass(period.status)"
                                    >
                                        {{ getStatusLabel(period.status) }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <Link
                                        :href="`/master/payroll-period/${period.id}/edit`"
                                        class="action-btn emoji primary"
                                        title="Edit"
                                    >
                                        ✏️
                                    </Link>
                                    <button
                                        @click="deletePeriod(period.id)"
                                        class="action-btn emoji danger"
                                        title="Hapus"
                                    >
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="dt-footer"
                    v-if="!loadingPeriods && periodsData.length > 0"
                >
                    <div class="dt-info">
                        Menampilkan
                        <strong v-if="totalItems">{{ startIndex + 1 }}</strong>
                        <strong v-else>0</strong>
                        &nbsp;–&nbsp;
                        <strong>{{ endIndex }}</strong>
                        dari
                        <strong>{{ totalItems }}</strong>
                        periode
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
                                currentPage === totalPages || totalPages === 0
                            "
                            @click="goToPage(currentPage + 1)"
                        >
                            »
                        </button>
                    </div>
                </div>
            </div>
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
                const res = await axios.get('/master/payroll-period', {
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
                await axios.delete(`/master/payroll-period/delete/${id}`);

                triggerAlert('success', 'Periode payroll berhasil dihapus');

                this.$inertia.visit('/master/payroll-period/all-data');
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
            this.$inertia.visit('/master/payroll-period/create');
        },
    },

    mounted() {
        // jika halaman ini harus selalu load via API
        this.fetchPeriods(this.currentPage);
    },
};
</script>
