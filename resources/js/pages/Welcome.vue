<template>
    <div>
        <!-- NAVBAR -->
        <header class="header">
            <div class="nav container">
                <a href="#beranda" class="brand" @click="setActive('#beranda')">
                    <img
                        :src="companyLogo"
                        alt="PT Mitra Wira Mas"
                        class="brand__logo"
                    />
                </a>

                <nav class="nav__menu">
                    <a
                        v-for="link in navLinks"
                        :key="link.hash"
                        :href="link.hash"
                        :class="[
                            'nav__link',
                            activeHash === link.hash && 'is-active',
                        ]"
                        @click="setActive(link.hash)"
                    >
                        {{ link.label }}
                    </a>
                </nav>

                <a class="btn btn--gold btn--sm" @click="masukAplikasi">
                    Masuk Aplikasi
                </a>
            </div>
        </header>

        <main class="page">
            <!-- ✅ BANNER FULL HEIGHT -->
            <section
                id="beranda"
                class="banner reveal"
                style="--d: 0"
                :style="bannerStyle"
            >
                <div class="container">
                    <div class="banner__inner">
                        <span class="banner__kicker">OUTSOURCING & SDM</span>
                        <h1 class="banner__title">
                            Solusi SDM untuk Operasional Perusahaan
                        </h1>
                        <p class="banner__desc">
                            PT Mitra Wira Mas membantu penyediaan tenaga kerja
                            profesional dengan proses rekrutmen, penempatan, dan
                            administrasi yang tertata.
                        </p>

                        <div class="banner__actions">
                            <a
                                class="btn btn--gold"
                                href="#pengumuman"
                                @click="setActive('#pengumuman')"
                            >
                                <font-awesome-icon icon="bullhorn" />
                                Pengumuman
                            </a>

                            <a
                                class="btn btn--outline"
                                href="#kontak"
                                @click="setActive('#kontak')"
                            >
                                <font-awesome-icon icon="phone" />
                                Kontak
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONTENT -->
            <div class="container">
                <!-- SEJARAH + FOTO PENDIRI -->
                <section id="sejarah" class="row row--two">
                    <div class="reveal block" style="--d: 60">
                        <div class="block__head">
                            <div>
                                <h2 class="title">
                                    Sejarah Perusahaan & Nama Pendiri
                                </h2>
                                <p class="sub">
                                    Simple, singkat, dan fokus ke kredibilitas.
                                </p>
                            </div>
                        </div>

                        <div class="content">
                            <p
                                style="
                                    margin: 0;
                                    color: rgba(15, 23, 42, 0.72);
                                    font-weight: 600;
                                "
                            >
                                PT Mitra Wira Mas berdiri untuk menjawab
                                kebutuhan perusahaan akan SDM yang siap kerja,
                                disiplin, dan sesuai SOP.
                            </p>

                            <ul class="mini-list">
                                <li>
                                    <font-awesome-icon icon="check" />
                                    <div>
                                        <strong>Pendiri: Nama Pendiri</strong>
                                        <span
                                            >Berfokus pada kualitas SDM dan SOP
                                            kerja.</span
                                        >
                                    </div>
                                </li>

                                <li>
                                    <font-awesome-icon icon="check" />
                                    <div>
                                        <strong>Penempatan</strong>
                                        <span
                                            >Area Jabodetabek dan
                                            sekitarnya.</span
                                        >
                                    </div>
                                </li>

                                <li>
                                    <font-awesome-icon icon="check" />
                                    <div>
                                        <strong>Komitmen</strong>
                                        <span
                                            >Administrasi rapi dan kontrol
                                            rutin.</span
                                        >
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="photo reveal block" style="--d: 120">
                        <div class="photo__wrap">
                            <img
                                :src="founderImage"
                                alt="Foto Pendiri"
                                load="lazy"
                            />
                        </div>
                        <div class="photo__cap">
                            <strong>Nama Pendiri</strong>
                            <span>Founder / Direktur</span>
                        </div>
                    </div>
                </section>

                <!-- MITRA -->
                <section id="mitra" class="row">
                    <div class="reveal block" style="--d: 90">
                        <div class="block__head">
                            <div>
                                <h2 class="title">
                                    Logo Perusahaan yang Bekerja Sama
                                </h2>
                                <p class="sub">
                                    Isi logo mitra/klien untuk meningkatkan
                                    trust.
                                </p>
                            </div>
                        </div>

                        <div class="content">
                            <div class="logos">
                                <div
                                    class="logo"
                                    v-for="(logo, i) in mitraLogos"
                                    :key="i"
                                >
                                    <img :src="logo" :alt="`Mitra ${i + 1}`" />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- PENGUMUMAN + LOKER -->
                <section class="row row--split">
                    <div id="pengumuman" class="reveal block" style="--d: 90">
                        <div class="block__head">
                            <div>
                                <h2 class="title">Pengumuman</h2>
                                <p class="sub">Ringkas dan gampang dibaca.</p>
                            </div>
                        </div>

                        <div class="content">
                            <div class="note">
                                <div
                                    class="note__item"
                                    v-for="(n, i) in notes"
                                    :key="i"
                                >
                                    <div class="note__top">
                                        <span class="badge">{{ n.badge }}</span>
                                        <span class="date">{{ n.date }}</span>
                                    </div>
                                    <p class="note__title">{{ n.title }}</p>
                                    <p class="note__desc">{{ n.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="loker" class="reveal block" style="--d: 140">
                        <div class="block__head">
                            <div>
                                <h2 class="title">Loker</h2>
                                <p class="sub">Lowongan kerja yang tersedia.</p>
                            </div>
                        </div>

                        <div class="content">
                            <div class="jobs">
                                <article
                                    class="job"
                                    v-for="(j, i) in jobs"
                                    :key="i"
                                >
                                    <div class="job__top">
                                        <h3 class="job__title">
                                            {{ j.title }}
                                        </h3>
                                        <span class="job__type">{{
                                            j.type
                                        }}</span>
                                    </div>

                                    <div class="job__meta">
                                        <span>
                                            <font-awesome-icon
                                                icon="location-dot"
                                            />
                                            {{ j.location }}
                                        </span>

                                        <span>
                                            <font-awesome-icon icon="clock" />
                                            {{ j.shift }}
                                        </span>
                                    </div>

                                    <div class="job__actions">
                                        <a
                                            href="#"
                                            class="btn btn--gold btn--sm"
                                            @click.prevent="
                                                openDetailLoker(j.slug)
                                            "
                                        >
                                            <font-awesome-icon
                                                icon="paper-plane"
                                            />
                                            Apply
                                        </a>

                                        <a
                                            href="#"
                                            class="btn btn--dark btn--sm"
                                            @click.prevent="
                                                openDetailLoker(j.slug)
                                            "
                                        >
                                            <font-awesome-icon
                                                icon="circle-info"
                                            />
                                            Detail
                                        </a>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ✅ FOOTER -->
            <footer class="footer" id="kontak">
                <div class="footer__top container">
                    <div class="footer__grid">
                        <div class="footer__brand">
                            <div class="footer__brandRow">
                                <img
                                    :src="companyLogo"
                                    alt="PT Mitra Wira Mas"
                                    class="footer__logoImg"
                                />
                            </div>
                            <p class="footer__desc">
                                Penyedia SDM untuk operasional perusahaan dengan
                                proses rekrutmen, penempatan, dan administrasi
                                yang tertata.
                            </p>
                        </div>

                        <div class="footer__col">
                            <h4>Kontak</h4>

                            <div class="footer__contactItem">
                                <font-awesome-icon icon="location-dot" />
                                <span>Jl. Contoh Alamat No. 10, Jakarta</span>
                            </div>

                            <div class="footer__contactItem">
                                <font-awesome-icon icon="phone" />
                                <span>+62 812-0000-0000</span>
                            </div>

                            <div class="footer__contactItem">
                                <font-awesome-icon icon="envelope" />
                                <span>info@mitrawiramas.co.id</span>
                            </div>
                        </div>

                        <div class="footer__col">
                            <h4>Menu</h4>
                            <a href="#beranda" @click="setActive('#beranda')"
                                >Beranda</a
                            >
                            <a href="#sejarah" @click="setActive('#sejarah')"
                                >Sejarah</a
                            >
                            <a href="#mitra" @click="setActive('#mitra')"
                                >Mitra</a
                            >
                            <a
                                href="#pengumuman"
                                @click="setActive('#pengumuman')"
                                >Pengumuman</a
                            >
                            <a href="#loker" @click="setActive('#loker')"
                                >Loker</a
                            >
                        </div>

                        <div class="footer__col">
                            <h4>Sosial</h4>
                            <p
                                style="
                                    margin: 0 0 10px;
                                    color: rgba(255, 255, 255, 0.72);
                                    font-weight: 600;
                                    font-size: 0.92rem;
                                "
                            >
                                Ikuti update terbaru kami
                            </p>

                            <div class="social">
                                <a href="#"
                                    ><font-awesome-icon
                                        :icon="['fab', 'instagram']"
                                /></a>
                                <a href="#"
                                    ><font-awesome-icon
                                        :icon="['fab', 'linkedin']"
                                /></a>
                                <a href="#"
                                    ><font-awesome-icon
                                        :icon="['fab', 'facebook']"
                                /></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer__bottom container">
                    <div class="footer__bottomRow">
                        <span
                            >© {{ year }} PT Mitra Wira Mas. All rights
                            reserved.</span
                        >
                        <div class="footer__links">
                            <!-- <a href="#">Terms</a>
                            <a href="#">Privacy</a> -->
                        </div>
                    </div>
                </div>
            </footer>
        </main>
        <Modal v-if="showLokerModal" @close="closeDetailLoker" size="lg">
            <div class="loker-modal">
                <div class="loker-modal__header">
                    <div class="loker-modal__title">Detail Loker</div>

                    <button
                        class="loker-modal__close"
                        type="button"
                        @click="closeDetailLoker"
                    >
                        ×
                    </button>
                </div>

                <div class="loker-modal__body">
                    <div v-if="lokerLoading" class="loading-state">
                        <div class="spinner"></div>
                        <p>Memuat detail loker...</p>
                    </div>

                    <div v-else-if="lokerError" class="error-state">
                        {{ lokerError }}
                    </div>

                    <div v-else-if="lokerDetail" class="loker-detail">
                        <h3 class="loker-detail__judul">
                            {{ lokerDetail.judul }}
                        </h3>

                        <div class="loker-detail__meta">
                            <span class="pill">{{
                                lokerDetail.tipe_pekerjaan || '-'
                            }}</span>
                            <span class="pill">{{
                                lokerDetail.penempatan_nama || '-'
                            }}</span>
                            <span class="pill">{{
                                lokerDetail.jam_kerja || '-'
                            }}</span>
                        </div>

                        <div class="loker-detail__info">
                            <div>
                                <b>Perusahaan:</b>
                                {{ lokerDetail.perusahaan_nama || '-' }}
                            </div>
                            <div><b>Gaji:</b> {{ gajiLabel }}</div>
                            <div>
                                <b>Publish:</b>
                                {{ lokerDetail.tanggal_publish || '-' }}
                            </div>
                        </div>

                        <div
                            v-if="lokerDetail.ringkasan"
                            class="loker-detail__section"
                        >
                            <div class="sec-title">Ringkasan</div>
                            <div class="sec-text">
                                {{ lokerDetail.ringkasan }}
                            </div>
                        </div>

                        <div
                            v-if="lokerDetail.deskripsi"
                            class="loker-detail__section"
                        >
                            <div class="sec-title">Deskripsi Pekerjaan</div>
                            <div class="sec-text preline">
                                {{ lokerDetail.deskripsi }}
                            </div>
                        </div>

                        <div
                            v-if="persyaratanList.length"
                            class="loker-detail__section"
                        >
                            <div class="sec-title">Persyaratan</div>
                            <ul class="req-list">
                                <li v-for="(r, i) in persyaratanList" :key="i">
                                    {{ r }}
                                </li>
                            </ul>
                        </div>

                        <div class="loker-detail__actions">
                            <button
                                class="btn btn--gold"
                                type="button"
                                @click="klikLamar"
                            >
                                <font-awesome-icon icon="paper-plane" />
                                Lamar Sekarang
                            </button>

                            <button
                                class="btn btn--dark"
                                type="button"
                                @click="closeDetailLoker"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
        <!-- <PelamarApplyForm
            :loker="lokerDetail"
            @close="closeDetailLoker"
            @submitted="closeDetailLoker"
        /> -->
    </div>
</template>

<script>
import Modal from '@/components/Modal.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import PelamarApplyForm from './PelamarApplyForm.vue';
export default {
    name: 'LandingPage',
    components: { Modal, PelamarApplyForm },
    data() {
        return {
            // ✅ Inertia safe: ambil dari public/assets (jangan import)
            heroImage: '/assets/hero-team.jpg',
            founderImage: '/assets/founder.jpg',
            companyLogo: '/assets/images/logo_baru.png',

            year: new Date().getFullYear(),

            navLinks: [
                { label: 'Beranda', hash: '#beranda' },
                { label: 'Sejarah', hash: '#sejarah' },
                { label: 'Mitra', hash: '#mitra' },
                { label: 'Pengumuman', hash: '#pengumuman' },
                { label: 'Loker', hash: '#loker' },
                { label: 'Kontak', hash: '#kontak' },
            ],

            notes: [],
            jobs: [],
            loadingLanding: true,
            errorLanding: null,

            // ✅ Mitra logos dari public/assets
            mitraLogos: [
                '/assets/mitra/mitra-1.jpg',
                '/assets/mitra/mitra-1.jpg',
                '/assets/mitra/mitra-1.jpg',
                '/assets/mitra/mitra-1.jpg',
                '/assets/mitra/mitra-1.jpg',
                '/assets/mitra/mitra-1.jpg',
                '/assets/mitra/mitra-1.jpg',
            ],

            // modal detail loker
            showLokerModal: false,
            lokerLoading: false,
            lokerDetail: null,
            lokerError: null,

            activeHash: '#beranda',

            // observers reference
            revealObs: null,
            spyObs: null,
        };
    },

    computed: {
        bannerStyle() {
            return {
                backgroundImage: `linear-gradient(90deg, rgba(5,18,37,.92), rgba(5,18,37,.68), rgba(5,18,37,.10)), url(${this.heroImage})`,
            };
        },
        gajiLabel() {
            if (!this.lokerDetail) return '-';
            const min = this.lokerDetail.gaji_min;
            const max = this.lokerDetail.gaji_max;
            const cur = this.lokerDetail.mata_uang || 'IDR';

            if (!min && !max) return 'Negosiasi';
            if (min && !max) return `${cur} ${this.formatRupiah(min)}+`;
            if (!min && max) return `${cur} s/d ${this.formatRupiah(max)}`;
            return `${cur} ${this.formatRupiah(min)} - ${this.formatRupiah(max)}`;
        },

        persyaratanList() {
            const text = this.lokerDetail?.persyaratan || '';
            return text
                .split('\n')
                .map((x) => x.trim())
                .filter(Boolean);
        },
    },

    methods: {
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(Number(num || 0));
        },
        setActive(hash) {
            this.activeHash = hash;
        },
        async fetchLandingData() {
            this.loadingLanding = true;
            this.errorLanding = null;

            try {
                const res = await axios.get('/referensi/landing-page');

                this.notes = res.data?.notes || [];
                this.jobs = res.data?.jobs || [];
            } catch (err) {
                this.errorLanding = 'Gagal mengambil data landing page';
                console.error(err);
            } finally {
                this.loadingLanding = false;
            }
        },
        handleHashChange() {
            this.activeHash = window.location.hash || '#beranda';
        },

        initRevealObserver() {
            const revealItems = document.querySelectorAll('.reveal');

            this.revealObs = new IntersectionObserver(
                (entries) => {
                    entries.forEach((e) => {
                        if (!e.isIntersecting) return;
                        e.target.classList.add('is-visible');
                        this.revealObs?.unobserve(e.target);
                    });
                },
                { threshold: 0.12 },
            );

            revealItems.forEach((el) => this.revealObs.observe(el));
        },

        initNavbarSpy() {
            const sections = this.navLinks
                .map((l) => document.querySelector(l.hash))
                .filter(Boolean);

            this.spyObs = new IntersectionObserver(
                (entries) => {
                    const visible = entries
                        .filter((e) => e.isIntersecting)
                        .sort(
                            (a, b) => b.intersectionRatio - a.intersectionRatio,
                        )[0];

                    if (!visible) return;
                    this.activeHash = '#' + visible.target.id;
                },
                {
                    threshold: [0.25, 0.35, 0.5, 0.65],
                    rootMargin: '-25% 0px -55% 0px',
                },
            );

            sections.forEach((sec) => this.spyObs.observe(sec));
        },
        async openDetailLoker(slug) {
            this.showLokerModal = true;
            this.lokerLoading = true;
            this.lokerError = null;
            this.lokerDetail = null;

            try {
                const res = await axios.get(
                    `/referensi/landing-page/loker/${slug}`,
                );
                this.lokerDetail = res.data;
            } catch (e) {
                console.error(e);
                this.lokerError = 'Gagal memuat detail loker.';
            } finally {
                this.lokerLoading = false;
            }
        },

        closeDetailLoker() {
            this.showLokerModal = false;
            this.lokerDetail = null;
            this.lokerError = null;
        },

        klikLamar() {
            if (!this.lokerDetail) return;

            // 1) jika ada link lamar
            if (this.lokerDetail.link_lamar) {
                window.open(this.lokerDetail.link_lamar, '_blank');
                return;
            }

            // 2) fallback ke whatsapp
            if (this.lokerDetail.whatsapp_kontak) {
                const nomor = this.lokerDetail.whatsapp_kontak.replace(
                    /\D/g,
                    '',
                );
                const msg = encodeURIComponent(
                    `Halo, saya ingin melamar posisi "${this.lokerDetail.judul}".`,
                );
                window.open(`https://wa.me/${nomor}?text=${msg}`, '_blank');
                return;
            }

            alert('Kontak melamar belum tersedia.');
        },

        masukAplikasi() {
            router.visit(`/login`);
        },
    },

    mounted() {
        this.handleHashChange();
        window.addEventListener('hashchange', this.handleHashChange);

        this.initRevealObserver();
        this.initNavbarSpy();
        this.fetchLandingData();
    },

    beforeUnmount() {
        window.removeEventListener('hashchange', this.handleHashChange);
        this.revealObs?.disconnect();
        this.spyObs?.disconnect();
    },
};
</script>

<style>
:root {
    --navy: #071a33;
    --navy2: #051225;
    --gold: #d6a33a;
    --gold2: #b98a2e;

    --text: #0f172a;
    --muted: #5b6472;
    --bg: #f5f7fb;
    --card: #ffffff;

    --shadow: 0 14px 38px rgba(10, 20, 40, 0.12);
    --radius: 18px;
}

* {
    box-sizing: border-box;
}
html,
body {
    margin: 0;
    padding: 0;
}
body {
    font-family:
        'Inter',
        system-ui,
        -apple-system,
        Segoe UI,
        Roboto,
        Arial,
        sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.5;
}

a {
    text-decoration: none;
    color: inherit;
}
img {
    max-width: 100%;
    display: block;
}

/* ✅ icon size normalize */
svg {
    width: 1em;
    height: 1em;
}

/* ✅ Content padding kiri-kanan 10% */
.container {
    width: 100%;
    padding-left: 10%;
    padding-right: 10%;
}

section,
footer {
    scroll-margin-top: 90px;
}
html {
    scroll-behavior: smooth;
}

/* =========================
   NAVBAR
========================= */
.header {
    position: sticky;
    top: 0;
    z-index: 99;
    background: linear-gradient(
        180deg,
        rgba(7, 26, 51, 0.95),
        rgba(5, 18, 37, 0.92)
    );
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
}

.nav {
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 10px;
}
.brand__logo {
    width: 46px;
    height: 46px;
    object-fit: contain;

    padding: 6px;
    border-radius: 12px;

    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.brand__mark {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: rgba(214, 163, 58, 0.14);
    color: var(--gold);
    font-weight: 900;
    letter-spacing: 0.5px;
}
.brand__text {
    display: flex;
    flex-direction: column;
    line-height: 1.05;
    color: #fff;
}
.brand__text small {
    opacity: 0.75;
    font-weight: 700;
    letter-spacing: 0.22em;
    font-size: 0.72rem;
}

.nav__menu {
    display: flex;
    align-items: center;
    gap: 16px;
}
.nav__link {
    color: rgba(255, 255, 255, 0.75);
    font-weight: 700;
    font-size: 0.92rem;
    padding: 8px 4px;
    position: relative;
}
.nav__link:hover {
    color: #fff;
}
.nav__link.is-active {
    color: #fff;
}
.nav__link.is-active::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -10px;
    width: 100%;
    height: 3px;
    border-radius: 999px;
    background: var(--gold);
}

/* =========================
   BUTTON
========================= */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 10px 14px;
    border-radius: 999px;
    font-weight: 800;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition:
        transform 0.15s ease,
        opacity 0.15s ease,
        background 0.15s ease;
}
.btn:hover {
    transform: translateY(-1px);
}
.btn:active {
    transform: translateY(0);
    opacity: 0.92;
}

