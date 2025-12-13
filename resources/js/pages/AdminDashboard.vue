<template>
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard Admin</h1>
        </div>
    </div>
    <div class="dashboard-grid">
        <!-- TOTAL KARYAWAN TIDAK AKTIF -->
        <div class="dashboard-card danger">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">Karyawan Tidak Aktif</div>
                <span class="dashboard-card-badge">Total</span>
            </div>
            <div class="dashboard-value">{{ stats.karyawanTidakAktif }}</div>
            <div class="dashboard-meta">
                PHK · Habis kontrak · Non-aktif sistem
            </div>
        </div>

        <!-- KONTRAK HAMPIR HABIS -->
        <div class="dashboard-card warning">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">Kontrak Hampir Habis</div>
                <select
                    class="dashboard-card-dropdown"
                    v-model="filters.kontrakHabis"
                    @change="fetchStats"
                >
                    <option value="7">Urgent (&lt; 7 hari)</option>
                    <option value="30">&lt; 30 hari</option>
                </select>
            </div>
            <div class="dashboard-value">{{ stats.kontrakHampirHabis }}</div>
            <div class="dashboard-meta">
                Perlu keputusan perpanjangan / offboarding
            </div>
        </div>

        <!-- TOTAL CLIENT AKTIF -->
        <div class="dashboard-card success">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">Client Aktif</div>
                <span class="dashboard-card-badge">Berjalan</span>
            </div>
            <div class="dashboard-value">{{ stats.clientAktif }}</div>
            <div class="dashboard-meta">Kontrak aktif & ongoing</div>
        </div>

        <!-- TOTAL CLIENT TIDAK AKTIF -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">Client Tidak Aktif</div>
                <span class="dashboard-card-badge">Closed</span>
            </div>
            <div class="dashboard-value">{{ stats.clientTidakAktif }}</div>
            <div class="dashboard-meta">
                Selesai · Putus · Tidak diperpanjang
            </div>
        </div>

        <!-- KARYAWAN BARU HARI INI -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">Karyawan Baru</div>
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
            <div class="dashboard-value">{{ stats.karyawanBaru }}</div>
            <div class="dashboard-meta">
                Onboarding {{ getMetaText(filters.karyawanBaru) }}
            </div>
        </div>

        <!-- PELAMAR HARI INI -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">Pelamar Masuk</div>
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
            <div class="dashboard-value">{{ stats.pelamarMasuk }}</div>
            <div class="dashboard-meta">Perlu screening HR</div>
        </div>

        <!-- KARYAWAN RESIGN HARI INI -->
        <div class="dashboard-card danger">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">Resign</div>
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
            <div class="dashboard-value">{{ stats.resign }}</div>
            <div class="dashboard-meta">Perlu exit clearance</div>
        </div>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';
import { defineComponent, reactive } from 'vue';

export default defineComponent({
    props: {
        initialStats: {
            type: Object,
            required: true,
        },
    },

    setup(props) {
        // State untuk stats
        const stats = reactive({
            karyawanTidakAktif: 0,
            kontrakHampirHabis: 0,
            clientAktif: 0,
            clientTidakAktif: 0,
            karyawanBaru: 0,
            pelamarMasuk: 0,
            resign: 0,
        });

        // State untuk filters
        const filters = reactive({
            kontrakHabis: '30',
            karyawanBaru: '1',
            pelamar: '1',
            resign: '1',
        });

        // Fetch stats dengan filter
        const fetchStats = () => {
            router.get(route('dashboard.stats'), filters, {
                preserveState: true,
                preserveScroll: true,
                only: ['stats'],
                onSuccess: (page) => {
                    if (page.props.stats) {
                        Object.assign(stats, page.props.stats);
                    }
                },
            });
        };

        // Helper untuk meta text
        const getMetaText = (days) => {
            if (days === '1') return 'hari ini';
            if (days === '7') return '7 hari terakhir';
            return '30 hari terakhir';
        };

        return {
            stats,
            filters,
            fetchStats,
            getMetaText,
        };
    },
});
</script>
