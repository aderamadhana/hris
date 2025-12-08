<template>
    <div class="min-h-screen">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <nav class="nav">
                    <Link href="/" class="logo">
                        <span class="logo-icon">◈</span>
                        <span class="logo-text">Aplikasi</span>
                    </Link>

                    <div class="nav-links">
                        <Link
                            href="/"
                            class="nav-link"
                            :class="{ active: $page.url === '/' }"
                        >
                            Beranda
                        </Link>
                        <Link
                            href="/about"
                            class="nav-link"
                            :class="{ active: $page.url === '/about' }"
                        >
                            Tentang
                        </Link>
                        <Link
                            href="/contact"
                            class="nav-link"
                            :class="{ active: $page.url === '/contact' }"
                        >
                            Kontak
                        </Link>
                    </div>

                    <button @click="toggleMenu" class="mobile-menu-btn">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </nav>

                <!-- Mobile Menu -->
                <div v-if="mobileMenuOpen" class="mobile-menu">
                    <Link href="/" class="mobile-link" @click="toggleMenu"
                        >Beranda</Link
                    >
                    <Link href="/about" class="mobile-link" @click="toggleMenu"
                        >Tentang</Link
                    >
                    <Link
                        href="/contact"
                        class="mobile-link"
                        @click="toggleMenu"
                        >Kontak</Link
                    >
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-section">
                        <h3>Tentang Kami</h3>
                        <p>
                            Solusi digital yang elegan dan profesional untuk
                            bisnis Anda.
                        </p>
                    </div>
                    <div class="footer-section">
                        <h3>Kontak</h3>
                        <p>Email: info@example.com</p>
                        <p>Telp: (021) 1234-5678</p>
                    </div>
                    <div class="footer-section">
                        <h3>Ikuti Kami</h3>
                        <div class="social-links">
                            <a href="#" class="social-link">Facebook</a>
                            <a href="#" class="social-link">Instagram</a>
                            <a href="#" class="social-link">LinkedIn</a>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2024 Aplikasi. Hak Cipta Dilindungi.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const mobileMenuOpen = ref(false);

const toggleMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};
</script>