.btn--gold {
    background: var(--gold);
    color: #111;
    border-color: rgba(0, 0, 0, 0.08);
}
.btn--gold:hover {
    background: var(--gold2);
}

.btn--outline {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.25);
    color: rgba(255, 255, 255, 0.9);
}
.btn--outline:hover {
    border-color: rgba(255, 255, 255, 0.4);
    color: #fff;
}

.btn--dark {
    background: var(--navy);
    color: #fff;
}
.btn--dark:hover {
    background: var(--navy2);
}

.btn--sm {
    padding: 9px 12px;
    font-size: 0.9rem;
}

/* =========================
   BLOCK STYLE
========================= */
.block {
    background: var(--card);
    border: 1px solid rgba(10, 20, 40, 0.06);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.block__head {
    padding: 18px 18px 0;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.title {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 900;
    letter-spacing: 0.02em;
}

.sub {
    margin: 8px 0 0;
    color: rgba(15, 23, 42, 0.68);
    font-weight: 600;
    font-size: 0.92rem;
}

.content {
    padding: 18px;
}

/* =========================
   ✅ BANNER FULL HEIGHT
========================= */
.banner {
    width: 100%;
    min-height: calc(100svh - 72px);
    display: flex;
    align-items: center;

    border-radius: 0;
    margin: 0;

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    position: relative;
}

.banner__inner {
    padding: 44px 0;
    max-width: 820px;
    color: #fff;
}

.banner__kicker {
    display: inline-block;
    font-weight: 900;
    letter-spacing: 0.22em;
    font-size: 0.75rem;
    color: rgba(214, 163, 58, 0.95);
    margin-bottom: 10px;
}

.banner__title {
    margin: 0 0 12px;
    font-size: clamp(1.9rem, 3.2vw, 2.8rem);
    line-height: 1.12;
    font-weight: 900;
}

.banner__desc {
    margin: 0 0 18px;
    color: rgba(255, 255, 255, 0.78);
    max-width: 64ch;
    font-weight: 600;
}

.banner__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.banner::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 10px;
    background: var(--gold);
}

/* =========================
   GRID LAYOUT
========================= */
.page {
    padding: 0px;
}

.row {
    margin-top: 16px;
    display: grid;
    gap: 16px;
}

.row--two {
    grid-template-columns: 1.35fr 0.65fr;
    align-items: stretch;
}

.row--split {
    grid-template-columns: 1fr 1fr;
    align-items: stretch;
}

/* =========================
   SEJARAH SIMPLE
========================= */
.mini-list {
    margin: 12px 0 0;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 10px;
}

.mini-list li {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 12px 12px;
    border-radius: 14px;
    background: rgba(214, 163, 58, 0.1);
    border: 1px solid rgba(214, 163, 58, 0.18);
}

.mini-list svg {
    margin-top: 2px;
    color: rgba(214, 163, 58, 0.95);
}

.mini-list strong {
    display: block;
    font-weight: 900;
    font-size: 0.95rem;
    margin-bottom: 2px;
}
.mini-list span {
    display: block;
    color: rgba(15, 23, 42, 0.7);
    font-weight: 600;
    font-size: 0.9rem;
}

/* =========================
   FOTO PENDIRI
========================= */
.photo {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.photo__wrap {
    flex: 1;
    background: #0b1220;
    overflow: hidden;
}

.photo__wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo__cap {
    padding: 14px 16px;
    border-top: 1px solid rgba(10, 20, 40, 0.08);
    background: #fff;
}
.photo__cap strong {
    display: block;
    font-weight: 900;
    margin-bottom: 3px;
}
.photo__cap span {
    color: rgba(15, 23, 42, 0.65);
    font-weight: 700;
    font-size: 0.9rem;
}

/* =========================
   MITRA LOGO
========================= */
.logos {
    display: grid;
    gap: 12px;
    margin-top: 12px;

    /* lebih lebar biar logo bisa besar */
    grid-template-columns: repeat(auto-fit, minmax(210px, 210px));
    justify-content: center;
}

.logo {
    border: 1px solid rgba(10, 20, 40, 0.06);
    border-radius: 16px;
    background: #fff;
    min-height: 150px;
    display: grid;
    place-items: center;
    box-shadow: 0 14px 34px rgba(10, 20, 40, 0.08);
    transition:
        transform 0.15s ease,
        box-shadow 0.15s ease;
}

.logo img {
    width: 170px;
    max-height: 100px;
    object-fit: contain;
    filter: grayscale(1);
    opacity: 0.82;
    transition: 0.15s ease;
}

.logo:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 46px rgba(10, 20, 40, 0.14);
}

.logo:hover img {
    filter: grayscale(0);
    opacity: 1;
}

/* =========================
   PENGUMUMAN SIMPLE
========================= */
.note {
    display: grid;
    gap: 12px;
    margin-top: 12px;
}

.note__item {
    padding: 14px 14px;
    border-radius: 16px;
    background: #fff;
    border: 1px solid rgba(10, 20, 40, 0.06);
    box-shadow: 0 14px 34px rgba(10, 20, 40, 0.08);
}

.note__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 6px;
}

