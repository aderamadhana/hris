<script setup>
import { triggerAlert } from '@/utils/alert';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user);

// state open/close
const masterOpen = ref(false);
const dataMenuOpen = ref(false);
const dataLogsOpen = ref(false);

// route helpers
const url = computed(() => page.url);

const isMasterActive = computed(() => url.value.startsWith('/master'));

const isDataActive = computed(() => {
    return (
        url.value.startsWith('/karyawan/all-karyawan') ||
        url.value.startsWith('/pelamar/all-pelamar')
    );
});

const isLogActive = computed(() => {
    // khusus halaman log saja
    return url.value.startsWith('/logs');
});

// sinkronisasi open state saat route berubah
const syncOpenStates = () => {
    if (isMasterActive.value) masterOpen.value = true;
    if (isDataActive.value) dataMenuOpen.value = true;
    if (isLogActive.value) dataLogsOpen.value = true;

    // opsional: kalau mau auto-close saat pindah halaman, aktifkan ini:
    if (!isMasterActive.value) masterOpen.value = false;
    if (!isDataActive.value) dataMenuOpen.value = false;
    if (!isLogActive.value) dataLogsOpen.value = false;
};

syncOpenStates();
watch(url, syncOpenStates);

// actions
const toggleMaster = () => (masterOpen.value = !masterOpen.value);
const toggleDataMenu = () => (dataMenuOpen.value = !dataMenuOpen.value);
const toggleLog = () => (dataLogsOpen.value = !dataLogsOpen.value);

const fiturBelumTersedia = () => {
    triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
};
</script>

<template>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">M</div>
            <span class="brand-text">TAB</span>
        </div>

        <!-- ADMIN -->
        <nav class="sidebar-nav" v-if="user?.role_id == 1">
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
                    <span class="label">Human Resource</span>
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
                        href="/master/client/all"
                        class="sidebar-subitem"
                        :class="{
                            active: page.url.startsWith('/master/client'),
                        }"
                    >
                        <span>Marketing</span>
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
                        <span>Payroll</span>
                    </Link>
                </div>
            </div>

            <!-- LOG (collapsible) -->
            <div class="sidebar-group" :class="{ open: dataLogsOpen }">
                <button
                    type="button"
                    class="sidebar-item sidebar-item--toggle"
                    :class="{ active: isLogActive }"
                    @click="toggleLog"
                >
                    <font-awesome-icon icon="clock-rotate-left" class="icon" />
                    <span class="label">Log</span>
                    <span class="caret" :class="{ open: dataLogsOpen }">▾</span>
                </button>

                <!-- FIX: v-show harus dataLogsOpen, bukan masterOpen -->
                <div v-show="dataLogsOpen" class="sidebar-submenu">
                    <Link
                        href="/logs/presensi/all"
                        class="sidebar-subitem"
                        :class="{
                            active: page.url.startsWith('/logs/presensi'),
                        }"
                    >
                        <span>Presensi</span>
                    </Link>
                </div>
            </div>
        </nav>

        <!-- KARYAWAN -->
        <nav class="sidebar-nav" v-if="user?.role_id == 2">
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

            <Link
                href="#"
                class="sidebar-item"
                @click.prevent="fiturBelumTersedia"
            >
                <font-awesome-icon icon="file-contract" class="icon" />
                <span class="label">Riwayat Kontrak</span>
            </Link>

            <Link
                href="#"
                class="sidebar-item"
                @click.prevent="fiturBelumTersedia"
            >
                <font-awesome-icon icon="triangle-exclamation" class="icon" />
                <span class="label">Surat Peringatan</span>
            </Link>
        </nav>
    </aside>
</template>
