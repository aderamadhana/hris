<template>
    <AppLayout>
        <section class="page companies-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Presensi</h2>
                    </div>

                    <p class="page-subtitle">
                        Monitoring presensi semua karyawan
                    </p>
                </div>
                <!-- <div class="page-actions">
                    <Button
                        variant="success"
                        :loading="isDownloading"
                        @click="syncPerusahaan"
                    >
                        <font-awesome-icon icon="sync" class="icon" />
                        Sync Client
                    </Button>
                    <Button variant="primary" @click="tambahPerusahaan">
                        <font-awesome-icon icon="plus" class="icon" />
                        Tambah Client
                    </Button>
                </div> -->
            </div>

            <!-- Toolbar -->
            <div class="dt-toolbar">
                <div class="dt-length">
                    <label>
                        Tampil
                        <select v-model.number="perPage" @change="fetchAbsensi">
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                        </select>
                        data
                    </label>
                </div>

                <div class="dt-search">
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Cari nama / kode perusahaan"
                    />
                </div>
            </div>

            <div class="card">
                <!-- Filter -->
                <div class="filter-bar">
                    <div class="filter-right">
                        <label for="">Filter Perusahaan</label>
                        <Select2
                            v-model="filtered_perusahaan"
                            :settings="{ width: '100%' }"
                        >
                            <option value="">Semua Perusahaan</option>
                            <option
                                v-for="value in data_filtered_perusahaan"
                                :value="value"
                            >
                                {{ value }}
                            </option>
                        </Select2>
                    </div>
                    <div class="filter-right">
                        <label for="">Filter Divisi / Departemen</label>
                        <Select2
                            v-model="filtered_jabatan"
                            :settings="{ width: '100%' }"
                        >
                            <option value="">Semua Divisi / Departemen</option>
                            <option
                                v-for="value in data_filtered_jabatan"
                                :value="value"
                            >
                                {{ value }}
                            </option>
                        </Select2>
                    </div>
                    <div class="filter-right">
                        <label for="">Filter Tanggal Absen</label>
                        <input
                            type="date"
                            v-model="filtered_tanggal_absen"
                            class="form-control"
                        />
                    </div>
                    <div class="filter-right">
                        <Button
                            variant="secondary"
                            class="filter-btn"
                            @click="filteredData"
                        >
                            <font-awesome-icon
                                icon="filter"
                                class="filter-icon"
                            />
                            <span>Filter</span>
                        </Button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive-custom">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Absen</th>
                                <th>Nama</th>
                                <th>Perusahaan - Divisi / Departemen</th>
                                <th>Data Presensi</th>
                                <th>Status Kehadiran</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-if="loading">
                                <td colspan="6" class="loading-row">
                                    <div class="table-spinner">
                                        <span class="spinner"></span>
                                        <span class="spinner-text"
                                            >Memuat data...</span
                                        >
                                    </div>
                                </td>
                            </tr>

                            <tr v-else-if="items.length === 0">
                                <td colspan="6" class="empty-row">
                                    Tidak ada data
                                </td>
                            </tr>

                            <tr
                                v-else
                                v-for="(item, index) in items"
                                :key="item.id"
                            >
                                <td>{{ startIndex + index + 1 }}</td>
                                <td>{{ item.tanggal }}</td>
                                <td>{{ item.nama_karyawan }}</td>
                                <td>
                                    {{ item.nama_perusahaan }} -
                                    {{ item.nama_divisi }}
                                </td>
                                <td>
                                    <div class="presensi-cell">
                                        <div>
                                            <div>
                                                Shift:
                                                {{
                                                    item.data_presensi
                                                        ?.nama_shift || '-'
                                                }}
                                                <template
                                                    v-if="
                                                        toHHMM(
                                                            item.data_presensi
                                                                ?.jam_masuk,
                                                        ) &&
                                                        toHHMM(
                                                            item.data_presensi
                                                                ?.jam_pulang,
                                                        )
                                                    "
                                                >
                                                    ({{
                                                        toHHMM(
                                                            item.data_presensi
                                                                .jam_masuk,
                                                        )
                                                    }}
                                                    -
                                                    {{
                                                        toHHMM(
                                                            item.data_presensi
                                                                .jam_pulang,
                                                        )
                                                    }})
                                                </template>
                                                <span
                                                    v-if="
                                                        item.data_presensi
                                                            ?.jam_shift_masuk &&
                                                        item.data_presensi
                                                            ?.jam_shift_pulang
                                                    "
                                                >
                                                    ({{
                                                        item.data_presensi
                                                            .jam_shift_masuk
                                                    }}
                                                    -
                                                    {{
                                                        item.data_presensi
                                                            .jam_shift_pulang
                                                    }})
                                                </span>
                                            </div>

                                            <div>
                                                In:
                                                {{
                                                    item.data_presensi
                                                        ?.clock_in || '-'
                                                }}
                                            </div>
                                            <div>
                                                Out:
                                                {{
                                                    item.data_presensi
                                                        ?.clock_out || '-'
                                                }}
                                            </div>

                                            <div>
                                                Durasi:
                                                <span
                                                    v-if="
                                                        item.data_presensi
                                                            ?.total_jam_kerja_hhmm
                                                    "
                                                >
                                                    {{
                                                        item.data_presensi
                                                            .total_jam_kerja_hhmm
                                                    }}
                                                </span>
                                                <span v-else>
                                                    {{
                                                        item.data_presensi
                                                            ?.durasi_label ||
                                                        '-'
                                                    }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="presensi-photos">
                                            <img
                                                v-if="
                                                    item.data_presensi
                                                        ?.foto_masuk_url
                                                "
                                                :src="
                                                    item.data_presensi
                                                        .foto_masuk_url
                                                "
                                                class="thumb"
                                            />
                                            <img
                                                v-if="
                                                    item.data_presensi
                                                        ?.foto_pulang_url
                                                "
                                                :src="
                                                    item.data_presensi
                                                        .foto_pulang_url
                                                "
                                                class="thumb"
                                            />
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <span
                                        class="status-pill"
                                        :class="
                                            item.status_kehadiran === 'valid'
                                                ? 'status-open'
                                                : 'status-closed'
                                        "
                                    >
                                        {{
                                            item.status_kehadiran ===
                                            'tidak_valid'
                                                ? 'Tidak Valid'
                                                : 'Aktif'
                                        }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER DATATABLE: INFO + PAGINATION -->
                <div class="dt-footer" v-if="!loading">
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

export default {
    components: { AppLayout, Button, Link, Select2 },

    data() {
        return {
            search: '',
            status: '',
            items: [],
            loading: false,
            isDownloading: false,

            currentPage: 1,
            perPage: 10,
            totalItems: 0,
            totalPages: 0,

            data_filtered_perusahaan: [],
            data_filtered_jabatan: [],

            filtered_jabatan: '',
            filtered_perusahaan: '',
            filtered_tanggal_absen: '',
        };
    },

    watch: {
        search() {
            this.fetchAbsensi();
        },
        status() {
            this.fetchAbsensi();
        },
    },

    computed: {
        startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },
        endIndex() {
            return Math.min(
                this.startIndex + this.items.length,
                this.totalItems,
            );
        },
        pages() {
            const pages = [];
            for (let i = 1; i <= this.totalPages; i++) {
                pages.push(i);
            }
            return pages;
        },
    },
    created() {
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');

        this.filtered_tanggal_absen = `${yyyy}-${mm}-${dd}`;
    },

    methods: {
        toHHMM(v) {
            if (!v) return null;
            const s = String(v).trim();

            // ISO: ambil setelah "T" -> "HH:MM"
            let m = s.match(/T(\d{2}:\d{2})/);
            if (m) return m[1];

            // TIME biasa: "HH:MM" atau "HH:MM:SS"
            m = s.match(/(\d{2}:\d{2})/);
            return m ? m[1] : null;
        },
        async getFilteredPerusahaanDanJabatan() {
            this.loadingUsers = true;

            try {
                const res = await axios.get(
                    '/referensi/get-filter_perusahaan_dan_jabatan',
                    {
                        params: {},
                    },
                );

                this.data_filtered_perusahaan = res.data.perusahaan;
                this.data_filtered_jabatan = res.data.position;
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memuat data karyawan.');
            } finally {
                this.loadingUsers = false;
            }
        },
        async fetchAbsensi(page = 1) {
            this.loading = true;
            try {
                const res = await axios.get('/logs/presensi', {
                    params: {
                        search: this.search,
                        status: this.status,
                        page,
                        per_page: this.perPage,
                        filtered_jabatan: this.filtered_jabatan,
                        filtered_perusahaan: this.filtered_perusahaan,
                        filtered_tanggal_absen: this.filtered_tanggal_absen,
                    },
                });

                this.items = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (e) {
                console.log(e);
                triggerAlert('error', 'Gagal memuat data perusahaan');
            } finally {
                this.loading = false;
            }
        },

        goToPage(page) {
            this.fetchAbsensi(page);
        },

        tambahPerusahaan() {
            this.$inertia.visit('/master/client/create');
        },

        async hapus(id) {
            if (!confirm('Hapus perusahaan ini?')) return;

            try {
                await axios.delete(`/master/client/${id}`);
                triggerAlert('success', 'Perusahaan berhasil dihapus');
                this.fetchAbsensi(this.currentPage);
            } catch {
                triggerAlert('error', 'Gagal menghapus perusahaan');
            }
        },

        async syncPerusahaan() {
            try {
                this.isDownloading = true;
                const { data } = await axios.get('/master/client/sync');

                // contoh: tampilkan hasil
                console.log(data.stats);
                triggerAlert('success', data.message);
                this.fetchAbsensi();
            } catch (error) {
                console.error('Sync gagal', error);
                triggerAlert('error', 'Sync gagal');
            } finally {
                this.isDownloading = false;
            }
        },

        filteredData() {
            this.fetchAbsensi();
        },
    },

    mounted() {
        this.fetchAbsensi();

        this.getFilteredPerusahaanDanJabatan();
    },
};
</script>