.badge {
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(214, 163, 58, 0.14);
    border: 1px solid rgba(214, 163, 58, 0.22);
    font-weight: 900;
    font-size: 0.78rem;
    color: rgba(15, 23, 42, 0.92);
}

.date {
    color: rgba(15, 23, 42, 0.55);
    font-weight: 800;
    font-size: 0.82rem;
}

.note__title {
    margin: 0 0 6px;
    font-weight: 900;
    font-size: 0.98rem;
}

.note__desc {
    margin: 0;
    color: rgba(15, 23, 42, 0.68);
    font-weight: 600;
    font-size: 0.92rem;
}

/* =========================
   LOKER
========================= */
.jobs {
    display: grid;
    gap: 12px;
    margin-top: 12px;
}

.job {
    padding: 14px 14px;
    border-radius: 16px;
    background: #fff;
    border: 1px solid rgba(10, 20, 40, 0.06);
    box-shadow: 0 14px 34px rgba(10, 20, 40, 0.08);
    transition:
        transform 0.15s ease,
        box-shadow 0.15s ease;
}

.job:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 46px rgba(10, 20, 40, 0.14);
}

.job__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
}

.job__title {
    margin: 0;
    font-weight: 900;
    font-size: 1rem;
}

.job__type {
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(7, 26, 51, 0.08);
    border: 1px solid rgba(7, 26, 51, 0.12);
    font-weight: 900;
    font-size: 0.78rem;
    color: rgba(7, 26, 51, 0.88);
    white-space: nowrap;
}

