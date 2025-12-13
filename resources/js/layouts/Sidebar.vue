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
                href="/admin/users"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/admin') }"
            >
                <span class="icon">👥</span>
                <span class="label">Karyawan</span>
            </Link>

            <Link
                href="/admin/users"
                class="sidebar-item"
                :class="{ active: page.url.startsWith('/admin') }"
            >
                <span class="icon">👥</span>
                <span class="label">Pelamar</span>
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
                :class="{ active: page.url === '/attendance' }"
            >
                <span class="icon">📷</span>
                <span class="label">Absensi</span>
            </Link>

            <Link
                href="/salary"
                class="sidebar-item"
                :class="{ active: page.url === '/salary' }"
            >
                <span class="icon">💰</span>
                <span class="label">Slip Gaji</span>
            </Link>

            <Link
                href="/salary"
                class="sidebar-item"
                :class="{ active: page.url === '/salary' }"
            >
                <span class="icon">💰</span>
                <span class="label">Riwayat Kontrak</span>
            </Link>

            <Link
                href="/salary"
                class="sidebar-item"
                :class="{ active: page.url === '/salary' }"
            >
                <span class="icon">💰</span>
                <span class="label">SP</span>
            </Link>
        </nav>
    </aside>
</template>
<script>
export default {
    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,
        };
    },
};
</script>
