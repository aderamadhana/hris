<template>
    <AppLayout
        ><section class="page employees-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Pengumuman</h2>
                    </div>

                    <p class="page-subtitle">Kelola informasi pengumuman</p>
                </div>

                <Button variant="primary" @click="tambahPengumuman">
                    + Tambah Pengumuman
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
                            placeholder="Cari judul pengumuman"
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
                        <!-- Filter Status -->
                        <div class="form-group">
                            <label>Status</label>
                            <select v-model="status" class="filter-input">
                                <option value="">Semua Status</option>
                                <option value="true">Aktif</option>
                                <option value="false">Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- Filter Kategori -->
                        <div class="form-group">
                            <label>Kategori</label>
                            <select v-model="kategori" class="filter-input">
                                <option value="">Semua Kategori</option>
                                <option value="Recruitment">Recruitment</option>
                                <option value="Info">Info</option>
                                <option value="Operasional">Operasional</option>
                            </select>
                        </div>

                        <!-- Filter Diutamakan -->
                        <div class="form-group">
                            <label>Diutamakan</label>
                            <select v-model="diutamakan" class="filter-input">
                                <option value="">Semua</option>
                                <option value="true">Diutamakan</option>
                                <option value="false">Normal</option>
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
                                    <th style="width: 14%">Slug</th>
                                    <th>Judul</th>
                                    <th style="width: 12%">Kategori</th>
                                    <th style="width: 20%">Ringkasan</th>
                                    <th style="width: 10%">Urutan</th>
                                    <th style="width: 10%">Diutamakan</th>
                                    <th style="width: 10%">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="loadingPengumuman">
                                    <td colspan="8" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="pengumumanData.length === 0">
                                    <td colspan="8" class="empty-row">
                                        Tidak ada data ...
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(p, index) in pengumumanData"
                                    :key="p.id"
                                >
                                    <td style="text-align: center">
                                        {{ startIndex + index + 1 }}
                                    </td>

                                    <!-- Slug -->
                                    <td>
                                        <button
                                            type="button"
                                            class="cell-konfig"
                                            title="Klik untuk edit pengumuman"
                                            @click="openPengumumanConfig(p)"
                                        >
                                            <span class="cell-konfig__title">
                                                {{ p.slug || '-' }}
                                            </span>
                                            <span class="cell-konfig__hint">
                                                Klik untuk edit pengumuman
                                            </span>
                                        </button>
                                    </td>

                                    <!-- Judul -->
                                    <td>{{ p.judul || '-' }}</td>

                                    <!-- Kategori -->
                                    <td>{{ p.kategori || '-' }}</td>

                                    <!-- Ringkasan -->
                                    <td>{{ p.ringkasan || '-' }}</td>

                                    <!-- Urutan -->
                                    <td style="text-align: center">
                                        {{ p.urutan ?? 0 }}
                                    </td>

                                    <!-- Diutamakan -->
                                    <td>
                                        <span
                                            class="status-pill"
                                            :class="
                                                p.diutamakan
                                                    ? 'status-warning'
                                                    : 'status-muted'
                                            "
                                        >
                                            {{ p.diutamakan ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td>
                                        <span
                                            class="status-pill"
                                            :class="getStatusClass(p.aktif)"
                                        >
                                            {{ getStatusLabel(p.aktif) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER -->
                    <div
                        class="dt-footer"
                        v-if="!loadingPengumuman && pengumumanData.length > 0"
                    >
                        <div class="dt-info">
                            Menampilkan
                            <strong v-if="totalItems">{{
                                startIndex + 1
                            }}</strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> pengumuman
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

            <!-- MODAL -->
            <AddPengumuman
                v-if="showModalAddPengumuman"
                @closeModal="closeAddPengumumanModal"
                @refreshData="fetchPengumuman"
            />

            <EditPengumuman
                v-if="showModalEditPengumuman"
                :pengumuman="selectedPengumuman"
                @closeModal="closeEditPengumumanModal"
                @refreshData="fetchPengumuman"
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
import AddPengumuman from './add-pengumuman.vue';
import EditPengumuman from './edit-pengumuman.vue';

export default {
    props: {
        pengumumans: Object, // optional initial inertia data
        filters: Object,
    },

    components: {
        AppLayout,
        Link,
        Button,
        AddPengumuman,
        EditPengumuman,
    },

    data() {
        return {
            // filters
            search: this.filters?.search || '',
            status: this.filters?.status || '',
            kategori: this.filters?.kategori || '',
            diutamakan: this.filters?.diutamakan || '',

            // data table
            pengumumanData: [],
            loadingPengumuman: false,

            // pagination
            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,

            // ui
            showFilters: false,
            selectedPengumuman: null,

            // modal
            showModalAddPengumuman: false,
            showModalEditPengumuman: false,

            // debounce search biar ga spam API
            _searchTimer: null,
        };
    },

    watch: {
        search() {
            this.debouncedFetch();
        },
        status() {
            this.fetchPengumuman(1);
        },
        kategori() {
            this.fetchPengumuman(1);
        },
        diutamakan() {
            this.fetchPengumuman(1);
        },
        perPage() {
            this.fetchPengumuman(1);
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
                this.startIndex + this.pengumumanData.length,
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
        debouncedFetch() {
            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(() => {
                this.fetchPengumuman(1);
            }, 300);
        },

        // convert "true"/"false"/boolean => 1/0 untuk backend
        normalizeBoolParam(v) {
            if (v === '' || v === null || typeof v === 'undefined') return '';

            if (v === true || v === 'true' || v === 1 || v === '1') return 1;
            if (v === false || v === 'false' || v === 0 || v === '0') return 0;

            return v;
        },

        async fetchPengumuman(page = 1) {
            this.loadingPengumuman = true;

            try {
                const res = await axios.get('/beranda/pengumuman/all', {
                    params: {
                        search: this.search,
                        status: this.normalizeBoolParam(this.status),
                        kategori: this.kategori,
                        diutamakan: this.normalizeBoolParam(this.diutamakan),
                        page,
                        per_page: this.perPage,
                    },
                });

                this.pengumumanData = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.perPage = res.data.meta.per_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (error) {
                triggerAlert('error', 'Gagal memuat pengumuman');
            } finally {
                this.loadingPengumuman = false;
            }
        },

        goToPage(page) {
            if (page < 1) return;
            if (page > this.totalPages) return;
            if (page === this.currentPage) return;
            this.fetchPengumuman(page);
        },

        getStatusClass(isActive) {
            return isActive ? 'status-active' : 'status-inactive';
        },

        getStatusLabel(status) {
            // handle boolean / 1/0 / string
            if (status === true || status === 1 || status === '1')
                return 'Aktif';
            if (status === false || status === 0 || status === '0')
                return 'Tidak Aktif';
            return status ?? '-';
        },

        tambahPengumuman() {
            this.showModalAddPengumuman = true;
        },

        closeAddPengumumanModal() {
            this.showModalAddPengumuman = false;
        },

        openPengumumanConfig(pengumumanSelected) {
            this.selectedPengumuman = pengumumanSelected;
            this.showModalEditPengumuman = true;
        },

        closeEditPengumumanModal() {
            this.showModalEditPengumuman = false;
        },
    },

    mounted() {
        this.fetchPengumuman(this.currentPage);
    },
};
</script>