.job__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 14px;
    color: rgba(15, 23, 42, 0.65);
    font-weight: 700;
    font-size: 0.88rem;
    margin-bottom: 10px;
}

.job__meta span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.job__meta svg {
    color: rgba(214, 163, 58, 0.95);
}

.job__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 10px;
}

/* =========================
   ✅ FOOTER
========================= */
.footer {
    background: linear-gradient(180deg, #050b16, #030713);
    color: rgba(255, 255, 255, 0.88);
    border-top: 1px solid rgba(255, 255, 255, 0.07);
    margin-top: 26px;
    padding-left: 10%;
    padding-right: 10%;
    padding-top: 20px;
    padding-bottom: 20px;
}

.footer__top {
    padding: 34px 0 26px;
}

.footer__grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr 1fr;
    gap: 22px;
    align-items: start;
}

.footer__brand {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.footer__brandRow {
    display: flex;
    align-items: center;
    gap: 10px;
}
.footer__logoImg {
    width: 56px;
    height: 56px;
    object-fit: contain;

    padding: 8px;
    border-radius: 14px;

    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.footer__logo {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: rgba(214, 163, 58, 0.14);
    color: var(--gold);
    font-weight: 900;
    letter-spacing: 0.5px;
}

.footer__brandText strong {
    display: block;
    color: #fff;
    font-weight: 900;
    line-height: 1.1;
}

.footer__brandText small {
    display: block;
    margin-top: 2px;
    opacity: 0.7;
    font-weight: 800;
    letter-spacing: 0.18em;
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.78);
}

.footer__desc {
    margin: 0;
    color: rgba(255, 255, 255, 0.72);
    font-weight: 600;
    font-size: 0.92rem;
    max-width: 40ch;
}

.footer__col h4 {
    margin: 0 0 12px;
    font-size: 0.95rem;
    letter-spacing: 0.08em;
    font-weight: 900;
    color: rgba(255, 255, 255, 0.92);
}

.footer__col p,
.footer__col a {
    margin: 0 0 8px;
    color: rgba(255, 255, 255, 0.72);
    font-weight: 600;
    font-size: 0.92rem;
    display: block;
}
.footer__col a:hover {
    color: #fff;
    text-decoration: underline;
    text-underline-offset: 3px;
}

.footer__contactItem {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-bottom: 10px;
    color: rgba(255, 255, 255, 0.72);
    font-weight: 600;
    font-size: 0.92rem;
}
.footer__contactItem svg {
    color: rgba(214, 163, 58, 0.95);
    margin-top: 2px;
}

.social {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 6px;
}
.social a {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    transition:
        transform 0.15s ease,
        border-color 0.15s ease;
}
.social a:hover {
    transform: translateY(-2px);
    border-color: rgba(214, 163, 58, 0.6);
}

.footer__bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.07);
    padding: 14px 0;
    color: rgba(255, 255, 255, 0.55);
    font-weight: 600;
    font-size: 0.86rem;
}

