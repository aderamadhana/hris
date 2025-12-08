<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">H</div>
            <span class="brand-text">HRIS</span>
        </div>

        <nav class="sidebar-nav">
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
