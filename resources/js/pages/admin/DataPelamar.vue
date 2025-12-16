<template>
    <AppLayout>
        <section class="page employees-page">
            <!-- HEADER + ACTIONS -->
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Data Pelamar</h2>
                        <!-- <span class="page-chip">Master Data</span> -->
                    </div>
                    <p class="page-subtitle">Kelola informasi pelamar</p>
                </div>

                <div class="page-actions">
                    <Button variant="primary" @click="fiturBelumTersedia">
                        ➕ Tambah Pelamar
                    </Button>
                </div>
            </div>

            <!-- RINGKASAN DI ATAS TABEL -->
            <div class="overview-row">
                <div class="overview-card primary">
                    <div class="overview-label">Total Pelamar</div>
                    <div class="overview-value">{{ totalItems }}</div>
                </div>
                <!-- <div class="overview-card neutral">
                    <div class="overview-label">Kontrak Hampir Habis</div>
                    <div class="overview-value">0</div>
                </div> -->
            </div>

            <!-- KONTROL DATATABLE -->
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
                            placeholder="Nama, NIK, atau jabatan..."
                        />
                    </label>
                </div>
            </div>

            <!-- TABEL DALAM CARD -->
            <div class="table-card">
                <div class="table-responsive-custom">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="col-no">#</th>
                                <th class="col-name">Nama</th>
                                <th class="col-nik">NIK</th>
                                <th class="col-tanggal-lahir">
                                    Tanggal Melamar
                                </th>
                                <th class="col-perusahaan">Perusahaan</th>
                                <th class="col-position">Jabatan</th>
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
                            <tr v-else v-for="(u, index) in users" :key="u.id">
                                <td>{{ startIndex + index + 1 }}</td>
                                <td>
                                    <div class="cell-user">
                                        <div class="cell-avatar">
                                            {{ u.name.charAt(0) }}
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
                                    <span class="cell-nik">{{ u.nik }}</span>
                                </td>

                                <td>
                                    <span class="cell-tanggal-lahir">
                                        {{ u.tanggal_lahir }}
                                    </span>
                                </td>

                                <td>
                                    <span class="cell-perusahaan">
                                        {{ u.perusahaan }}
                                    </span>
                                </td>

                                <td>{{ u.position }}</td>

                                <td>
                                    <span
                                        class="status-pill"
                                        :class="{
                                            'status-active':
                                                u.status === 'Aktif',
                                            'status-inactive':
                                                u.status !== 'Aktif',
                                        }"
                                    >
                                        {{ u.status }}
                                    </span>
                                </td>

                                <td class="actions-cell">
                                    <button
                                        class="action-btn emoji primary"
                                        title="Lihat Detail Pelamar"
                                        @click="openDetail(u.id)"
                                    >
                                        👁️
                                    </button>

                                    <!-- <button
                                        class="action-btn emoji success"
                                        title="Slip Gaji Karyawan"
                                        @click="openPayslip(u.id)"
                                    >
                                        📄
                                    </button> -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER DATATABLE: INFO + PAGINATION -->
                <div class="dt-footer" v-if="!loadingUsers">
                    <div class="dt-info">
                        Menampilkan
                        <strong v-if="totalItems">{{ startIndex + 1 }}</strong>
                        <strong v-else>0</strong>
                        &nbsp;–&nbsp;
                        <strong>{{ endIndex }}</strong>
                        dari
                        <strong>{{ totalItems }}</strong>
                        pelamar
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
                                currentPage === totalPages || totalPages === 0
                            "
                            @click="goToPage(currentPage + 1)"
                        >
                            »
                        </button>
                    </div>
                </div>
            </div>

            <ImportKaryawan
                v-if="showImportKaryawanModal"
                @closeModal="closeModalImportKaryawan"
                @refreshData="fetchEmployees"
            ></ImportKaryawan>
            <ImportGaji
                v-if="showImportGajiModal"
                @closeModal="closeModalImportGaji"
                @refreshData="fetchEmployees"
            ></ImportGaji>
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
import ImportGaji from '../import/ImportGaji.vue';
import ImportKaryawan from '../import/ImportKaryawan.vue';

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

            showImportGajiModal: false,
            showImportKaryawanModal: false,
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
    },
    created() {
        this.debouncedFetch = this.debounce(() => {
            this.fetchEmployees(1);
        }, 400);

        this.fetchEmployees(1);
    },
    mounted() {
        this.fetchEmployees();
    },
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

            try {
                const res = await axios.get('/employee', {
                    params: {
                        page,
                        per_page: this.perPage,
                        search: this.search,
                        status_active: 0,
                    },
                });

                this.users = res.data.data;
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
        editUser(u) {
            alert(`Edit: ${u.name}`);
        },
        deleteUser(u) {
            if (confirm(`Hapus ${u.name}?`)) {
                alert('Deleted');
            }
        },
        tambahKaryawan() {
            this.$inertia.visit('/karyawan/tambah-karyawan');
        },
    },
};
</script>
