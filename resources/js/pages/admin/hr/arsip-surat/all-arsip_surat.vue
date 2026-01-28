<template>
    <AppLayout>
        <section class="page employees-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Arsip Surat</h2>
                    </div>

                    <p class="page-subtitle">
                        Kelola arsip surat (nomor, tanggal, perihal, file)
                    </p>
                </div>

                <Button variant="primary" @click="tambahSurat">
                    + Tambah Surat
                </Button>
            </div>

            <div class="dt-toolbar-mobile">
                <!-- Row 1: Length & Search -->
                <div class="dt-row-main">
                    <label class="dt-length-compact">
                        Tampil
                        <select
                            v-model.number="perPage"
                            @change="fetchSurats(1)"
                        >
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
                            placeholder="Cari nomor surat / perihal"
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
                            <label>Tanggal Dari</label>
                            <input
                                type="date"
                                v-model="tanggal_dari"
                                class="filter-input"
                            />
                        </div>

                        <div class="form-group">
                            <label>Tanggal Sampai</label>
                            <input
                                type="date"
                                v-model="tanggal_sampai"
                                class="filter-input"
                            />
                        </div>

                        <!-- <div class="form-group" style="align-self: end">
                            <Button variant="secondary" @click="resetFilter">
                                Reset
                            </Button>
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
                                    <th>Nomor Surat</th>
                                    <th>Tanggal Surat</th>
                                    <th>Perihal</th>
                                    <th>File</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="loadingSurat">
                                    <td colspan="6" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="suratData.length === 0">
                                    <td colspan="6" class="empty-row">
                                        Tidak ada data ...
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(surat, index) in suratData"
                                    :key="surat.id"
                                >
                                    <td style="text-align: center">
                                        {{ startIndex + index + 1 }}
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="cell-konfig"
                                            title="Klik untuk edit surat"
                                            @click="openSuratConfig(surat)"
                                        >
                                            <span class="cell-konfig__title">
                                                {{ surat.nomor_surat || '-' }}
                                            </span>
                                            <span class="cell-konfig__hint">
                                                Klik untuk edit surat
                                            </span>
                                        </button>
                                    </td>

                                    <td>{{ surat.tanggal_surat }}</td>
                                    <td>{{ surat.perihal }}</td>

                                    <td>
                                        <a
                                            v-if="surat.file_url"
                                            :href="surat.file_url"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            Lihat File
                                        </a>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER -->
                    <div
                        class="dt-footer"
                        v-if="!loadingSurat && suratData.length > 0"
                    >
                        <div class="dt-info">
                            Menampilkan
                            <strong v-if="totalItems">{{
                                startIndex + 1
                            }}</strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> surat
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

            <!-- Modal -->
            <AddSurat
                v-if="showModalAddSurat"
                @closeModal="closeAddSuratModal"
                @refreshData="fetchSurats"
            />
            <EditSurat
                v-if="showModalEditSurat"
                :surat="selectedSurat"
                @closeModal="closeEditSuratModal"
                @refreshData="fetchSurats"
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

// ganti sesuai file kamu
import AddSurat from './add-arsip_surat.vue';
import EditSurat from './edit-arsip_surat.vue';

export default {
    props: {
        surats: Object, // opsional
        filters: Object,
    },

    components: {
        AppLayout,
        Link,
        Button,
        AddSurat,
        EditSurat,
    },

    data() {
        return {
            search: this.filters?.search || '',
            tanggal_dari: this.filters?.tanggal_dari || '',
            tanggal_sampai: this.filters?.tanggal_sampai || '',

            suratData: [],
            loadingSurat: false,

            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,

            showFilters: false,

            selectedSurat: null,
            showModalAddSurat: false,
            showModalEditSurat: false,
        };
    },

    watch: {
        search() {
            this.fetchSurats(1);
        },
        tanggal_dari() {
            this.fetchSurats(1);
        },
        tanggal_sampai() {
            this.fetchSurats(1);
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
                this.startIndex + this.suratData.length,
                this.totalItems,
            );
        },
        pages() {
            if (this.totalPages <= 1) return [];
            const range = 2;
            const pages = [];

            let start = Math.max(1, this.currentPage - range);
            let end = Math.min(this.totalPages, this.currentPage + range);

            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },
    },

    methods: {
        async fetchSurats(page = 1) {
            this.loadingSurat = true;

            try {
                // GANTI endpoint sesuai route kamu
                const res = await axios.get('/hr/arsip-surat/all', {
                    params: {
                        search: this.search,
                        tanggal_dari: this.tanggal_dari,
                        tanggal_sampai: this.tanggal_sampai,
                        page,
                        per_page: this.perPage,
                    },
                });

                this.suratData = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.perPage = res.data.meta.per_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (error) {
                triggerAlert('error', 'Gagal load data surat');
            } finally {
                this.loadingSurat = false;
            }
        },

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.fetchSurats(page);
        },

        resetFilter() {
            this.tanggal_dari = '';
            this.tanggal_sampai = '';
            this.fetchSurats(1);
        },

        tambahSurat() {
            this.showModalAddSurat = true;
        },
        closeAddSuratModal() {
            this.showModalAddSurat = false;
        },

        openSuratConfig(surat) {
            this.selectedSurat = surat;
            this.showModalEditSurat = true;
        },
        closeEditSuratModal() {
            this.showModalEditSurat = false;
        },

        async hapusSurat(id) {
            try {
                await axios.delete(`/hr/arsip-surat/surat/${id}`);
                triggerAlert('success', 'Surat berhasil dihapus');
                this.fetchSurats(this.currentPage);
            } catch (e) {
                triggerAlert('error', 'Gagal menghapus surat');
            }
        },
    },

    mounted() {
        this.fetchSurats(this.currentPage);
    },
};
</script>
