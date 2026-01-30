<template>
    <AppLayout>
        <section class="page employees-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Surat Peringatan</h2>
                    </div>

                    <p class="page-subtitle">
                        Kelola data surat peringatan karyawan
                    </p>
                </div>

                <Button variant="primary" @click="tambahSP">
                    + Tambah Surat Peringatan
                </Button>
            </div>

            <div class="dt-toolbar-mobile">
                <div class="dt-row-main">
                    <label class="dt-length-compact">
                        Tampil
                        <select v-model.number="perPage" @change="fetchSPs(1)">
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
                            placeholder="Cari nomor SP / nama / NIP / KTP"
                        />
                    </div>
                </div>

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
                            <label>Filter Tingkat</label>
                            <select v-model="tingkat" class="filter-input">
                                <option value="">Semua</option>
                                <option value="SP1">SP1</option>
                                <option value="SP2">SP2</option>
                                <option value="SP3">SP3</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tanggal SP Dari</label>
                            <input
                                type="date"
                                v-model="tanggal_dari"
                                class="filter-input"
                            />
                        </div>

                        <div class="form-group">
                            <label>Tanggal SP Sampai</label>
                            <input
                                type="date"
                                v-model="tanggal_sampai"
                                class="filter-input"
                            />
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-responsive-custom">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-no" style="width: 5%">No</th>
                                    <th>Nomor SP</th>
                                    <th>Tanggal SP</th>
                                    <th>Nama Karyawan</th>
                                    <th>Tingkat</th>
                                    <th>Pelanggaran</th>
                                    <th>Tanggal Kejadian</th>
                                    <th>Tanggal Berakhir</th>
                                    <th>Periode (bulan)</th>
                                    <th>File</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="loadingSP">
                                    <td :colspan="colspan" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="spData.length === 0">
                                    <td :colspan="colspan" class="empty-row">
                                        Tidak ada data ...
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(sp, index) in spData"
                                    :key="sp.id"
                                >
                                    <td style="text-align: center">
                                        {{ startIndex + index + 1 }}
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="cell-konfig"
                                            title="Klik untuk edit surat peringatan"
                                            @click="openSPConfig(sp)"
                                        >
                                            <span class="cell-konfig__title">
                                                {{ sp.nomor_sp || '-' }}
                                            </span>
                                            <span class="cell-konfig__hint">
                                                Klik untuk edit surat peringatan
                                            </span>
                                        </button>
                                    </td>

                                    <td>{{ formatDate(sp.tanggal_sp) }}</td>

                                    <td>
                                        {{
                                            sp.employee?.nama ||
                                            sp.nama_karyawan ||
                                            '-'
                                        }}
                                        -
                                        {{
                                            sp.employee?.no_ktp ||
                                            sp.no_ktp ||
                                            '-'
                                        }}
                                    </td>

                                    <td>{{ sp.tingkat || '-' }}</td>
                                    <td class="wrap">
                                        {{ sp.pelanggaran || '-' }}
                                    </td>
                                    <td>
                                        {{ formatDate(sp.tanggal_kejadian) }}
                                    </td>
                                    <td>
                                        {{ formatDate(sp.tanggal_berakhir) }}
                                    </td>
                                    <td style="text-align: center">
                                        {{ sp.periode_bulan ?? '-' }}
                                    </td>

                                    <td>
                                        <a
                                            v-if="sp.file_url"
                                            :href="sp.file_url"
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

                    <div
                        class="dt-footer"
                        v-if="!loadingSP && spData.length > 0"
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
                            peringatan
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

            <AddSuratPeringatan
                v-if="showModalAddSP"
                @closeModal="closeAddSPModal"
                @refreshData="fetchSPs"
            />

            <EditSuratPeringatan
                v-if="showModalEditSP"
                :surat_peringatan="selectedSP"
                @closeModal="closeEditSPModal"
                @refreshData="fetchSPs"
            />
        </section>
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import axios from 'axios';

import AddSuratPeringatan from './add-surat_peringatan.vue';
import EditSuratPeringatan from './edit-surat_peringatan.vue';

export default {
    props: { filters: Object },

    components: {
        AppLayout,
        Button,
        AddSuratPeringatan,
        EditSuratPeringatan,
    },

    data() {
        return {
            search: this.filters?.search || '',
            tingkat: this.filters?.tingkat || '',
            tanggal_dari: this.filters?.tanggal_dari || '',
            tanggal_sampai: this.filters?.tanggal_sampai || '',

            spData: [],
            loadingSP: false,

            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,

            showFilters: false,
            selectedSP: null,

            showModalAddSP: false,
            showModalEditSP: false,

            _debounceTimer: null,
            _requestSeq: 0,
        };
    },

    watch: {
        // debounce supaya gak spam request tiap ketik / klik filter
        search() {
            this.queueFetch(1);
        },
        tingkat() {
            this.queueFetch(1);
        },
        tanggal_dari() {
            this.queueFetch(1);
        },
        tanggal_sampai() {
            this.queueFetch(1);
        },
    },

    computed: {
        colspan() {
            // No, Nomor, Tgl SP, Nama, Tingkat, Pelanggaran, Tgl Kejadian, Tgl Berakhir, Periode, File
            return 10;
        },
        startIndex() {
            if (this.totalItems === 0) return 0;
            return (this.currentPage - 1) * this.perPage;
        },
        endIndex() {
            if (this.totalItems === 0) return 0;
            return Math.min(
                this.startIndex + this.spData.length,
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
        queueFetch(page = 1) {
            clearTimeout(this._debounceTimer);
            this._debounceTimer = setTimeout(() => {
                this.fetchSPs(page);
            }, 300);
        },

        formatDate(val) {
            if (!val) return '-';
            // kalau backend sudah kirim 'YYYY-MM-DD', biarkan
            if (typeof val === 'string' && /^\d{4}-\d{2}-\d{2}/.test(val))
                return val.slice(0, 10);
            const d = new Date(val);
            if (Number.isNaN(d.getTime())) return String(val);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        },

        async fetchSPs(page = 1) {
            const seq = ++this._requestSeq;
            this.loadingSP = true;

            try {
                const res = await axios.get('/hr/surat-peringatan/all', {
                    params: {
                        search: this.search,
                        tingkat: this.tingkat,
                        tanggal_dari: this.tanggal_dari,
                        tanggal_sampai: this.tanggal_sampai,
                        page,
                        per_page: this.perPage,
                    },
                });

                // race guard: kalau ada request lebih baru, abaikan hasil lama
                if (seq !== this._requestSeq) return;

                this.spData = res.data.data || [];
                this.currentPage = res.data.meta?.current_page ?? 1;
                this.perPage = res.data.meta?.per_page ?? this.perPage;
                this.totalItems = res.data.meta?.total ?? 0;
                this.totalPages = res.data.meta?.last_page ?? 0;
            } catch (error) {
                if (seq === this._requestSeq) {
                    triggerAlert('error', 'Gagal load surat peringatan');
                }
            } finally {
                if (seq === this._requestSeq) {
                    this.loadingSP = false;
                }
            }
        },

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.fetchSPs(page);
        },

        tambahSP() {
            this.showModalAddSP = true;
        },
        closeAddSPModal() {
            this.showModalAddSP = false;
        },

        openSPConfig(sp) {
            this.selectedSP = sp;
            this.showModalEditSP = true;
        },
        closeEditSPModal() {
            this.showModalEditSP = false;
        },
    },

    mounted() {
        this.fetchSPs(this.currentPage);
    },
};
</script>

<style scoped>
.wrap {
    white-space: normal;
    word-break: break-word;
}
</style>