.footer__bottomRow {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.footer__links {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.footer__links a {
    color: rgba(255, 255, 255, 0.55);
    font-weight: 700;
    font-size: 0.86rem;
}
.footer__links a:hover {
    color: #fff;
    text-decoration: underline;
    text-underline-offset: 3px;
}

/* =========================
   REVEAL
========================= */
.reveal {
    opacity: 0;
    transform: translateY(16px);
    filter: blur(1.5px);
    transition:
        opacity 0.7s ease,
        transform 0.7s ease,
        filter 0.7s ease;
    transition-delay: calc(var(--d, 0) * 1ms);
}
.reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
    filter: blur(0);
}
.loker-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}

.loker-modal__title {
    font-weight: 900;
    font-size: 1rem;
}

.loker-modal__close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid rgba(15, 23, 42, 0.12);
    background: #fff;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
}

.loker-modal__body {
    padding: 16px 18px;
}

.loker-detail__judul {
    margin: 0 0 10px;
    font-weight: 900;
}

.loker-detail__meta {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.pill {
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(7, 26, 51, 0.08);
    border: 1px solid rgba(7, 26, 51, 0.12);
    font-weight: 800;
    font-size: 0.82rem;
}

.loker-detail__info {
    display: grid;
    gap: 6px;
    color: rgba(15, 23, 42, 0.75);
    font-weight: 650;
    margin-bottom: 12px;
}

.loker-detail__section {
    margin-top: 14px;
}

.sec-title {
    font-weight: 900;
    margin-bottom: 6px;
}

.sec-text {
    color: rgba(15, 23, 42, 0.75);
    font-weight: 600;
}

.preline {
    white-space: pre-line;
}

.req-list {
    margin: 0;
    padding-left: 18px;
    color: rgba(15, 23, 42, 0.75);
    font-weight: 650;
}

.loker-detail__actions {
    margin-top: 16px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.error-state {
    color: #b91c1c;
    font-weight: 800;
}
/* =========================
   RESPONSIVE
========================= */
@media (max-width: 1100px) {
    .footer__grid {
        grid-template-columns: 1.2fr 1fr 1fr;
    }
}

@media (max-width: 980px) {
    .row--two {
        grid-template-columns: 1fr;
    }
    .row--split {
        grid-template-columns: 1fr;
    }
    .logos {
        grid-template-columns: repeat(3, 1fr);
    }
    .nav__menu {
        display: none;
    }

    .container {
        padding-left: 6%;
        padding-right: 6%;
    }

    .footer__grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 560px) {
    .logos {
        grid-template-columns: repeat(auto-fit, minmax(170px, 170px));
    }

    .logo img {
        width: 150px;
        max-height: 58px;
    }

    .container {
        padding-left: 5%;
        padding-right: 5%;
    }

    .footer__grid {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion: reduce) {
    * {
        transition: none !important;
    }
    .reveal {
        opacity: 1;
        transform: none;
        filter: none;
    }
    html {
        scroll-behavior: auto;
    }
}
</style>
