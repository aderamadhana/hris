<template>
    <div class="app">
        <Header @toggle-sidebar="toggleSidebar" />

        <div class="body">
            <!-- WRAPPER yang ditoggle -->
            <div class="sidebar-shell" :class="{ 'is-open': sidebarOpen }">
                <Sidebar />
            </div>

            <!-- BACKDROP -->
            <div
                v-if="sidebarOpen"
                class="sidebar-backdrop"
                @click="closeSidebar"
            />

            <Content class="main">
                <slot />
            </Content>
        </div>

        <AlertPopup />
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

import AlertPopup from '@/components/AlertPopup.vue';
import Content from './Content.vue';
import Header from './Header.vue';
import Sidebar from './Sidebar.vue';

const sidebarOpen = ref(false);
const page = usePage();

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
const closeSidebar = () => {
    sidebarOpen.value = false;
};

// auto close saat navigasi inertia berubah URL
watch(
    () => page.url,
    () => closeSidebar(),
);
</script>

<style>
/* full height */
.app {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.body {
    flex: 1;
    display: flex;
    min-height: 0;
}

.main {
    flex: 1;
    min-width: 0;
}

.sidebar-shell {
    flex: 0 0 auto;
}
/* Drawer aktif untuk <= 768px */
@media (max-width: 768px) {
    .sidebar-shell {
        position: fixed;
        top: 64px;
        left: 0;
        height: calc(100dvh - 64px);
        z-index: 1000;

        transform: translateX(-110%);
        transition: transform 200ms ease;

        /* default width (akan dioverride breakpoint lebih spesifik di bawah) */
        width: 240px;
    }

    .sidebar-shell.is-open {
        transform: translateX(0);
    }

    .sidebar-shell .sidebar {
        width: 100%;
        height: 100%;
    }

    .sidebar-backdrop {
        position: fixed;
        top: 64px;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(2, 6, 23, 0.45);
        z-index: 900;
    }
}

/* HP kecil (<= 420px) */
@media (max-width: 420px) {
    .sidebar-shell {
        width: clamp(180px, 68vw, 220px);
    }
}

/* HP normal (421px – 640px) */
@media (min-width: 421px) and (max-width: 640px) {
    .sidebar-shell {
        width: clamp(200px, 60vw, 240px);
    }
}

/* Tablet kecil (641px – 768px) */
@media (min-width: 641px) and (max-width: 768px) {
    .sidebar-shell {
        width: 240px; /* konsisten */
    }
}
</style>
