<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">M</div>
            <span class="brand-text">TAB</span>

            <!-- <div class="logo-image-sidebar">
                <img
                    src="assets/images/logo_kecil.png"
                    alt="HRIS Logo"
                    loading="lazy"
                />
            </div> -->
        </div>

        <nav class="sidebar-nav" v-if="user.role_id == 1">
            <Link
                href="/dashboard"
                class="sidebar-item"
                :class="{ active: page.url === '/dashboard' }"
            >
                <span class="icon">🏠</span>
                <span class="label">Dashboard</span>
            </Link>

            <Link
                href="/admin/karyawan"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/admin/karyawan') }"
            >
                <span class="icon">🧑‍💼</span>
                <span class="label">Data Karyawan</span>
            </Link>

            <Link
                href="/admin/pelamar"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/admin/pelamar') }"
            >
                <span class="icon">📝</span>
                <span class="label">Data Pelamar</span>
            </Link>
            <Link
                @click="fiturBelumTersedia()"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/admin/master-client') }"
            >
                <span class="icon">🏢</span>
                <span class="label">Master Client</span>
            </Link>

            <Link
                href="/master/payroll-period/all-data"
                class="sidebar-item"
                :class="{
                    active: page.url.startsWith('/master/payroll-period'),
                }"
            >
                <span class="icon">📅</span>
                <span class="label">Master Periode Gaji</span>
            </Link>
        </nav>
        <nav class="sidebar-nav" v-if="user.role_id == 2">
            <Link
                href="/dashboard"
                class="sidebar-item"
                :class="{ active: page.url === '/dashboard' }"
            >
                <span class="icon">🏠</span>
                <span class="label">Dashboard</span>
            </Link>

            <Link
                href="/attendance"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/attendance') }"
            >
                <span class="icon">🕒</span>
                <span class="label">Absensi</span>
            </Link>

            <Link
                href="/salary"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/salary') }"
            >
                <span class="icon">🧾</span>
                <span class="label">Slip Gaji</span>
            </Link>

            <Link
                @click="fiturBelumTersedia()"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/contracts') }"
            >
                <span class="icon">📄</span>
                <span class="label">Riwayat Kontrak</span>
            </Link>

            <Link
                @click="fiturBelumTersedia()"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/warnings') }"
            >
                <span class="icon">⚠️</span>
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
        };
    },
    methods: {
        fiturBelumTersedia() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },
    },
};
</script>
