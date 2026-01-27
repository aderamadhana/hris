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
                            placeholder="Cari judul loker"
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
                        <!-- Status -->
                        <div class="form-group">
                            <label>Status</label>
                            <!-- ✅ ganti value jadi aktif/nonaktif (match backend) -->
                            <select v-model="status" class="filter-input">
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- Publish -->
                        <!-- <div class="form-group">
                            <label>Publish</label>
                            <select v-model="publish" class="filter-input">
                                <option value="">Semua</option>
                                <option value="published">Published</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div> -->

                        <!-- Tipe Pekerjaan -->
                        <!-- <div class="form-group">
                            <label>Tipe</label>
                            <select
                                v-model="tipe_pekerjaan"
                                class="filter-input"
                            >
                                <option value="">Semua</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div> -->

                        <!-- Jam Kerja -->
                        <!-- <div class="form-group">
                            <label>Jam Kerja</label>
                            <select v-model="jam_kerja" class="filter-input">
                                <option value="">Semua</option>
                                <option value="Shift">Shift</option>
                                <option value="Office">Office</option>
                                <option value="Day">Day</option>
                                <option value="Night">Night</option>
                            </select>
                        </div> -->

                        <!-- Perusahaan -->
                        <div class="form-group">
                            <label>Perusahaan</label>
                            <Select2
                                v-model="perusahaan_id"
                                class="filter-input"
                            >
                                <option value="">Semua</option>
                                <option
                                    v-for="p in data_perusahaan"
                                    :key="p.id"
                                    :value="p.id"
                                >
                                    {{ p.nama_perusahaan }}
                                </option>
                            </Select2>
                        </div>

                        <!-- Penempatan -->
                        <div class="form-group">
                            <label>Penempatan</label>
                            <Select2
                                v-model="penempatan_id"
                                class="filter-input"
                                :disabled="!perusahaan_id"
                            >
                                <option value="">Semua</option>
                                <option
                                    v-for="d in data_penempatan"
                                    :key="d.id"
                                    :value="d.id"
                                >
                                    {{ d.nama_divisi }}
                                </option>
                            </Select2>
                        </div>

                        <!-- Reset -->
                        <!-- <div class="form-group" style="grid-column: 1 / -1">
                            <button
                                type="button"
                                class="btn btn-outline-secondary w-100"
                                @click="resetFilters"
                            >
                                Reset Filter
                            </button>
                        </div> -->
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
                                    <!-- ✅ colspan harus 9 -->
                                    <td colspan="9" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text">
                                                Memuat data...
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="lokersData.length === 0">
                                    <!-- ✅ colspan harus 9 -->
                                    <td colspan="9" class="empty-row">
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
                                            <span class="cell-konfig__hint">
                                                Klik untuk edit loker
                                            </span>
                                        </button>
                                    </td>

                                    <td>{{ loker.judul || '-' }}</td>
                                    <td>{{ loker.tipe_pekerjaan || '-' }}</td>
                                    <td>{{ loker.perusahaan_nama || '-' }}</td>
                                    <td>{{ loker.penempatan_nama || '-' }}</td>
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
                            <strong v-if="totalItems">
                                {{ startIndex + 1 }}
                            </strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> loker
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
import Select2 from '@/components/Select2.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import AddLoker from './add-loker.vue';
import EditLoker from './edit-loker.vue';

