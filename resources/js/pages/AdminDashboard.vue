<template>
    <div>
        <div v-if="isLoading" class="fullpage-loader">
            <div class="fullpage-loader__card">
                <div class="fullpage-loader__spinner"></div>
                <div class="fullpage-loader__title">
                    Loading charts dashboard…
                </div>
                <div class="fullpage-loader__subtitle">
                    Mohon tunggu sebentar
                </div>
            </div>
        </div>
        <div class="page-header">
            <div>
                <div class="page-heading-row">
                    <h2 class="page-title">Dashboard Admin</h2>
                </div>
                <p class="page-subtitle">
                    Pantau status karyawan, kontrak, rekrutmen, dan aktivitas
                    keluar masuk secara real-time.
                </p>
            </div>
            <div class="last-updated">
                <p class="last-updated-label">Last Updated</p>
                <p class="last-updated-date">{{ currentDate }}</p>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="overview-grid">
            <div class="overview-card success" @click="toKaryawanAktif">
                <div class="overview-card-content">
                    <div class="overview-card-info">
                        <p class="overview-card-label">Karyawan Aktif</p>
                        <p class="overview-card-value">
                            {{ stats.karyawanAktif }}
                        </p>
                        <div class="overview-card-badge success">
                            +{{ stats.karyawanBaru }} bulan ini
                        </div>
                    </div>
                    <div class="overview-card-icon success">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overview-card danger" @click="toKaryawanTidakAktif">
                <div class="overview-card-content">
                    <div class="overview-card-info">
                        <p class="overview-card-label">Tidak Aktif</p>
                        <p class="overview-card-value">
                            {{ stats.karyawanTidakAktif }}
                        </p>
                        <p class="overview-card-meta">PHK · Habis kontrak</p>
                    </div>
                    <div class="overview-card-icon danger">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overview-card warning" @click="toKontrakHampirHabis">
                <div class="overview-card-content">
                    <div class="overview-card-info">
                        <p class="overview-card-label">Kontrak Hampir Habis</p>
                        <p class="overview-card-value">
                            {{ stats.kontrakHampirHabis }}
                        </p>
                        <p class="overview-card-meta warning">
                            Perlu tindakan segera
                        </p>
                    </div>
                    <div class="overview-card-icon warning">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                            ></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overview-card info" @click="toClientAktif">
                <div class="overview-card-content">
                    <div class="overview-card-info">
                        <p class="overview-card-label">Client Aktif</p>
                        <p class="overview-card-value">
                            {{ stats.clientAktif }}
                        </p>
                        <p class="overview-card-meta">Kontrak ongoing</p>
                    </div>
                    <div class="overview-card-icon info">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect
                                x="2"
                                y="7"
                                width="20"
                                height="14"
                                rx="2"
                                ry="2"
                            ></rect>
                            <path
                                d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"
                            ></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <!-- Tren Karyawan -->
            <div class="chart-card large">
                <h3 class="chart-title">Tren Karyawan (6 Bulan Terakhir)</h3>
                <div class="chart-container">
                    <canvas ref="employeeTrendChart"></canvas>
                </div>
            </div>

            <!-- Status Karyawan -->
            <div class="chart-card">
                <h3 class="chart-title">Status Karyawan</h3>
                <div class="chart-container">
                    <canvas ref="employeeStatusChart"></canvas>
                </div>
                <div class="legend-list">
                    <div
                        v-for="item in employeeStatus"
                        :key="item.name"
                        class="legend-item"
                    >
                        <div
                            class="legend-color"
                            :style="{ backgroundColor: item.color }"
                        ></div>
                        <span class="legend-label">{{ item.name }}</span>
                        <span class="legend-value">{{ item.value }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row Charts -->
        <div class="charts-section">
            <!-- Kontrak Hampir Habis per Tanggal -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        Kontrak Hampir Habis per Tanggal
                    </h3>
                    <select
                        class="chart-dropdown"
                        v-model="filters.kontrakHabis"
                        @change="fetchStats"
                    >
                        <option value="7">7 hari ke depan</option>
                        <option value="30">30 hari ke depan</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas ref="kontrakHampirHabisChart"></canvas>
                </div>
            </div>

            <!-- Karyawan per Departemen -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Karyawan per Departemen</h3>
                    <Select2
                        class="chart-dropdown"
                        v-model="filters.perusahaanSelected"
                        :settings="{ width: '40%' }"
                        @change="fetchStats"
                    >
                        <option value="">Pilih</option>
                        <option
                            v-for="perusahaan in data_perusahaan"
                            :key="perusahaan.id"
                            :value="perusahaan.id"
                        >
                            {{ perusahaan.nama_perusahaan }}
                        </option>
                    </Select2>
                </div>
                <div class="chart-container">
                    <canvas ref="departmentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- KATEGORI: HR -->
        <div class="category-section">
            <h2 class="category-title">HR</h2>
            <div class="dashboard-grid">
                <!-- HR | KARYAWAN BARU -->
                <div class="dashboard-card info">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <div class="card-icon info">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                    ></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="20" y1="8" x2="20" y2="14"></line>
                                    <line
                                        x1="23"
                                        y1="11"
                                        x2="17"
                                        y2="11"
                                    ></line>
                                </svg>
                            </div>
                            <h3 class="header-title">Karyawan Baru</h3>
                        </div>
                        <div class="header-right">
                            <select
                                class="dashboard-card-dropdown"
                                v-model="filters.karyawanBaru"
                                @change="fetchStats"
                            >
                                <option value="1">Hari ini</option>
                                <option value="7">7 hari</option>
                                <option value="30">30 hari</option>
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
                            <div class="card-icon danger">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                    ></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="18" y1="8" x2="23" y2="13"></line>
                                    <line x1="23" y1="8" x2="18" y2="13"></line>
                                </svg>
                            </div>
                            <h3 class="header-title">Resign</h3>
                        </div>
                        <div class="header-right">
                            <select
                                class="dashboard-card-dropdown"
                                v-model="filters.resign"
                                @change="fetchStats"
                            >
                                <option value="1">Hari ini</option>
                                <option value="7">7 hari</option>
                                <option value="30">30 hari</option>
                            </select>
                        </div>
                    </div>
                    <div class="dashboard-value">{{ stats.resign }}</div>
                    <div class="dashboard-meta">Perlu exit clearance</div>
                </div>

                <!-- KONTRAK | HAMPIR HABIS -->
                <div class="dashboard-card warning">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <div class="card-icon warning">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                    ></path>
                                    <polyline
                                        points="14 2 14 8 20 8"
                                    ></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
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

                <!-- PELAMAR MASUK -->
                <div class="dashboard-card success">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <div class="card-icon success">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                    ></path>
                                    <polyline
                                        points="14 2 14 8 20 8"
                                    ></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <h3 class="header-title">Pelamar Masuk</h3>
                        </div>
                        <div class="header-right">
                            <select
                                class="dashboard-card-dropdown"
                                v-model="filters.pelamar"
                                @change="fetchStats"
                            >
                                <option value="1">Hari ini</option>
                                <option value="7">7 hari</option>
                                <option value="30">30 hari</option>
                            </select>
                        </div>
                    </div>
                    <div class="dashboard-value">{{ stats.pelamarMasuk }}</div>
                    <div class="dashboard-meta">Perlu screening HR</div>
                </div>
            </div>
        </div>

        <!-- KATEGORI: CLIENT -->
        <div class="category-section">
            <h2 class="category-title">Client</h2>
            <div class="dashboard-grid">
                <!-- CLIENT | TIDAK AKTIF -->
                <div class="dashboard-card danger" @click="toClientTidakAktif">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <div class="card-icon danger">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon"
                                >
                                    <rect
                                        x="2"
                                        y="7"
                                        width="20"
                                        height="14"
                                        rx="2"
                                        ry="2"
                                    />
                                    <path
                                        d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"
                                    />
                                </svg>
                            </div>
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
    </div>
</template>

<script>
import Select2 from '@/components/Select2.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { defineComponent } from 'vue';

export default defineComponent({
    components: {
        Select2,
    },
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
                perusahaanSelected: '',
            },

            employeeTrend: [],
            employeeStatus: [],
            recruitmentFunnel: [],
            departmentData: [],

            charts: {},
            data_perusahaan: [],

            isUpdatingFromBackend: false,

            // internal
            debounceTimer: null,
            _fetchToken: 0,
            _chartJsPromise: null,
            isLoading: false,
        };
    },

    computed: {
        currentDate() {
            return new Date().toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
            });
        },
    },

    watch: {
        'filters.kontrakHabis'(n, o) {
            if (!this.isUpdatingFromBackend && n !== o)
                this.debouncedFetchStats();
        },
        'filters.karyawanBaru'(n, o) {
            if (!this.isUpdatingFromBackend && n !== o)
                this.debouncedFetchStats();
        },
        'filters.pelamar'(n, o) {
            if (!this.isUpdatingFromBackend && n !== o)
                this.debouncedFetchStats();
        },
        'filters.resign'(n, o) {
            if (!this.isUpdatingFromBackend && n !== o)
                this.debouncedFetchStats();
        },
        'filters.perusahaanSelected'(n, o) {
            if (!this.isUpdatingFromBackend && n !== o)
                this.debouncedFetchStats();
        },
    },

    mounted() {
        this.setupDebounce();
        this.getFPerusahaanDanDivisi();
        this.fetchStats();
    },

    beforeUnmount() {
        if (this.debounceTimer) clearTimeout(this.debounceTimer);
        this.destroyCharts();
    },

    methods: {
        async getFPerusahaanDanDivisi() {
            try {
                const res = await axios.get('/referensi/perusahaan-divisi');
                this.data_perusahaan = res.data.data || [];
            } catch (err) {
                console.error(err);
                triggerAlert(
                    'error',
                    'Gagal memuat filter perusahaan/jabatan.',
                );
            }
        },

        setupDebounce() {
            this.debouncedFetchStats = () => {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.fetchStats();
                }, 500);
            };
        },

        // Load Chart.js once, return a promise
        loadChartJS() {
            if (typeof Chart !== 'undefined') return Promise.resolve();

            if (this._chartJsPromise) return this._chartJsPromise;

            this._chartJsPromise = new Promise((resolve, reject) => {
                const existingScript =
                    document.querySelector('script[data-chartjs="1"]') ||
                    document.querySelector('script[src*="chart.min.js"]') ||
                    document.querySelector('script[src*="Chart.js"]');

                if (existingScript) {
                    // wait until Chart global exists
                    const start = Date.now();
                    const timer = setInterval(() => {
                        if (typeof Chart !== 'undefined') {
                            clearInterval(timer);
                            resolve();
                        } else if (Date.now() - start > 5000) {
                            clearInterval(timer);
                            reject(
                                new Error(
                                    'Chart.js script exists but Chart is not available',
                                ),
                            );
                        }
                    }, 50);
                    return;
                }

                const script = document.createElement('script');
                script.src =
                    'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
                script.async = true;
                script.defer = true;
                script.dataset.chartjs = '1';

                script.onload = () => resolve();
                script.onerror = () =>
                    reject(new Error('Failed to load Chart.js'));

                document.head.appendChild(script);
            });

            return this._chartJsPromise;
        },

        async fetchStats() {
            const token = ++this._fetchToken;

            // kalau UI kamu pakai v-if="loading" buat section chart,
            // ini penting supaya chart lama tidak nyangkut ke canvas yang akan di-unmount.
            this.destroyCharts();
            this.isLoading = true;

            try {
                const response = await axios.get('/dashboard/stats', {
                    params: {
                        kontrak_habis: this.filters.kontrakHabis,
                        karyawan_baru: this.filters.karyawanBaru,
                        pelamar: this.filters.pelamar,
                        resign: this.filters.resign,
                        perusahaan: this.filters.perusahaanSelected,
                    },
                });

                if (token !== this._fetchToken) return;
                if (!response.data.success) return;

                const data = response.data.data;

                this.isUpdatingFromBackend = true;

                // stats
                this.stats = data.stats || this.stats;

                // jangan replace object filters (ini bikin DOM/refs reset)
                if (data.filters) {
                    Object.assign(this.filters, data.filters);
                }

                // chart data
                this.employeeTrend = data.employeeTrend || [];
                this.employeeStatus = data.employeeStatus || [];
                this.recruitmentFunnel = data.recruitmentFunnel || [];
                this.departmentData = data.departmentData || [];
            } catch (error) {
                console.error('Error fetching stats:', error);
            } finally {
                if (token !== this._fetchToken) return;

                this.isLoading = false;

                // tunggu DOM kembali (canvas muncul lagi)
                await this.$nextTick();

                this.isUpdatingFromBackend = false;

                try {
                    await this.loadChartJS();
                } catch (e) {
                    console.error(e);
                    return;
                }

                // nextTick lagi untuk memastikan refs benar-benar terpasang
                await this.$nextTick();

                this.initializeCharts();
            }
        },

        async updateFilter(filterName, value) {
            // optional kalau kamu pakai handler manual di dropdown
            this.isUpdatingFromBackend = true;
            this.filters[filterName] = value;
            this.isUpdatingFromBackend = false;

            await this.fetchStats();
        },

        async refreshDashboard() {
            await this.fetchStats();
        },

        getMetaText(days) {
            if (days == 1) return 'hari ini';
            if (days == 7) return '7 hari terakhir';
            if (days == 30) return '30 hari terakhir';
            return '';
        },

        initializeCharts() {
            // Jika canvas belum ada (biasanya karena v-if/transition), skip saja.
            // fetchStats akan memanggil lagi setelah DOM siap.
            if (!this.$refs.employeeTrendChart) return;

            // Trend Chart
            if (this.$refs.employeeTrendChart) {
                this.charts.trend = new Chart(this.$refs.employeeTrendChart, {
                    type: 'line',
                    data: {
                        labels: this.employeeTrend.map((d) => d.bulan),
                        datasets: [
                            {
                                label: 'Karyawan Masuk',
                                data: this.employeeTrend.map((d) => d.masuk),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                            },
                            {
                                label: 'Karyawan Keluar',
                                data: this.employeeTrend.map((d) => d.keluar),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                            },
                            {
                                label: 'Total Karyawan',
                                data: this.employeeTrend.map((d) => d.total),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                        },
                        scales: {
                            y: { beginAtZero: true },
                        },
                    },
                });
            }

            // Status Chart
            if (this.$refs.employeeStatusChart) {
                this.charts.status = new Chart(this.$refs.employeeStatusChart, {
                    type: 'doughnut',
                    data: {
                        labels: this.employeeStatus.map((d) => d.name),
                        datasets: [
                            {
                                data: this.employeeStatus.map((d) => d.value),
                                backgroundColor: this.employeeStatus.map(
                                    (d) => d.color,
                                ),
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                    },
                });
            }

            // Recruitment Chart
            if (this.$refs.kontrakHampirHabisChart) {
                this.charts.recruitment = new Chart(
                    this.$refs.kontrakHampirHabisChart,
                    {
                        type: 'bar',
                        data: {
                            labels: this.recruitmentFunnel.map(
                                (d) => d.tanggal,
                            ),
                            datasets: [
                                {
                                    label: 'Kontrak Berakhir',
                                    data: this.recruitmentFunnel.map(
                                        (d) => d.jumlah,
                                    ),
                                    backgroundColor: '#f59e0b',
                                    borderColor: '#d97706',
                                    borderWidth: 1,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const count = context.parsed.y;
                                            return count + ' kontrak berakhir';
                                        },
                                    },
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        callback: function (value) {
                                            if (Number.isInteger(value))
                                                return value;
                                        },
                                    },
                                },
                            },
                        },
                    },
                );
            }

            // Department Chart
            if (this.$refs.departmentChart) {
                this.charts.department = new Chart(this.$refs.departmentChart, {
                    type: 'bar',
                    data: {
                        labels: this.departmentData.map((d) => d.dept),
                        datasets: [
                            {
                                label: 'Total Karyawan',
                                data: this.departmentData.map((d) => d.total),
                                backgroundColor: '#10b981',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: { beginAtZero: true },
                        },
                    },
                });
            }
        },

        destroyCharts() {
            Object.keys(this.charts).forEach((key) => {
                const ch = this.charts[key];
                if (ch && typeof ch.destroy === 'function') {
                    ch.destroy();
                }
            });
            this.charts = {};
        },

        // Navigation methods
        toKaryawanAktif() {
            router.visit(`/hr/karyawan`);
        },
        toKaryawanTidakAktif() {
            router.visit(`/hr/karyawan`);
        },
        toKontrakHampirHabis() {
            router.visit(`/hr/karyawan`);
        },
        toClientAktif() {
            router.visit(`/marketing/client/aktif`);
        },
        toKaryawanBaru() {
            router.visit(`/hr/karyawan`);
        },
        toKaryawanResign() {
            router.visit(`/hr/karyawan`);
        },
        toPelamarMasuk() {
            router.visit(`/hr/pelamar`);
        },
        toClientTidakAktif() {
            router.visit(`/marketing/client/non-aktif`);
        },
    },
});
</script>

<style scoped></style>
