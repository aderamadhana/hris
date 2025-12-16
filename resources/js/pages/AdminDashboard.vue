<template>
    <div class="page-header">
        <div>
            <div class="page-heading-row">
                <h2 class="page-title">Dashboard Admin</h2>
                <!-- <span class="page-chip">Master Data</span> -->
            </div>
            <p class="page-subtitle">
                Pantau status karyawan, kontrak, rekrutmen, dan aktivitas keluar
                masuk secara real-time.
            </p>
        </div>
    </div>
    <div class="dashboard-container">
        <!-- KATEGORI: HR -->
        <div class="category-section">
            <h2 class="category-title">HR</h2>
            <div class="dashboard-grid">
                <!-- HR | KARYAWAN AKTIF -->
                <div class="dashboard-card success">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <h3 class="header-title">Karyawan Aktif</h3>
                        </div>
                        <div class="header-right">
                            <span class="dashboard-card-badge">Total</span>
                        </div>
                    </div>

                    <div class="dashboard-value">
                        {{ stats.karyawanAktif }}
                    </div>
                    <div class="dashboard-meta">
                        PHK · Habis kontrak · Non-aktif sistem
                    </div>
                </div>

                <!-- HR | KARYAWAN TIDAK AKTIF -->
                <div class="dashboard-card danger">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <h3 class="header-title">Karyawan Tidak Aktif</h3>
                        </div>
                        <div class="header-right">
                            <span class="dashboard-card-badge">Total</span>
                        </div>
                    </div>

                    <div class="dashboard-value">
                        {{ stats.karyawanTidakAktif }}
                    </div>
                    <div class="dashboard-meta">
                        PHK · Habis kontrak · Non-aktif sistem
                    </div>
                </div>

                <!-- HR | KARYAWAN BARU -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <h3 class="header-title">Karyawan Baru</h3>
                        </div>
                        <div class="header-right">
                            <select
                                class="dashboard-card-dropdown"
                                v-model="filters.karyawanBaru"
                                @change="fetchStats"
                            >
                                <option value="1">Hari ini</option>
                                <option value="7">7 hari terakhir</option>
                                <option value="30">30 hari terakhir</option>
                            </select>
                        </div>
                    </div>

                    <div class="dashboard-value">{{ stats.karyawanBaru }}</div>
                    <div class="dashboard-meta">
                        Onboarding {{ getMetaText(filters.karyawanBaru) }}
                    </div>
                </div>

                <!-- HR | RESIGN -->
                <div class="dashboard-card danger">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <h3 class="header-title">Resign</h3>
                        </div>
                        <div class="header-right">
                            <select
                                class="dashboard-card-dropdown"
                                v-model="filters.resign"
                                @change="fetchStats"
                            >
                                <option value="1">Hari ini</option>
                                <option value="7">7 hari terakhir</option>
                                <option value="30">30 hari terakhir</option>
                            </select>
                        </div>
                    </div>

                    <div class="dashboard-value">{{ stats.resign }}</div>
                    <div class="dashboard-meta">Perlu exit clearance</div>
                </div>
            </div>
        </div>

        <!-- KATEGORI: KONTRAK -->
        <div class="category-section">
            <h2 class="category-title">Kontrak</h2>
            <div class="dashboard-grid">
                <!-- KONTRAK | HAMPIR HABIS -->
                <div class="dashboard-card warning">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <h3 class="header-title">Kontrak Hampir Habis</h3>
                        </div>
                        <div class="header-right">
                            <select
                                class="dashboard-card-dropdown"
                                v-model="filters.kontrakHabis"
                                @change="fetchStats"
                            >
                                <option value="7">Urgent (&lt; 7 hari)</option>
                                <option value="30">&lt; 30 hari</option>
                            </select>
                        </div>
                    </div>

                    <div class="dashboard-value">
                        {{ stats.kontrakHampirHabis }}
                    </div>
                    <div class="dashboard-meta">
                        Perlu keputusan perpanjangan / offboarding
                    </div>
                </div>
            </div>
        </div>

        <!-- KATEGORI: CLIENT -->
        <div class="category-section">
            <h2 class="category-title">Client</h2>
            <div class="dashboard-grid">
                <!-- CLIENT | AKTIF -->
                <div class="dashboard-card success">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <h3 class="header-title">Client Aktif</h3>
                        </div>
                        <div class="header-right">
                            <span class="dashboard-card-badge">Berjalan</span>
                        </div>
                    </div>

                    <div class="dashboard-value">{{ stats.clientAktif }}</div>
                    <div class="dashboard-meta">Kontrak aktif & ongoing</div>
                </div>

                <!-- CLIENT | TIDAK AKTIF -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <h3 class="header-title">Client Tidak Aktif</h3>
                        </div>
                        <div class="header-right">
                            <span class="dashboard-card-badge">Closed</span>
                        </div>
                    </div>

                    <div class="dashboard-value">
                        {{ stats.clientTidakAktif }}
                    </div>
                    <div class="dashboard-meta">
                        Selesai · Putus · Tidak diperpanjang
                    </div>
                </div>
            </div>
        </div>

        <!-- KATEGORI: REKRUTMEN -->
        <div class="category-section">
            <h2 class="category-title">Rekrutmen</h2>
            <div class="dashboard-grid">
                <!-- REKRUTMEN | PELAMAR MASUK -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <h3 class="header-title">Pelamar Masuk</h3>
                        </div>
                        <div class="header-right">
                            <select
                                class="dashboard-card-dropdown"
                                v-model="filters.pelamar"
                                @change="fetchStats"
                            >
                                <option value="1">Hari ini</option>
                                <option value="7">7 hari terakhir</option>
                                <option value="30">30 hari terakhir</option>
                            </select>
                        </div>
                    </div>

                    <div class="dashboard-value">{{ stats.pelamarMasuk }}</div>
                    <div class="dashboard-meta">Perlu screening HR</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { defineComponent } from 'vue';

export default defineComponent({
    data() {
        return {
            stats: {
                karyawanAktif: 0,
                karyawanTidakAktif: 0,
                kontrakHampirHabis: 0,
                clientAktif: 0,
                clientTidakAktif: 0,
                karyawanBaru: 0,
                pelamarMasuk: 0,
                resign: 0,
            },
            filters: {
                kontrakHabis: 30,
                karyawanBaru: 7,
                pelamar: 7,
                resign: 7,
            },
            loading: false,
        };
    },
    mounted() {
        this.fetchStats();
    },
    methods: {
        async fetchStats() {
            this.loading = true;
            try {
                const response = await axios.get('/dashboard/stats', {
                    params: {
                        kontrak_habis: this.filters.kontrakHabis,
                        karyawan_baru: this.filters.karyawanBaru,
                        pelamar: this.filters.pelamar,
                        resign: this.filters.resign,
                    },
                });

                if (response.data.success) {
                    this.stats = response.data.data;
                }
            } catch (error) {
                console.error('Error fetching stats:', error);
            } finally {
                this.loading = false;
            }
        },
        getMetaText(days) {
            if (days == 1) return 'hari ini';
            if (days == 7) return '7 hari terakhir';
            if (days == 30) return '30 hari terakhir';
            return '';
        },
    },
});
</script>
