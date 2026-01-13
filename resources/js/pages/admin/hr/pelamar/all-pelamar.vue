<template>
    <AppLayout>
        <div v-if="isLoadingHapus" class="fullpage-loader">
            <div class="fullpage-loader__card">
                <div class="fullpage-loader__spinner"></div>
                <div class="fullpage-loader__title">
                    Loading hapus pelamar...
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
                        <h2 class="page-title">Data Pelamar</h2>
                    </div>
                    <p class="page-subtitle">Kelola informasi pelamar</p>
                </div>

                <div class="page-actions">
                    <Button variant="primary" @click="tambahPelamar">
                        <font-awesome-icon icon="plus" class="icon" />
                        Tambah Pelamar
                    </Button>
                </div>
            </div>

            <!-- RINGKASAN -->
            <div class="overview-row">
                <div class="overview-card primary">
                    <div class="card-icon">
                        <font-awesome-icon icon="users" />
                    </div>
                    <div class="overview-label">Total Pelamar</div>
                    <div class="overview-value">
                        {{ statistics.total_applicants }}
                    </div>
                </div>
                <div class="overview-card warning">
                    <div class="card-icon warning">
                        <font-awesome-icon icon="users" />
                    </div>
                    <div class="overview-label">Butuh Perpanjang Kontrak</div>
                    <div class="overview-value">
                        {{ statistics.total_needs_renewal }}
                    </div>
                </div>
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
                            placeholder="Cari Nama, NIK, atau jabatan..."
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
                            <label>Kategori</label>
                            <select v-model="kategori" class="filter-input">
                                <option value="">Semua Kategori</option>
                                <option value="needs_renewal">
                                    Butuh Perpanjang Kontrak
                                </option>
                                <option value="applicant">Data Pelamar</option>
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
                                    <th class="col-no">#</th>
                                    <th class="col-name" style="width: 30%">
                                        Nama
                                    </th>
                                    <th class="col-nik">NIK</th>
                                    <th class="col-status">Tanggal Melamar</th>
                                    <th class="col-status">Status</th>
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
                                        <div class="cell-user">
                                            <div class="cell-avatar">
                                                {{ (u.name || '').charAt(0) }}
                                            </div>
                                            <div class="cell-user-text">
                                                <div class="cell-name">
                                                    {{ u.name }}
                                                </div>
                                                <div class="cell-dept">
                                                    NRP: {{ u.nrp }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="cell-nik">{{
                                            u.nik
                                        }}</span>
                                    </td>

                                    <!-- Pastikan field ini memang ada di data pelamar -->
                                    <td>
                                        {{ u.tgl_daftar ? u.tgl_daftar : '-' }}
                                    </td>

                                    <td>
                                        <span
                                            class="status-pill"
                                            :class="{
                                                'status-active':
                                                    u.category === 'applicant',
                                                'status-inactive':
                                                    u.category !== 'applicant',
                                            }"
                                        >
                                            {{ u.category_label }}
                                        </span>
                                    </td>

                                    <td class="col-actions">
                                        <div class="actions-wrap">
                                            <button
                                                class="action-btn warning"
                                                title="Edit Data"
                                                @click="openEdit(u.id)"
                                            >
                                                <font-awesome-icon
                                                    icon="edit"
                                                />
                                            </button>
                                            <button
                                                class="action-btn danger"
                                                title="Hapus Data"
                                                @click="deleteUser(u)"
                                            >
                                                <font-awesome-icon
                                                    icon="trash"
                                                />
                                            </button>
                                            <button
                                                class="action-btn primary"
                                                title="Detail Data"
                                                @click="openDetail(u.id)"
                                            >
                                                <font-awesome-icon icon="eye" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER -->
                    <div class="dt-footer" v-if="!loadingUsers">
                        <div class="dt-info">
                            Menampilkan
                            <strong>{{
                                totalItems ? startIndex + 1 : 0
                            }}</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> pelamar
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
        </section>
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import DropdownButton from '@/components/DropdownButton.vue';
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
    },
    data() {
        return {
            users: [],
            loadingUsers: false,

            currentPage: 1,
            perPage: 10,
            totalItems: 0,
            totalPages: 0,

            search: '',
            kategori: '',

            showImportGajiModal: false,
            showImportKaryawanModal: false,

            showFilters: false,
            statistics: [],
            isLoadingHapus: false,
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

            let start = Math.max(1, this.currentPage - range);
            let end = Math.min(this.totalPages, this.currentPage + range);

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        },
    },

    watch: {
        search() {
            this.currentPage = 1;
            this.fetchEmployees(1);
        },
        perPage(newVal, oldVal) {
            if (newVal === oldVal) return;

            this.currentPage = 1;
            this.fetchEmployees(1);
        },
        kategori() {
            this.fetchEmployees(1);
        },
    },
    created() {
        this.debouncedFetch = this.debounce(() => {
            this.fetchEmployees(1);
        }, 400);

        this.fetchEmployees(1);
    },
    // mounted() {
    //     this.fetchEmployees();
    // },
    search: {
        handler() {
            this.currentPage = 1;
            this.debouncedFetch();
        },
    },
    methods: {
        debounce(fn, delay) {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn.apply(this, args), delay);
            };
        },
        showSuccess() {
            this.triggerAlertHtml('success', '<b>Berhasil</b>', 3000);
        },
        fiturBelumTersedia() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },
        async fetchEmployees(page = this.currentPage) {
            this.loadingUsers = true;

            var needs_renewal = false;
            var is_applicant = false;

            if (this.kategori == 'needs_renewal') {
                needs_renewal = true;
            } else if (this.kategori == 'applicant') {
                is_applicant = true;
            }

            try {
                const res = await axios.get('/hr/pelamar/all', {
                    params: {
                        page,
                        per_page: this.perPage,
                        search: this.search,
                        status_active: 0,
                        needs_renewal: needs_renewal,
                        is_applicant: is_applicant,
                    },
                });

                this.users = res.data.data;
                this.statistics = res.data.statistics;
                this.currentPage = res.data.meta.current_page;
                this.perPage = res.data.meta.per_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memuat data karyawan.');
            } finally {
                this.loadingUsers = false;
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

        openDetail(u) {
            router.visit(`/employee/detail_pelamar/${u}`);
        },

        openEdit(id) {
            router.visit(`/hr/pelamar/edit-pelamar/${id}`);
        },
        editUser(u) {
            alert(`Edit: ${u.name}`);
        },
        deleteUser(u) {
            if (!confirm(`Hapus ${u.name}?`)) return;
            this.isLoadingHapus = true;

            router.post(
                `/hr/pelamar/delete/${u.id}`,
                {},
                {
                    onSuccess: () => {
                        triggerAlert('success', 'Berhasil hapus data.');
                        this.fetchEmployees(1);
                        this.isLoadingHapus = false;
                    },
                },
            );
        },
        tambahPelamar() {
            this.$inertia.visit('/hr/pelamar/tambah-pelamar');
        },
    },
};
</script>
