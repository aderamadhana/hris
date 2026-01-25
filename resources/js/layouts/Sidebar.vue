<script setup>
import { triggerAlert } from '@/utils/alert';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

// pakai path tanpa query biar active-state tidak ke-distract ?page=...
const path = computed(() => (page.url ?? '').split('?')[0]);

// open/close state per group
const hrOpen = ref(false);
const marketingOpen = ref(false);
const logOpen = ref(false);
const insuranceOpen = ref(false);
const berandaOpen = ref(false);

// ACTIVE matchers (ubah prefix sesuai routing project kamu)
const isHrActive = computed(() => {
    return (
        path.value.startsWith('/hr/karyawan') || // ✅ tambah ini
        path.value.startsWith('/hr/pelamar') || // kalau ada
        path.value.startsWith('/karyawan') || // kalau masih dipakai
        path.value.startsWith('/pelamar') || // kalau masih dipakai
        path.value.startsWith('/hr/payroll') ||
        path.value.startsWith('/hr/surat-peringatan') ||
        path.value.startsWith('/hr/shift')
    );
});

const isMarketingActive = computed(() => {
    return (
        path.value.startsWith('/master/client') ||
        path.value.startsWith('/marketing')
    );
});

const isLogActive = computed(() => {
    return path.value.startsWith('/logs');
});

const isInsuranceActive = computed(() => {
    return (
        path.value.startsWith('/asuransi') ||
        path.value.startsWith('/insurance') ||
        path.value.startsWith('/bpjs')
    );
});

const isBerandaActive = computed(() => {
    return (
        path.value.startsWith('/beranda/lowongan-kerja') ||
        path.value.startsWith('/beranda/pengumuman')
    );
});

// helper: tutup semua group
const closeAllGroups = () => {
    hrOpen.value = false;
    marketingOpen.value = false;
    logOpen.value = false;
    insuranceOpen.value = false;
    berandaOpen.value = false;
};

// sinkronisasi open state saat route berubah (hanya 1 yang boleh open)
const syncOpenStates = () => {
    closeAllGroups();

    if (isHrActive.value) hrOpen.value = true;
    else if (isMarketingActive.value) marketingOpen.value = true;
    else if (isLogActive.value) logOpen.value = true;
    else if (isInsuranceActive.value) insuranceOpen.value = true;
    else if (isBerandaActive.value) berandaOpen.value = true;
};

syncOpenStates();
watch(path, syncOpenStates);

// actions (accordion: toggle satu, lainnya auto close)
const toggleHr = () => {
    const next = !hrOpen.value;
    closeAllGroups();
    hrOpen.value = next;
};

const toggleMarketing = () => {
    const next = !marketingOpen.value;
    closeAllGroups();
    marketingOpen.value = next;
};

const toggleLog = () => {
    const next = !logOpen.value;
    closeAllGroups();
    logOpen.value = next;
};

const toggleInsurance = () => {
    const next = !insuranceOpen.value;
    closeAllGroups();
    insuranceOpen.value = next;
};

const toggleBeranda = () => {
    const next = !berandaOpen.value;
    closeAllGroups();
    berandaOpen.value = next;
};
const fiturBelumTersedia = () => {
    triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
};
</script>

