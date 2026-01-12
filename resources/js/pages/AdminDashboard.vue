<template>
    <div>
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
            <div class="overview-card success">
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

            <div class="overview-card danger">
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

            <div class="overview-card warning">
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

            <div class="overview-card info">
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
                    <canvas ref="recruitmentChart"></canvas>
                </div>
            </div>

            <!-- Karyawan per Departemen -->
            <div class="chart-card">
                <h3 class="chart-title">Karyawan per Departemen</h3>
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
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <div class="card-icon">
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
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div class="header-left">
                            <div class="card-icon">
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
import axios from 'axios';
import { defineComponent } from 'vue';

export default defineComponent({
    data() {
        return {
            stats: {
                karyawanAktif: 245,
                karyawanTidakAktif: 28,
                kontrakHampirHabis: 12,
                clientAktif: 18,
                clientTidakAktif: 7,
                karyawanBaru: 8,
                pelamarMasuk: 34,
                resign: 3,
            },
            filters: {
                kontrakHabis: 30,
                karyawanBaru: 7,
                pelamar: 7,
                resign: 7,
            },
            loading: false,
            employeeTrend: [
                { bulan: 'Jul', masuk: 12, keluar: 5, total: 230 },
                { bulan: 'Agu', masuk: 15, keluar: 8, total: 237 },
                { bulan: 'Sep', masuk: 10, keluar: 6, total: 241 },
                { bulan: 'Okt', masuk: 18, keluar: 7, total: 252 },
                { bulan: 'Nov', masuk: 9, keluar: 11, total: 250 },
                { bulan: 'Des', masuk: 8, keluar: 13, total: 245 },
            ],
            employeeStatus: [
                { name: 'Karyawan Tetap', value: 165, color: '#10b981' },
                { name: 'Kontrak', value: 58, color: '#3b82f6' },
                { name: 'Probation', value: 22, color: '#f59e0b' },
            ],
            recruitmentFunnel: [
                { tanggal: '13 Jan', jumlah: 2 },
                { tanggal: '14 Jan', jumlah: 1 },
                { tanggal: '15 Jan', jumlah: 3 },
                { tanggal: '18 Jan', jumlah: 1 },
                { tanggal: '20 Jan', jumlah: 2 },
                { tanggal: '25 Jan', jumlah: 1 },
                { tanggal: '28 Jan', jumlah: 2 },
            ],
            departmentData: [
                { dept: 'IT', total: 45 },
                { dept: 'Sales', total: 38 },
                { dept: 'Marketing', total: 28 },
                { dept: 'Finance', total: 22 },
                { dept: 'HR', total: 15 },
                { dept: 'Operations', total: 35 },
            ],
            charts: {},
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
    mounted() {
        this.fetchStats();
        this.loadChartJS();
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
        loadChartJS() {
            const script = document.createElement('script');
            script.src =
                'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
            script.onload = () => {
                this.$nextTick(() => {
                    this.initCharts();
                });
            };
            document.head.appendChild(script);
        },
        initCharts() {
            // Employee Trend Chart
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
                            legend: {
                                position: 'top',
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                            },
                        },
                    },
                });
            }

            // Employee Status Pie Chart
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
                            legend: {
                                display: false,
                            },
                        },
                    },
                });
            }

            // Kontrak Hampir Habis per Tanggal Chart
            if (this.$refs.recruitmentChart) {
                this.charts.recruitment = new Chart(
                    this.$refs.recruitmentChart,
                    {
                        type: 'bar',
                        data: {
                            labels: this.recruitmentFunnel.map(
                                (d) => d.tanggal,
                            ),
                            datasets: [
                                {
                                    label: 'Jumlah Karyawan',
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
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return (
                                                context.parsed.y + ' karyawan'
                                            );
                                        },
                                    },
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
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
                            legend: {
                                display: false,
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                            },
                        },
                    },
                });
            }
        },
    },
    beforeUnmount() {
        Object.values(this.charts).forEach((chart) => {
            if (chart) chart.destroy();
        });
    },
});
</script>

<style scoped></style>
