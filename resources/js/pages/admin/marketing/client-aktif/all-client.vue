<template>
    <AppLayout>
        <section class="page companies-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Client</h2>
                    </div>

                    <p class="page-subtitle">
                        Kelola data perusahaan dan periode MOU
                    </p>
                </div>
                <div class="page-actions">
                    <Button
                        variant="warning"
                        size="md"
                        :loading="isDownloadingClient"
                        @click="downloadClient()"
                        class="download-btn"
                    >
                        <font-awesome-icon icon="download" class="icon" />
                        Download Client
                    </Button>
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
                </div>
            </div>

            <!-- RINGKASAN -->
            <div class="overview-row">
                <div class="overview-card primary">
                    <div class="card-content">
                        <div class="card-left">
                            <div class="overview-label">Total Aktif</div>
                            <div class="overview-value">
                                {{ statistics.total_all_active }}
                            </div>
                            <div class="overview-badge positive">
                                <span>●</span> Terdaftar
                            </div>
                        </div>
                        <div class="card-icon">
                            <font-awesome-icon icon="users" />
                        </div>
                    </div>
                </div>

                <div class="overview-card danger">
                    <div class="card-content">
                        <div class="card-left">
                            <div class="overview-label">Total Tidak Aktif</div>
                            <div class="overview-value">
                                {{ statistics.total_all_non_active }}
                            </div>
                            <div class="overview-badge warning">
                                <span>●</span> Perlu Tindakan
                            </div>
                        </div>
                        <div class="card-icon">
                            <font-awesome-icon icon="file-contract" />
                        </div>
                    </div>
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
                            @change="fetchPerusahaan"
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
                            placeholder="Cari nama / kode perusahaan"
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
                            <label>Status</label>
                            <select v-model="status" class="filter-input">
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
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
                                    <th>Kode</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Awal MOU</th>
                                    <th>Akhir MOU</th>
                                    <th>Status</th>
                                    <th>Total Karyawan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="8" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="items.length === 0">
                                    <td colspan="8" class="empty-row">
                                        Tidak ada data
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(item, index) in items"
                                    :key="item.id"
                                >
                                    <td>{{ startIndex + index + 1 }}</td>
                                    <td>{{ item.kode_perusahaan }}</td>
                                    <td>{{ item.nama_perusahaan }}</td>
                                    <td>{{ item.alamat || '-' }}</td>
                                    <td>{{ item.tanggal_awal_mou || '-' }}</td>
                                    <td>{{ item.tanggal_akhir_mou || '-' }}</td>
                                    <td>
                                        <span
                                            class="status-pill"
                                            :class="
                                                item.status === 'aktif'
                                                    ? 'status-open'
                                                    : 'status-closed'
                                            "
                                        >
                                            {{
                                                item.status === 'aktif'
                                                    ? 'Aktif'
                                                    : 'Tidak Aktif'
                                            }}
                                        </span>
                                    </td>
                                    <td>{{ item.total_karyawan_aktif }}</td>
                                    <td class="col-actions">
                                        <div class="actions-wrap">
                                            <Link
                                                :href="`/marketing/client/aktif/edit/${item.id}`"
                                                class="action-btn primary"
                                                title="Edit"
                                            >
                                                <font-awesome-icon
                                                    icon="pen-to-square"
                                                />
                                            </Link>

                                            <button
                                                class="action-btn danger"
                                                title="Hapus"
                                                @click="hapus(item.id)"
                                            >
                                                <font-awesome-icon
                                                    icon="trash"
                                                />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER DATATABLE: INFO + PAGINATION -->
                    <div class="dt-footer" v-if="!loading">
                        <div class="dt-info">
                            Menampilkan
                            <strong v-if="totalItems">{{
                                startIndex + 1
                            }}</strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> perusahaan
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
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    components: { AppLayout, Button, Link },

    data() {
        return {
            search: '',
            status: 'aktif',
            items: [],
            statistics: [],
            loading: false,
            isDownloading: false,
            isDownloadingClient: false,

            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,
        };
    },

    watch: {
        search() {
            this.fetchPerusahaan();
        },
        status() {
            this.fetchPerusahaan();
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

    methods: {
        async fetchPerusahaan(page = 1) {
            this.loading = true;
            try {
                const res = await axios.get('/marketing/client/aktif/all', {
                    params: {
                        search: this.search,
                        status: this.status,
                        page,
                        per_page: this.perPage,
                    },
                });

                this.items = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.statistics = res.data.statistics;
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
            this.fetchPerusahaan(page);
        },

        tambahPerusahaan() {
            this.$inertia.visit('/marketing/client/aktif/create');
        },

        async hapus(id) {
            if (!confirm('Hapus perusahaan ini?')) return;

            try {
                await axios.delete(`/marketing/client/aktif/${id}`);
                triggerAlert('success', 'Perusahaan berhasil dihapus');
                this.fetchPerusahaan(this.currentPage);
            } catch {
                triggerAlert('error', 'Gagal menghapus perusahaan');
            }
        },

        async syncPerusahaan() {
            try {
                this.isDownloading = true;
                const { data } = await axios.get(
                    '/marketing/client/aktif/sync',
                );

                // contoh: tampilkan hasil
                console.log(data.stats);
                triggerAlert('success', data.message);
                this.fetchPerusahaan();
            } catch (error) {
                console.error('Sync gagal', error);
                triggerAlert('error', 'Sync gagal');
            } finally {
                this.isDownloading = false;
            }
        },
        async downloadClient() {
            try {
                this.isDownloadingClient = true;
                const response = await axios.get('/export/client', {
                    responseType: 'blob',
                    params: {
                        search: this.search,
                        status: this.status,
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

                const filename = `data_client_${tgl}${bln}${thn}${hari}${jam}${detik}.xlsx`;

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
                this.isDownloadingClient = false;
            }
        },
    },

    mounted() {
        this.fetchPerusahaan();
    },
};
</script>