<template>
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-title">Menu</span>
        </div>

        <!-- ADMIN -->
        <nav class="sidebar-nav" v-if="user?.role_id == 1">
            <!-- Dashboard -->
            <Link
                href="/dashboard"
                class="sidebar-item"
                :class="{ active: path === '/dashboard' }"
            >
                <font-awesome-icon icon="tachometer-alt" class="icon" />
                <span class="label">Dashboard</span>
            </Link>

            <!-- Human Resource -->
            <div class="sidebar-group" :class="{ open: hrOpen }">
                <button
                    type="button"
                    class="sidebar-item sidebar-item--toggle"
                    :class="{ active: isHrActive }"
                    @click="toggleHr"
                >
                    <font-awesome-icon icon="folder-open" class="icon" />
                    <span class="label">Human Resource</span>
                    <span class="caret" :class="{ open: hrOpen }">▾</span>
                </button>

                <transition name="sb-collapse">
                    <div v-show="hrOpen" class="sidebar-submenu">
                        <Link
                            href="/hr/karyawan"
                            class="sidebar-subitem"
                            :class="{ active: path.startsWith('/hr/karyawan') }"
                        >
                            <span>Karyawan</span>
                        </Link>

                        <Link
                            href="/hr/pelamar"
                            class="sidebar-subitem"
                            :class="{ active: path.startsWith('/hr/pelamar') }"
                        >
                            <span>Pelamar</span>
                        </Link>

                        <Link
                            href="/hr/payroll"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith('/hr/payroll'),
                            }"
                        >
                            <span>Payroll</span>
                        </Link>

                        <!-- <Link
                            href="/hr/surat-peringatan"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith('/hr/surat-peringatan'),
                            }"
                        >
                            <span>Surat Peringatan</span>
                        </Link> -->
                        <Link
                            href="/hr/shift"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith('/hr/shift'),
                            }"
                        >
                            <span>Shift</span>
                        </Link>
                    </div>
                </transition>
            </div>

            <!-- Marketing -->
            <div class="sidebar-group" :class="{ open: marketingOpen }">
                <button
                    type="button"
                    class="sidebar-item sidebar-item--toggle"
                    :class="{ active: isMarketingActive }"
                    @click="toggleMarketing"
                >
                    <font-awesome-icon icon="layer-group" class="icon" />
                    <span class="label">Marketing</span>
                    <span class="caret" :class="{ open: marketingOpen }"
                        >▾</span
                    >
                </button>

                <transition name="sb-collapse">
                    <div v-show="marketingOpen" class="sidebar-submenu">
                        <Link
                            href="/marketing/client/aktif"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith(
                                    '/marketing/client/aktif',
                                ),
                            }"
                        >
                            <span>Client</span>
                        </Link>
                    </div>
                </transition>
            </div>

            <!-- Log Data -->
            <div class="sidebar-group" :class="{ open: logOpen }">
                <button
                    type="button"
                    class="sidebar-item sidebar-item--toggle"
                    :class="{ active: isLogActive }"
                    @click="toggleLog"
                >
                    <font-awesome-icon icon="clock-rotate-left" class="icon" />
                    <span class="label">Log Data</span>
                    <span class="caret" :class="{ open: logOpen }">▾</span>
                </button>

                <transition name="sb-collapse">
                    <div v-show="logOpen" class="sidebar-submenu">
                        <Link
                            href="/logs/presensi"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith('/logs/presensi'),
                            }"
                        >
                            <span>Data Presensi</span>
                        </Link>

                        <Link
                            href="/logs/aktivitas"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith('/logs/aktivitas'),
                            }"
                        >
                            <span>Data Aktivitas</span>
                        </Link>
                    </div>
                </transition>
            </div>

            <!-- Asuransi -->
            <!-- <div class="sidebar-group" :class="{ open: insuranceOpen }">
                <button
                    type="button"
                    class="sidebar-item sidebar-item--toggle"
                    :class="{ active: isInsuranceActive }"
                    @click="toggleInsurance"
                >
                    <font-awesome-icon icon="shield-halved" class="icon" />
                    <span class="label">Asuransi</span>
                    <span class="caret" :class="{ open: insuranceOpen }"
                        >▾</span
                    >
                </button>

                <transition name="sb-collapse">
                    <div v-show="insuranceOpen" class="sidebar-submenu">
                        <Link
                            href="/asuransi/bpjs-kesehatan"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith(
                                    '/asuransi/bpjs-kesehatan',
                                ),
                            }"
                        >
                            <span>BPJS Kesehatan</span>
                        </Link>

                        <Link
                            href="/asuransi/bpjs-ketenagakerjaan"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith(
                                    '/asuransi/bpjs-ketenagakerjaan',
                                ),
                            }"
                        >
                            <span>BPJS Ketenagakerjaan</span>
                        </Link>

                        <Link
                            href="/asuransi/kecelakaan-kerja"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith(
                                    '/asuransi/kecelakaan-kerja',
                                ),
                            }"
                        >
                            <span>Input Kecelakaan Kerja</span>
                        </Link>
                    </div>
                </transition>
            </div> -->

            <!-- Asuransi -->
            <div class="sidebar-group" :class="{ open: berandaOpen }">
                <button
                    type="button"
                    class="sidebar-item sidebar-item--toggle"
                    :class="{ active: isBerandaActive }"
                    @click="toggleBeranda"
                >
                    <font-awesome-icon icon="home" class="icon" />
                    <span class="label">Beranda</span>
                    <span class="caret" :class="{ open: berandaOpen }">▾</span>
                </button>

                <transition name="sb-collapse">
                    <div v-show="berandaOpen" class="sidebar-submenu">
                        <Link
                            href="/beranda/lowongan-kerja"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith(
                                    '/beranda/lowongan-kerja',
                                ),
                            }"
                        >
                            <span>Lowongan Kerja</span>
                        </Link>

                        <Link
                            href="/beranda/pengumuman"
                            class="sidebar-subitem"
                            :class="{
                                active: path.startsWith('/beranda/pengumuman'),
                            }"
                        >
                            <span>Pengumuman</span>
                        </Link>
                    </div>
                </transition>
            </div>

            <!-- <Link
                href="/konfigurasi"
                class="sidebar-item"
                :class="{ active: path === '/konfigurasi' }"
            >
                <font-awesome-icon icon="cog" class="icon" />
                <span class="label">Konfigurasi</span>
            </Link> -->
        </nav>

        <!-- KARYAWAN -->
        <nav class="sidebar-nav" v-if="user?.role_id == 2">
            <Link
                href="/dashboard"
                class="sidebar-item"
                :class="{ active: path === '/dashboard' }"
            >
                <font-awesome-icon icon="tachometer-alt" class="icon" />
                <span class="label">Dashboard</span>
            </Link>

            <Link
                href="/attendance"
                class="sidebar-item"
                :class="{ active: path.startsWith('/attendance') }"
            >
                <font-awesome-icon icon="clock" class="icon" />
                <span class="label">Absensi</span>
            </Link>

            <Link
                href="/salary"
                class="sidebar-item"
                :class="{ active: path.startsWith('/salary') }"
            >
                <font-awesome-icon icon="file-invoice-dollar" class="icon" />
                <span class="label">Slip Gaji</span>
            </Link>

            <Link
                href="/riwayat-kontrak"
                class="sidebar-item"
                :class="{ active: path.startsWith('/riwayat-kontrak') }"
            >
                <font-awesome-icon icon="file-contract" class="icon" />
                <span class="label">Riwayat Kontrak</span>
            </Link>

            <!-- <Link
                href="/surat-peringatan"
                class="sidebar-item"
                :class="{ active: path.startsWith('/surat-peringatan') }"
            >
                <font-awesome-icon icon="triangle-exclamation" class="icon" />
                <span class="label">Surat Peringatan</span>
            </Link> -->
        </nav>
    </aside>
</template>
