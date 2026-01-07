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

            <!-- Toolbar -->
            <div class="dt-toolbar">
                <div class="dt-length">
                    <label>
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
                        <label>Status</label>
                        <select v-model="status" class="filter-input">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
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
                                <td class="actions-cell">
                                    <Link
                                        :href="`/master/client/edit/${item.id}`"
                                        class="action-btn primary"
                                    >
                                        <font-awesome-icon
                                            icon="pen-to-square"
                                        />
                                    </Link>
                                    <button
                                        class="action-btn danger"
                                        @click="hapus(item.id)"
                                    >
                                        <font-awesome-icon icon="trash" />
                                    </button>
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
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    components: { AppLayout, Button, Link },

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
                const res = await axios.get('/master/client', {
                    params: {
                        search: this.search,
                        status: this.status,
                        page,
                        per_page: this.perPage,
                    },
                });

                this.items = res.data.data;
                this.currentPage = res.data.current_page;
                this.totalItems = res.data.total;
                this.totalPages = res.data.last_page;
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
            this.$inertia.visit('/master/client/create');
        },

        async hapus(id) {
            if (!confirm('Hapus perusahaan ini?')) return;

            try {
                await axios.delete(`/master/client/${id}`);
                triggerAlert('success', 'Perusahaan berhasil dihapus');
                this.fetchPerusahaan(this.currentPage);
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
                this.fetchPerusahaan();
            } catch (error) {
                console.error('Sync gagal', error);
                triggerAlert('error', 'Sync gagal');
            } finally {
                this.isDownloading = false;
            }
        },
    },

    mounted() {
        this.fetchPerusahaan();
    },
};
</script>
