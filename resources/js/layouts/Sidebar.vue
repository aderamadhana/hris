<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">H</div>
            <span class="brand-text">HRIS</span>

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
                <span class="label">Users</span>
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
                <span class="label">Payroll</span>
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
