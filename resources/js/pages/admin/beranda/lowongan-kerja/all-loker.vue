<template>
    <AppLayout>
        <section class="page employees-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Lowongan Kerja</h2>
                    </div>

                    <p class="page-subtitle">Kelola informasi lowongan kerja</p>
                </div>
                <Button variant="primary" @click="tambahLoker">
                    + Tambah Lowongan Kerja
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
                                    <th style="width: 12%">Kode/Slug</th>
                                    <th>Judul Loker</th>
                                    <th style="width: 12%">Tipe</th>
                                    <th style="width: 14%">Perusahaan</th>
                                    <th style="width: 14%">Penempatan</th>
                                    <th style="width: 10%">Jam Kerja</th>
                                    <th style="width: 12%">Gaji</th>
                                    <th style="width: 9%">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="loadingLokers">
                                    <td colspan="8" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="lokersData.length === 0">
                                    <td colspan="8" class="empty-row">
                                        Tidak ada data ...
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(loker, index) in lokersData"
                                    :key="loker.id"
                                >
                                    <td style="text-align: center">
                                        {{ startIndex + index + 1 }}
                                    </td>

                                    <!-- Slug / Kode -->
                                    <td>
                                        <button
                                            type="button"
                                            class="cell-konfig"
                                            title="Klik untuk edit loker"
                                            @click="openLokerConfig(loker)"
                                        >
                                            <span class="cell-konfig__title">
                                                {{ loker.slug || '-' }}
                                            </span>
                                            <span class="cell-konfig__hint"
                                                >Klik untuk edit loker</span
                                            >
                                        </button>
                                    </td>

                                    <!-- Judul -->
                                    <td>{{ loker.judul || '-' }}</td>

                                    <!-- Tipe -->
                                    <td>{{ loker.tipe_pekerjaan || '-' }}</td>

                                    <!-- Perusahaan -->
                                    <td>{{ loker.perusahaan_nama || '-' }}</td>

                                    <!-- Penempatan -->
                                    <td>{{ loker.penempatan_nama || '-' }}</td>

                                    <!-- Jam Kerja -->
                                    <td>{{ loker.jam_kerja || '-' }}</td>

                                    <!-- Gaji -->
                                    <td>
                                        {{
                                            formatGaji(
                                                loker.gaji_min,
                                                loker.gaji_max,
                                                loker.mata_uang,
                                            )
                                        }}
                                    </td>

                                    <!-- Status -->
                                    <td>
                                        <span
                                            class="status-pill"
                                            :class="getStatusClass(loker.aktif)"
                                        >
                                            {{ getStatusLabel(loker.aktif) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER -->
                    <div
                        class="dt-footer"
                        v-if="!loadingLokers && lokersData.length > 0"
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
            <AddLoker
                v-if="showModalAddLoker"
                @closeModal="closeAddLokerModal"
                @refreshData="fetchLoker"
            />
            <EditLoker
                v-if="showModalEditLoker"
                :loker="selectedLoker"
                @closeModal="closeEditLokerModal"
                @refreshData="fetchLoker"
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
import AddLoker from './add-loker.vue';
import EditLoker from './edit-loker.vue';

export default {
    props: {
        lokers: Object, // initial data dari Inertia (opsional)
        filters: Object,
    },

    components: {
        AppLayout,
        Link,
        Button,
        AddLoker,
        EditLoker,
    },

    data() {
        return {
            search: this.filters?.search || '',
            status: this.filters?.status || '',

            lokersData: [],
            loadingLokers: false,

            currentPage: 1,
            perPage: 10,
            totalItems: 0,
            totalPages: 0,
            showFilters: false,
            selectedLoker: [],
            shiftForm: [],

            showModalAddLoker: false,
            showModalEditLoker: false,
        };
    },

    watch: {
        search() {
            this.fetchLoker();
        },
        status() {
            this.fetchLoker();
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
                this.startIndex + this.lokersData.length,
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
        async fetchLoker(page = 1) {
            this.loadingLokers = true;

            try {
                const res = await axios.get('/beranda/lowongan-kerja/all', {
                    params: {
                        search: this.search,
                        status: this.status,
                        page,
                    },
                });

                // asumsi response Laravel pagination
                this.lokersData = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.perPage = res.data.meta.per_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (error) {
                triggerAlert('error', 'Gagal load shift');
            } finally {
                this.loadingLokers = false;
            }
        },

        formatGaji(min, max, cur = 'IDR') {
            const toRupiah = (n) =>
                new Intl.NumberFormat('id-ID').format(Number(n || 0));

            if (!min && !max) return 'Negosiasi';
            if (min && !max) return `${cur} ${toRupiah(min)}+`;
            if (!min && max) return `${cur} s/d ${toRupiah(max)}`;
            return `${cur} ${toRupiah(min)} - ${toRupiah(max)}`;
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

        tambahLoker() {
            this.showModalAddLoker = true;
        },

        closeAddLokerModal() {
            this.showModalAddLoker = false;
        },

        openLokerConfig(shift_selected) {
            this.selectedLoker = shift_selected;
            this.showModalEditLoker = true;
        },

        closeEditLokerModal() {
            this.showModalEditLoker = false;
        },
    },

    mounted() {
        // jika halaman ini harus selalu load via API
        this.fetchLoker(this.currentPage);
    },
};
</script>
