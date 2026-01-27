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
                <div class="page-actions">
                    <Button
                        variant="primary"
                        size="md"
                        :loading="isDownloading"
                        @click="downloadPresensi()"
                        class="download-btn"
                    >
                        <font-awesome-icon icon="download" class="icon" />
                        Download Presensi
                    </Button>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="dt-toolbar-mobile">
                <!-- Row 1: Length & Search -->
                <div class="dt-row-main">
                    <label class="dt-length-compact">
                        Tampil
                        <select v-model.number="perPage" @change="fetchAbsensi">
                            <option :value="5">5</option>
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
                            placeholder="Cari nama karyawan"
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
                            <label for="">Perusahaan</label>
                            <Select2
                                v-model="filtered_perusahaan"
                                :settings="{ width: '100%' }"
                            >
                                <option value="">Semua Perusahaan</option>
                                <option
                                    v-for="value in data_filtered_perusahaan"
                                    :key="value"
                                    :value="value.id"
                                >
                                    {{ value.nama_perusahaan }}
                                </option>
                            </Select2>
                        </div>
                        <div class="form-group">
                            <label for="">Divisi / Dept</label>
                            <Select2
                                v-model="filtered_jabatan"
                                :settings="{ width: '100%' }"
                                :disabled="!filtered_perusahaan"
                            >
                                <option value="">Semua Divisi / Dept</option>
                                <option
                                    v-for="value in data_filtered_jabatan"
                                    :key="value"
                                    :value="value.id"
                                >
                                    {{ value.nama_divisi }}
                                </option>
                            </Select2>
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Absen</label>
                            <input
                                type="date"
                                v-model="filtered_tanggal_absen"
                                class="form-control"
                            />
                        </div>

                        <div class="form-group filter-actions">
                            <label class="filter-label">&nbsp;</label>
                            <Button
                                variant="secondary"
                                class="filter-apply-btn"
                                @click="filteredData"
                            >
                                <font-awesome-icon icon="filter" />
                                Terapkan Filter
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="table-card card">
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
                                                                item
                                                                    .data_presensi
                                                                    ?.jam_masuk,
                                                            ) &&
                                                            toHHMM(
                                                                item
                                                                    .data_presensi
                                                                    ?.jam_pulang,
                                                            )
                                                        "
                                                    >
                                                        ({{
                                                            toHHMM(
                                                                item
                                                                    .data_presensi
                                                                    .jam_masuk,
                                                            )
                                                        }}
                                                        -
                                                        {{
                                                            toHHMM(
                                                                item
                                                                    .data_presensi
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
                                        <button
                                            class="status-toggle-btn"
                                            :class="{
                                                'status-valid':
                                                    item.status_kehadiran ===
                                                    'valid',
                                                'status-tidak-valid':
                                                    item.status_kehadiran ===
                                                    'tidak_valid',
                                            }"
                                            @click="toggleStatus(item)"
                                            :disabled="loadingStatus[item.id]"
                                        >
                                            <span
                                                v-if="!loadingStatus[item.id]"
                                            >
                                                {{
                                                    item.status_kehadiran ===
                                                    'valid'
                                                        ? 'Valid'
                                                        : 'Tidak Valid'
                                                }}
                                            </span>
                                            <span v-else>
                                                <font-awesome-icon
                                                    icon="spinner"
                                                    spin
                                                    class="loading-icon"
                                                />
                                            </span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER -->
                    <div class="dt-footer" v-if="!loading">
                        <div class="dt-info">
                            Menampilkan
                            <strong v-if="totalItems">{{
                                startIndex + 1
                            }}</strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> presensi
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
            perPage: 50,
            totalItems: 0,
            totalPages: 0,

            data_filtered_perusahaan: [],
            data_filtered_jabatan: [],

            filtered_jabatan: '',
            filtered_perusahaan: '',
            filtered_tanggal_absen: '',
            showFilters: false,
            loadingStatus: {},
        };
    },

    watch: {
        search() {
            this.fetchAbsensi();
        },
        status() {
            this.fetchAbsensi();
        },
        filtered_perusahaan(newVal, oldVal) {
            console.log('Perusahaan changed:', newVal);
            this.onPerusahaanChange();
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
            if (this.totalPages <= 1) return [];
            const range = 2;
            const pages = [];
            const start = Math.max(1, this.currentPage - range);
            const end = Math.min(this.totalPages, this.currentPage + range);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },
    },
    //created() {
    //     const now = new Date();
    //     const yyyy = now.getFullYear();
    //     const mm = String(now.getMonth() + 1).padStart(2, '0');
    //     const dd = String(now.getDate()).padStart(2, '0');

    //     this.filtered_tanggal_absen = `${yyyy}-${mm}-${dd}`;
    // },

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
            try {
                const res = await axios.get('/referensi/perusahaan-divisi');
                this.data_filtered_perusahaan = res.data.data || [];
                this.all_data = res.data.data;
            } catch (err) {
                console.error(err);
                triggerAlert(
                    'error',
                    'Gagal memuat filter perusahaan/jabatan.',
                );
            }
        },
        onPerusahaanChange() {
            console.log('onPerusahaanChange called');

            // Reset divisi
            this.filtered_jabatan = '';
            this.data_filtered_jabatan = [];

            // Jika tidak ada perusahaan dipilih, stop
            if (!this.filtered_perusahaan) {
                console.log('Tidak ada perusahaan dipilih');
                return;
            }

            // Filter perusahaan yang dipilih dari data_perusahaan
            const perusahaanSelected = this.data_filtered_perusahaan.find(
                (p) => p.id == this.filtered_perusahaan, // Gunakan == untuk compare
            );

            console.log('Perusahaan Selected:', perusahaanSelected);

            // Ambil divisi dari perusahaan yang dipilih
            if (perusahaanSelected) {
                if (
                    perusahaanSelected.divisi &&
                    Array.isArray(perusahaanSelected.divisi) &&
                    perusahaanSelected.divisi.length > 0
                ) {
                    this.data_filtered_jabatan =
                        perusahaanSelected.divisi.filter(
                            (d) => d.status === 'aktif',
                        );
                    console.log(
                        'Divisi ditemukan:',
                        this.data_filtered_jabatan,
                    );
                } else {
                    console.log('Perusahaan tidak memiliki divisi');
                }
            } else {
                console.log('Perusahaan tidak ditemukan dalam data');
            }
        },
        async fetchAbsensi(page = 1) {
            this.loading = true;
            try {
                const res = await axios.get('/logs/presensi/all', {
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

        async downloadPresensi() {
            try {
                this.isDownloading = true;
                const response = await axios.get('/export/presensi', {
                    responseType: 'blob',
                    params: {
                        search: this.search,
                        filtered_perusahaan: this.filtered_perusahaan,
                        filtered_jabatan: this.filtered_jabatan,
                        filtered_tanggal_absen: this.filtered_tanggal_absen,
                    },
                });

                const blob = new Blob([response.data], {
                    type: response.headers['content-type'],
                });

                const pad2 = (n) => String(n).padStart(2, '0');
                const d = new Date();
                const tgl = pad2(d.getDate());
                const bln = pad2(d.getMonth() + 1);
                const thn = d.getFullYear();
                const hari = pad2(d.getHours());
                const jam = pad2(d.getMinutes());
                const detik = pad2(d.getSeconds());

                const filename = `presensi_${tgl}${bln}${thn}${hari}${jam}${detik}.xlsx`;

                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', filename);
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Download gagal', error);
            } finally {
                this.isDownloading = false;
            }
        },

        filteredData() {
            this.fetchAbsensi();
        },

        async toggleStatus(item) {
            const newStatus =
                item.status_kehadiran === 'valid' ? 'Tidak Valid' : 'Valid';

            if (!confirm(`Ubah status kehadiran menjadi ${newStatus}?`)) {
                return;
            }

            // Set loading - Vue 3 way atau vanilla JS
            this.loadingStatus[item.id] = true;
            this.loadingStatus = { ...this.loadingStatus }; // Trigger reactivity

            try {
                const statusValue =
                    item.status_kehadiran === 'valid' ? 'tidak_valid' : 'valid';

                const response = await axios.post(
                    `/logs/presensi/${item.id}/update-status`,
                    {
                        status_kehadiran: statusValue,
                    },
                );

                // Update local data
                item.status_kehadiran = statusValue;

                triggerAlert('success', 'Status kehadiran berhasil diubah');
            } catch (error) {
                console.error('Error updating status:', error);
                triggerAlert(
                    'warning',
                    error.response?.data?.message ||
                        'Terjadi kesalahan saat mengubah status',
                );
            } finally {
                // Remove loading
                delete this.loadingStatus[item.id];
                this.loadingStatus = { ...this.loadingStatus }; // Trigger reactivity
            }
        },
    },

    mounted() {
        this.fetchAbsensi();

        this.getFilteredPerusahaanDanJabatan();
    },
};
</script>
