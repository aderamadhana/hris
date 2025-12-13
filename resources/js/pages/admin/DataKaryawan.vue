<template>
    <AppLayout>
        <section class="page employees-page">
            <!-- HEADER + ACTIONS -->
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Data Karyawan</h2>
                        <!-- <span class="page-chip">Master Data</span> -->
                    </div>
                    <p class="page-subtitle">
                        Kelola informasi karyawan, mulai dari penambahan data,
                        import massal, hingga penghapusan.
                    </p>
                </div>

                <div class="page-actions">
                    <DropdownButton label="Upload Data">
                        <a @click="openImportKaryawanModal">
                            ⬆️ Upload Excel Karyawan
                        </a>
                        <a @click="openImportPayslipModal">
                            🧾 Upload Slip Gaji Karyawan
                        </a>
                    </DropdownButton>
                    <Button variant="primary" @click="tambahKaryawan">
                        ➕ Tambah Karyawan
                    </Button>
                </div>
            </div>

            <!-- RINGKASAN DI ATAS TABEL -->
            <div class="overview-row">
                <div class="overview-card primary">
                    <div class="overview-label">Total Karyawan</div>
                    <div class="overview-value">{{ totalItems }}</div>
                </div>
                <div class="overview-card neutral">
                    <div class="overview-label">Kontrak Hampir Habis</div>
                    <div class="overview-value">{{ totalItems }}</div>
                </div>
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
                                <th class="col-tanggal-lahir">Tanggal Lahir</th>
                                <th class="col-perusahaan">Perusahaan</th>
                                <th class="col-position">Jabatan</th>
                                <th class="col-status">Status</th>
                                <th class="col-action">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="paginatedUsers.length === 0">
                                <td colspan="8" class="empty-row">
                                    Tidak ada data ...
                                </td>
                            </tr>

                            <tr
                                v-for="(u, index) in paginatedUsers"
                                :key="u.id"
                            >
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
                                    <span class="cell-tanggal-lahir">{{
                                        u.tanggal_lahir
                                    }}</span>
                                </td>

                                <td>
                                    <span class="cell-perusahaan">{{
                                        u.perusahaan
                                    }}</span>
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
                                    <Button
                                        type="button"
                                        variant="warning"
                                        title="Detail karyawan"
                                        @click="openDetail(u.id)"
                                    >
                                        Detail Karyawan </Button
                                    >&nbsp;

                                    <Button
                                        type="button"
                                        variant="success"
                                        title="Slip gaji karyawan"
                                        @click="openPayslip(u.id)"
                                    >
                                        Slip Gaji
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER DATATABLE: INFO + PAGINATION -->
                <div class="dt-footer">
                    <div class="dt-info">
                        Menampilkan
                        <strong v-if="totalItems">{{ startIndex + 1 }}</strong>
                        <strong v-else>0</strong>
                        &nbsp;–&nbsp;
                        <strong>{{ endIndex }}</strong>
                        dari
                        <strong>{{ totalItems }}</strong>
                        karyawan
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
            users: [], // ← DATA DARI /employee
            currentPage: 1,
            perPage: 10,
            loadingUsers: false,

            search: '',
            sortKey: 'name',
            sortDir: 'asc',

            showImportGajiModal: false,
            showImportKaryawanModal: false,
        };
    },

    computed: {
        filteredUsers() {
            if (!this.search) {
                return this.users;
            }

            const keyword = this.search.toLowerCase();

            return this.users.filter((u) => {
                return (
                    u.name.toLowerCase().includes(keyword) ||
                    u.nik.toLowerCase().includes(keyword) ||
                    u.position.toLowerCase().includes(keyword)
                );
            });
        },

        totalItems() {
            return this.filteredUsers.length;
        },

        totalPages() {
            return Math.ceil(this.totalItems / this.perPage);
        },

        startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },

        endIndex() {
            return Math.min(this.startIndex + this.perPage, this.totalItems);
        },

        paginatedUsers() {
            return this.filteredUsers.slice(this.startIndex, this.endIndex);
        },

        pages() {
            return Array.from({ length: this.totalPages }, (_, i) => i + 1);
        },
    },

    watch: {
        search() {
            this.currentPage = 1;
        },
        perPage() {
            this.currentPage = 1;
        },
    },
    mounted() {
        this.fetchEmployees();
    },
    methods: {
        showSuccess() {
            this.triggerAlertHtml('success', '<b>Berhasil</b>', 3000);
        },
        fiturBelumTersedia() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },
        async fetchEmployees() {
            this.loadingUsers = true;

            try {
                const res = await axios.get('/employee');
                this.users = res.data;
                this.currentPage = 1; // reset pagination
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memuat data karyawan.');
            } finally {
                this.loadingUsers = false;
            }
        },
        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
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
            router.visit(`/employee/profil/${u}`);
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
            this.$inertia.visit('/admin/karyawan/tambah');
        },
    },
};
</script>
