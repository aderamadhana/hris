<template>
    <AppLayout>
        <section class="page employees-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Shift</h2>
                    </div>

                    <p class="page-subtitle">
                        Kelola informasi shift untuk karyawan
                    </p>
                </div>
                <Button variant="primary" @click="tambahShift">
                    + Tambah Shift
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
                            placeholder="Cari shift"
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
                                <option value="true">Aktif</option>
                                <option value="false">Tidak Aktif</option>
                            </select>
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
                                    <th>Kode Shift</th>
                                    <th>Nama Shift</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Toleransi Keterlambatan</th>
                                    <th>Durasi Kerja</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="loadingShifts">
                                    <td colspan="8" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="shiftsData.length === 0">
                                    <td colspan="8" class="empty-row">
                                        Tidak ada data ...
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(shift, index) in shiftsData"
                                    :key="shift.id"
                                >
                                    <td style="text-align: center">
                                        {{ startIndex + index + 1 }}
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="cell-konfig"
                                            title="Klik untuk edit shift"
                                            @click="openShiftConfig(shift)"
                                        >
                                            <span class="cell-konfig__title">{{
                                                shift.kode_shift || '-'
                                            }}</span>
                                            <span class="cell-konfig__hint"
                                                >Klik untuk edit shift</span
                                            >
                                        </button>
                                    </td>
                                    <td>{{ shift.nama_shift }}</td>
                                    <td>{{ shift.jam_masuk }}</td>
                                    <td>{{ shift.jam_pulang }}</td>
                                    <td>
                                        {{ shift.toleransi_keterlambatan }}
                                        menit
                                    </td>
                                    <td>{{ shift.durasi_kerja }} menit</td>
                                    <td>{{ shift.keterangan }}</td>
                                    <td>
                                        <span
                                            class="status-pill"
                                            :class="
                                                getStatusClass(shift.is_active)
                                            "
                                        >
                                            {{
                                                getStatusLabel(shift.is_active)
                                            }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER -->
                    <div
                        class="dt-footer"
                        v-if="!loadingShifts && shiftsData.length > 0"
                    >
                        <div class="dt-info">
                            Menampilkan
                            <strong v-if="totalItems">{{
                                startIndex + 1
                            }}</strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> shift
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
            <AddShift
                v-if="showModalAddShift"
                @closeModal="closeAddshiftModal"
                @refreshData="fetchShifts"
            />
            <EditShift
                v-if="showModalEditShift"
                :shift="selectedShift"
                @closeModal="closeEditshiftModal"
                @refreshData="fetchShifts"
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
import AddShift from './add-shift.vue';
import EditShift from './edit-shift.vue';

export default {
    props: {
        shifts: Object, // initial data dari Inertia (opsional)
        filters: Object,
    },

    components: {
        AppLayout,
        Link,
        Button,
        AddShift,
        EditShift,
    },

    data() {
        return {
            search: this.filters?.search || '',
            status: this.filters?.status || '',

            shiftsData: [],
            loadingShifts: false,

            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,
            showFilters: false,
            selectedShift: [],
            shiftForm: [],

            showModalAddShift: false,
            showModalEditShift: false,
        };
    },

    watch: {
        search() {
            this.fetchShifts();
        },
        status() {
            this.fetchShifts();
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
                this.startIndex + this.shiftsData.length,
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
        async fetchShifts(page = 1) {
            this.loadingShifts = true;

            try {
                const res = await axios.get('/hr/shift/all', {
                    params: {
                        search: this.search,
                        status: this.status,
                        page,
                    },
                });

                // asumsi response Laravel pagination
                this.shiftsData = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.perPage = res.data.meta.per_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (error) {
                triggerAlert('error', 'Gagal load shift');
            } finally {
                this.loadingShifts = false;
            }
        },

        getStatusClass(isActive) {
            return isActive ? 'status-active' : 'status-inactive';
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

        tambahShift() {
            this.showModalAddShift = true;
        },

        closeAddshiftModal() {
            this.showModalAddShift = false;
        },

        openShiftConfig(shift_selected) {
            this.selectedShift = shift_selected;
            this.showModalEditShift = true;
        },

        closeEditshiftModal() {
            this.showModalEditShift = false;
        },
    },

    mounted() {
        // jika halaman ini harus selalu load via API
        this.fetchShifts(this.currentPage);
    },
};
</script>
