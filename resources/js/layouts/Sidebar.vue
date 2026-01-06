<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">M</div>
            <span class="brand-text">TAB</span>
        </div>

        <!-- ADMIN -->
        <nav class="sidebar-nav" v-if="user.role_id == 1">
            <Link
                href="/dashboard"
                class="sidebar-item"
                :class="{ active: page.url === '/dashboard' }"
            >
                <font-awesome-icon icon="house" class="icon" />
                <span class="label">Dashboard</span>
            </Link>

            <!-- DATA (collapsible) -->
            <div class="sidebar-group" :class="{ open: dataMenuOpen }">
                <button
                    type="button"
                    class="sidebar-item sidebar-item--toggle"
                    :class="{ active: isDataActive }"
                    @click="toggleDataMenu"
                >
                    <font-awesome-icon icon="folder-open" class="icon" />
                    <span class="label">Data</span>
                    <span class="caret" :class="{ open: dataMenuOpen }">▾</span>
                </button>

                <div v-show="dataMenuOpen" class="sidebar-submenu">
                    <Link
                        href="/karyawan/all-karyawan"
                        class="sidebar-subitem"
                        :class="{
                            active: page.url.startsWith(
                                '/karyawan/all-karyawan',
                            ),
                        }"
                    >
                        <span>Karyawan</span>
                    </Link>

                    <Link
                        href="/pelamar/all-pelamar"
                        class="sidebar-subitem"
                        :class="{
                            active: page.url.startsWith('/pelamar/all-pelamar'),
                        }"
                    >
                        <span>Pelamar</span>
                    </Link>
                </div>
            </div>

            <!-- MASTER (collapsible) -->
            <div class="sidebar-group" :class="{ open: masterOpen }">
                <button
                    type="button"
                    class="sidebar-item sidebar-item--toggle"
                    :class="{ active: isMasterActive }"
                    @click="toggleMaster"
                >
                    <font-awesome-icon icon="layer-group" class="icon" />
                    <span class="label">Master</span>
                    <span class="caret" :class="{ open: masterOpen }">▾</span>
                </button>

                <div v-show="masterOpen" class="sidebar-submenu">
                    <Link
                        href="#"
                        class="sidebar-subitem"
                        @click.prevent="fiturBelumTersedia()"
                    >
                        <span>Client</span>
                    </Link>

                    <Link
                        href="/master/payroll-period/all-data"
                        class="sidebar-subitem"
                        :class="{
                            active: page.url.startsWith(
                                '/master/payroll-period',
                            ),
                        }"
                    >
                        <span>Periode Gaji</span>
                    </Link>
                </div>
            </div>
        </nav>

        <!-- KARYAWAN -->
        <nav class="sidebar-nav" v-if="user.role_id == 2">
            <Link
                href="/dashboard"
                class="sidebar-item"
                :class="{ active: page.url === '/dashboard' }"
            >
                <font-awesome-icon icon="house" class="icon" />
                <span class="label">Dashboard</span>
            </Link>

            <Link
                href="/attendance"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/attendance') }"
            >
                <font-awesome-icon icon="clock" class="icon" />
                <span class="label">Absensi</span>
            </Link>

            <Link
                href="/salary"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/salary') }"
            >
                <font-awesome-icon icon="file-invoice-dollar" class="icon" />
                <span class="label">Slip Gaji</span>
            </Link>

            <Link @click="fiturBelumTersedia()" class="sidebar-item">
                <font-awesome-icon icon="file-contract" class="icon" />
                <span class="label">Riwayat Kontrak</span>
            </Link>

            <Link @click="fiturBelumTersedia()" class="sidebar-item">
                <font-awesome-icon icon="triangle-exclamation" class="icon" />
                <span class="label">Surat Peringatan</span>
            </Link>
        </nav>
    </aside>
</template>
<script>
import { triggerAlert } from '@/utils/alert';

export default {
    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,
            masterOpen: false,
            dataMenuOpen: false,
        };
    },
    computed: {
        page() {
            return this.$page;
        },

        isMasterActive() {
            return this.page.url.startsWith('/master');
        },
        isDataActive() {
            return (
                this.page.url.startsWith('/karyawan/all-karyawan') ||
                this.page.url.startsWith('/pelamar/all-pelamar')
            );
        },
    },
    watch: {
        'page.url'() {
            this.masterOpen = this.isMasterActive;
        },
    },
    mounted() {
        // auto-open kalau sedang di halaman master
        if (this.isMasterActive) this.masterOpen = true;
        if (this.isDataActive) this.dataMenuOpen = true;
    },
    methods: {
        toggleMaster() {
            this.masterOpen = !this.masterOpen;
        },

        toggleDataMenu() {
            this.dataMenuOpen = !this.dataMenuOpen;
        },
        fiturBelumTersedia() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },
    },
};
</script>