export default {
    props: {
        lokers: Object,
        filters: Object,
    },

    components: {
        AppLayout,
        Link,
        Button,
        AddLoker,
        EditLoker,
        Select2,
    },

    data() {
        return {
            // search + filter
            search: this.filters?.search || '',
            status: this.filters?.status || '',
            publish: this.filters?.publish || '',
            tipe_pekerjaan: this.filters?.tipe_pekerjaan || '',
            jam_kerja: this.filters?.jam_kerja || '',
            perusahaan_id: this.filters?.perusahaan_id || '',
            penempatan_id: this.filters?.penempatan_id || '',

            // data table
            lokersData: [],
            loadingLokers: false,

            // pagination
            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,

            // ui
            showFilters: false,
            selectedLoker: null,
            showModalAddLoker: false,
            showModalEditLoker: false,

            // options perusahaan / divisi
            data_perusahaan: [],
            data_penempatan: [],

            // debounce timer
            _searchTimer: null,
        };
    },

    watch: {
        search() {
            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(() => {
                this.fetchLoker(1);
            }, 300);
        },

        status() {
            this.fetchLoker(1);
        },
        publish() {
            this.fetchLoker(1);
        },
        tipe_pekerjaan() {
            this.fetchLoker(1);
        },
        jam_kerja() {
            this.fetchLoker(1);
        },
        perPage() {
            this.fetchLoker(1);
        },

        perusahaan_id() {
            this.penempatan_id = '';
            this.buildPenempatanOptions();
            this.fetchLoker(1);
        },

        penempatan_id() {
            this.fetchLoker(1);
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

            const start = Math.max(1, this.currentPage - range);
            const end = Math.min(this.totalPages, this.currentPage + range);

            for (let i = start; i <= end; i++) pages.push(i);
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
                        status: this.status, // ✅ sekarang "aktif"/"nonaktif"
                        publish: this.publish,
                        tipe_pekerjaan: this.tipe_pekerjaan,
                        jam_kerja: this.jam_kerja,
                        perusahaan_id: this.perusahaan_id,
                        penempatan_id: this.penempatan_id,
                        page,
                        per_page: this.perPage,
                    },
                });

                this.lokersData = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.perPage = res.data.meta.per_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (error) {
                triggerAlert('error', 'Gagal load loker');
            } finally {
                this.loadingLokers = false;
            }
        },

        goToPage(page) {
            if (page < 1) return;
            if (page > this.totalPages) return;
            if (page === this.currentPage) return;
            this.fetchLoker(page);
        },

        async getFilteredPerusahaanDanJabatan() {
            try {
                const res = await axios.get('/referensi/perusahaan-divisi');
                this.data_perusahaan = res.data.data || [];
                this.buildPenempatanOptions();
            } catch (err) {
                triggerAlert('error', 'Gagal memuat perusahaan/divisi');
            }
        },

        buildPenempatanOptions() {
            if (!this.perusahaan_id) {
                this.data_penempatan = [];
                return;
            }

            const p = this.data_perusahaan.find(
                (x) => String(x.id) === String(this.perusahaan_id),
            );

            if (!p || !Array.isArray(p.divisi)) {
                this.data_penempatan = [];
                return;
            }

            // ambil divisi aktif saja (kalau data kamu pakai status)
            this.data_penempatan = p.divisi.filter(
                (d) =>
                    d.status === 'aktif' || d.status === 1 || d.status === true,
            );
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
            // handle boolean / 1/0
            if (status === true || status === 1 || status === '1')
                return 'Aktif';
            if (status === false || status === 0 || status === '0')
                return 'Tidak Aktif';
            return status ?? '-';
        },

        resetFilters() {
            this.search = '';
            this.status = '';
            this.publish = '';
            this.tipe_pekerjaan = '';
            this.jam_kerja = '';
            this.perusahaan_id = '';
            this.penempatan_id = '';
            this.fetchLoker(1);
        },

        tambahLoker() {
            this.showModalAddLoker = true;
        },

        closeAddLokerModal() {
            this.showModalAddLoker = false;
        },

        openLokerConfig(lokerSelected) {
            this.selectedLoker = lokerSelected;
            this.showModalEditLoker = true;
        },

        closeEditLokerModal() {
            this.showModalEditLoker = false;
        },
    },

    mounted() {
        this.getFilteredPerusahaanDanJabatan();
        this.fetchLoker(this.currentPage);
    },
};
</script>
