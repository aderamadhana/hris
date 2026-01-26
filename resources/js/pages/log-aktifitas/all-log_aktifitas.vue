<template>
    <AppLayout>
        <section class="page companies-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Aktifitas</h2>
                    </div>

                    <p class="page-subtitle">
                        Monitoring aktifitas semua karyawan
                    </p>
                </div>
                <div class="page-actions">
                    <Button
                        variant="primary"
                        size="md"
                        :loading="isDownloading"
                        @click="downloadAktifitas()"
                        class="download-btn"
                    >
                        <font-awesome-icon icon="download" class="icon" />
                        Download Aktifitas
                    </Button>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="dt-toolbar-mobile">
                <!-- Row 1: Length & Search -->
                <div class="dt-row-main">
                    <label class="dt-length-compact">
                        Tampil
                        <select
                            v-model.number="perPage"
                            @change="fetchAktifitas"
                        >
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
                        <!-- <div class="form-group">
                            <label for="">Tanggal Absen</label>
                            <input
                                type="date"
                                v-model="filtered_tanggal_absen"
                                class="form-control"
                            />
                        </div> -->

                        <!-- <div class="form-group filter-actions">
                            <label class="filter-label">&nbsp;</label>
                            <Button
                                variant="secondary"
                                class="filter-apply-btn"
                                @click="filteredData"
                            >
                                <font-awesome-icon icon="filter" />
                                Terapkan Filter
                            </Button>
                        </div> -->
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="table-card card">
                    <div class="table-responsive-custom">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Karyawan</th>
                                    <th>Perusahaan / Penempatan</th>
                                    <th>Aktivitas & Jam Kerja</th>
                                    <th>Hasil & Biaya</th>
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
                                        {{ item.nama_perusahaan }}
                                        <div class="subtext">
                                            {{ item.nama_divisi }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="aktifitas-cell">
                                            <div class="aktifitas-main">
                                                <div class="aktifitas-kode">
                                                    {{
                                                        item.data_aktifitas
                                                            ?.kode_kerja || '-'
                                                    }}
                                                </div>

                                                <div class="aktifitas-shift">
                                                    Shift:
                                                    {{
                                                        item.data_aktifitas
                                                            ?.nama_shift || '-'
                                                    }}
                                                </div>

                                                <div class="aktifitas-jam">
                                                    Jam:
                                                    <span
                                                        v-if="
                                                            item.data_aktifitas
                                                                ?.jam_masuk &&
                                                            item.data_aktifitas
                                                                ?.jam_pulang
                                                        "
                                                    >
                                                        {{
                                                            toHHMM(
                                                                item
                                                                    .data_aktifitas
                                                                    .jam_masuk,
                                                            )
                                                        }}
                                                        –
                                                        {{
                                                            toHHMM(
                                                                item
                                                                    .data_aktifitas
                                                                    .jam_pulang,
                                                            )
                                                        }}
                                                    </span>
                                                    <span v-else>-</span>
                                                </div>

                                                <div class="aktifitas-durasi">
                                                    Durasi:
                                                    {{
                                                        item.data_aktifitas
                                                            ?.total_jam_kerja_hhmm ||
                                                        '-'
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="hasil-cell">
                                            <div>
                                                Hasil:
                                                {{
                                                    item.data_aktifitas
                                                        ?.hasil_kerja ?? 0
                                                }}
                                            </div>

                                            <div
                                                v-if="
                                                    item.data_aktifitas
                                                        ?.hasil_lembur
                                                "
                                            >
                                                Lembur:
                                                {{
                                                    item.data_aktifitas
                                                        .hasil_lembur
                                                }}
                                            </div>

                                            <div class="hasil-total">
                                                ACT: Rp
                                                {{
                                                    formatCurrency(
                                                        item.data_aktifitas
                                                            ?.total_act ?? 0,
                                                    )
                                                }}
                                            </div>
                                        </div>
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
                            dari <strong>{{ totalItems }}</strong> aktifitas
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
            // filtered_tanggal_absen: '',
            showFilters: false,
            loadingStatus: {},
        };
    },

    watch: {
        search() {
            this.fetchAktifitas();
        },
        status() {
            this.fetchAktifitas();
        },
        filtered_perusahaan(newVal, oldVal) {
            console.log('Perusahaan changed:', newVal);
            this.onPerusahaanChange();
            this.filteredData();
        },
        filtered_jabatan(newVal, oldVal) {
            console.log('Jabatan changed:', newVal);
            this.filteredData();
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
        formatCurrency(value) {
            if (!value) return '0';
            return new Intl.NumberFormat('id-ID').format(value);
        },
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
        async fetchAktifitas(page = 1) {
            this.loading = true;
            try {
                const res = await axios.get('/logs/aktifitas/all', {
                    params: {
                        search: this.search,
                        status: this.status,
                        page,
                        per_page: this.perPage,
                        filtered_jabatan: this.filtered_jabatan,
                        filtered_perusahaan: this.filtered_perusahaan,
                        // filtered_tanggal_absen: this.filtered_tanggal_absen,
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
            this.fetchAktifitas(page);
        },

        tambahPerusahaan() {
            this.$inertia.visit('/master/client/create');
        },

        async hapus(id) {
            if (!confirm('Hapus perusahaan ini?')) return;

            try {
                await axios.delete(`/master/client/${id}`);
                triggerAlert('success', 'Perusahaan berhasil dihapus');
                this.fetchAktifitas(this.currentPage);
            } catch {
                triggerAlert('error', 'Gagal menghapus perusahaan');
            }
        },

        async downloadAktifitas() {
            try {
                this.isDownloading = true;
                const response = await axios.get('/export/aktifitas', {
                    responseType: 'blob',
                    params: {
                        search: this.search,
                        filtered_perusahaan: this.filtered_perusahaan,
                        filtered_jabatan: this.filtered_jabatan,
                        // filtered_tanggal_absen: this.filtered_tanggal_absen,
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

                const filename = `aktifitas_${tgl}${bln}${thn}${hari}${jam}${detik}.xlsx`;

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
            this.fetchAktifitas();
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
                    `/logs/aktifitas/${item.id}/update-status`,
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
        this.fetchAktifitas();

        this.getFilteredPerusahaanDanJabatan();
    },
};
</script>
<style>
/* ===== AKTIVITAS CELL ===== */
.aktifitas-cell {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.aktifitas-main {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.aktifitas-kode {
    font-weight: 600;
    color: #111827;
}

.aktifitas-shift,
.aktifitas-jam,
.aktifitas-durasi {
    font-size: 13px;
    color: #374151;
}

/* ===== HASIL CELL ===== */
.hasil-cell {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 13px;
}

.hasil-cell div {
    color: #374151;
}

/* ACT highlight */
.hasil-total {
    margin-top: 4px;
    font-weight: 600;
    color: #065f46;
    background: #ecfdf5;
    padding: 4px 8px;
    border-radius: 6px;
    width: fit-content;
}

/* ===== ROW SPACING ===== */
tbody tr td {
    vertical-align: top;
    padding: 12px 10px;
}

/* ===== RESPONSIVE TOUCH ===== */
@media (max-width: 768px) {
    .aktifitas-shift,
    .aktifitas-jam,
    .aktifitas-durasi,
    .hasil-cell {
        font-size: 12px;
    }

    .hasil-total {
        font-size: 12px;
    }
}
</style>
