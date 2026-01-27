<template>
    <div class="landing-root">
        <!-- NAVBAR -->
        <header class="header-landing">
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

                <button class="btn btn--gold btn--sm" @click="masukAplikasi">
                    Masuk Aplikasi
                </button>
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
                <div>
                    <div
                        v-if="$page.props.flash.success && showSuccess"
                        class="alert alert-success"
                    >
                        <div class="alert-left">
                            <span class="alert-icon">✓</span>

                            <div class="alert-text">
                                <div class="alert-title">Berhasil</div>
                                <div class="alert-msg">
                                    {{ $page.props.flash.success }}
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="alert-close"
                            @click="showSuccess = false"
                        >
                            ×
                        </button>
                    </div>

                    <!-- ERROR -->
                    <div
                        v-if="$page.props.flash.error && showError"
                        class="alert alert-error"
                    >
                        <div class="alert-left">
                            <span class="alert-icon">!</span>

                            <div class="alert-text">
                                <div class="alert-title">Gagal</div>
                                <div class="alert-msg">
                                    {{ $page.props.flash.error }}
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="alert-close"
                            @click="showError = false"
                        >
                            ×
                        </button>
                    </div>
                </div>
                <!-- SEJARAH + FOTO PENDIRI -->
                <section id="sejarah" class="row row--two">
                    <div class="reveal block" style="--d: 60">
                        <div class="block__head">
                            <div>
                                <h2 class="title">
                                    Sejarah Perusahaan & Pendiri
                                </h2>
                                <p class="sub">
                                    <!-- Simple, singkat, dan fokus ke kredibilitas. -->
                                </p>
                            </div>
                        </div>

                        <div class="content">
                            <p class="paragraph">
                                <strong>Lorem Ipsum</strong> is simply dummy
                                text of the printing and typesetting industry.
                                Lorem Ipsum has been the industry's standard
                                dummy text ever since the 1500s, when an unknown
                                printer took a galley of type and scrambled it
                                to make a type specimen book. It has survived
                                not only five centuries, but also the leap into
                                electronic typesetting, remaining essentially
                                unchanged. It was popularised in the 1960s with
                                the release of Letraset sheets containing Lorem
                                Ipsum passages, and more recently with desktop
                                publishing software like Aldus PageMaker
                                including versions of Lorem Ipsum.
                            </p>
                        </div>
                    </div>

                    <div class="photo reveal block" style="--d: 120">
                        <div class="photo__wrap">
                            <img
                                :src="founderImage"
                                alt="Foto Pendiri"
                                loading="lazy"
                            />
                        </div>
                        <div class="photo__cap">
                            <strong>Alm. H. Jasiman</strong>
                            <span>Founder PT. Mitra Wira Mas </span>
                        </div>
                    </div>
                </section>

                <!-- MITRA -->
                <section id="mitra" class="row">
                    <div class="reveal block" style="--d: 90">
                        <div class="block__head head-row">
                            <div>
                                <h2 class="title">
                                    Perusahaan yang Bekerja Sama
                                </h2>
                                <p class="sub">
                                    Mitra & klien yang telah mempercayai kami
                                </p>
                            </div>
                        </div>

                        <div class="content">
                            <div class="logo-strip">
                                <Swiper
                                    class="logo-swiper"
                                    :modules="modules"
                                    :loop="true"
                                    :speed="10000"
                                    :autoplay="{
                                        delay: 0,
                                        disableOnInteraction: false,
                                        pauseOnMouseEnter: true,
                                    }"
                                    :slides-per-view="'auto'"
                                    :space-between="28"
                                >
                                    <SwiperSlide
                                        v-for="(logo, i) in mitraLogos"
                                        :key="i"
                                        class="logo-slide"
                                    >
                                        <div class="logo-chip">
                                            <img
                                                :src="logo"
                                                :alt="`Mitra ${i + 1}`"
                                                loading="lazy"
                                            />
                                        </div>
                                    </SwiperSlide>
                                </Swiper>
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
                                <!-- <p class="sub">Ringkas dan gampang dibaca.</p> -->
                            </div>
                        </div>

                        <div class="content">
                            <div v-if="loadingLanding" class="loading-state">
                                <div class="spinner"></div>
                                <p>Memuat data...</p>
                            </div>

                            <div v-else-if="errorLanding" class="error-state">
                                {{ errorLanding }}
                            </div>

                            <div v-else class="note">
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

                                <div v-if="!notes.length" class="empty-state">
                                    Tidak ada pengumuman.
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
                            <div v-if="loadingLanding" class="loading-state">
                                <div class="spinner"></div>
                                <p>Memuat data...</p>
                            </div>

                            <div v-else-if="errorLanding" class="error-state">
                                {{ errorLanding }}
                            </div>

                            <div v-else class="jobs">
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

                                <div v-if="!jobs.length" class="empty-state">
                                    Tidak ada lowongan tersedia.
                                </div>
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
                            <p class="footer__socialText">
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
                        <div class="footer__links"></div>
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
    </div>
</template>

<script>
import Modal from '@/components/Modal.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import 'swiper/css';
import { Autoplay } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';

export default {
    name: 'LandingPage',
    components: { Modal },
    components: { Swiper, SwiperSlide },
    props: {
        mitraLogos: { type: Array, required: true },
    },
    data() {
        return {
            modules: [Autoplay],
            showSuccess: true,
            showError: true,
            heroImage: '/assets/hero-team.jpg',
            founderImage: '/assets/founder.png',
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

            mitraLogos: [
                '/assets/mitra/mitra-1.png',
                '/assets/mitra/mitra-2.png',
                '/assets/mitra/mitra-3.png',
                '/assets/mitra/mitra-4.png',
                '/assets/mitra/mitra-5.png',
                '/assets/mitra/mitra-6.png',
                '/assets/mitra/mitra-7.png',
                '/assets/mitra/mitra-8.png',
                '/assets/mitra/mitra-9.png',
                '/assets/mitra/mitra-10.png',
                '/assets/mitra/mitra-11.png',
                '/assets/mitra/mitra-12.png',
                '/assets/mitra/mitra-13.png',
                '/assets/mitra/mitra-14.png',
            ],
            showLokerModal: false,
            lokerLoading: false,
            lokerDetail: null,
            lokerError: null,

            activeHash: '#beranda',

            revealObs: null,
            spyObs: null,
        };
    },

    watch: {
        '$page.props.flash.success'(val) {
            this.showSuccess = !!val;
        },
        '$page.props.flash.error'(val) {
            this.showError = !!val;
        },
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
            return `${cur} ${this.formatRupiah(min)} - ${this.formatRupiah(
                max,
            )}`;
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

            if (this.lokerDetail.link_lamar) {
                window.open(this.lokerDetail.link_lamar, '_blank');
                return;
            }

            router.visit(`/landing/` + this.lokerDetail.id);
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
/* =========================================================
   ✅ ISOLATION ROOT (PENTING: MENCEGAH BOCOR KE APPLY PAGE)
========================================================= */
.landing-root {
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

    min-height: 100vh;
    background: var(--bg);
    color: var(--text);
    line-height: 1.5;

    font-family:
        'Inter',
        system-ui,
        -apple-system,
        Segoe UI,
        Roboto,
        Arial,
        sans-serif;
}

/* Reset lokal */
.landing-root * {
    box-sizing: border-box;
}
.landing-root a {
    text-decoration: none;
    color: inherit;
}
.landing-root img {
    max-width: 100%;
    display: block;
}
.landing-root svg {
    width: 1em;
    height: 1em;
}

/* ✅ Content padding kiri-kanan 10% */
.landing-root .container {
    width: 100%;
    padding-left: 10%;
    padding-right: 10%;
}

.landing-root section,
.landing-root footer {
    scroll-margin-top: 90px;
}

/* =========================
   NAVBAR
========================= */
.landing-root .header-landing {
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

.landing-root .nav {
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

/* wrapper */
.landing-root .brand {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

/* gambar logo */
.landing-root .brand__logo {
    height: 46px; /* tetap tinggi */
    width: auto; /* biar memanjang sesuai aspect ratio */
    max-width: 180px; /* batasi biar nggak makan navbar */
    object-fit: contain;
    padding: 0; /* jangan padding di img */
    border-radius: 0; /* opsional */
    background: transparent;
    border: 0;
}

.landing-root .nav__menu {
    display: flex;
    align-items: center;
    gap: 16px;
}
.landing-root .nav__link {
    color: rgba(255, 255, 255, 0.75);
    font-weight: 700;
    font-size: 0.92rem;
    padding: 8px 4px;
    position: relative;
}
.landing-root .nav__link:hover {
    color: #fff;
}
.landing-root .nav__link.is-active {
    color: #fff;
}
.landing-root .nav__link.is-active::after {
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
.landing-root .btn {
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
.landing-root .btn:hover {
    transform: translateY(-1px);
}
.landing-root .btn:active {
    transform: translateY(0);
    opacity: 0.92;
}

.landing-root .btn--gold {
    background: var(--gold);
    color: #111;
    border-color: rgba(0, 0, 0, 0.08);
}
.landing-root .btn--gold:hover {
    background: var(--gold2);
}

.landing-root .btn--outline {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.25);
    color: rgba(255, 255, 255, 0.9);
}
.landing-root .btn--outline:hover {
    border-color: rgba(255, 255, 255, 0.4);
    color: #fff;
}

.landing-root .btn--dark {
    background: var(--navy);
    color: #fff;
}
.landing-root .btn--dark:hover {
    background: var(--navy2);
}

.landing-root .btn--sm {
    padding: 9px 12px;
    font-size: 0.9rem;
}

/* =========================
   BLOCK STYLE
========================= */
.landing-root .block {
    background: var(--card);
    border: 1px solid rgba(10, 20, 40, 0.06);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.landing-root .block__head {
    padding: 18px 18px 0;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.landing-root .title {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 900;
    letter-spacing: 0.02em;
}

.landing-root .sub {
    margin: 8px 0 0;
    color: rgba(15, 23, 42, 0.68);
    font-weight: 600;
    font-size: 0.92rem;
}

.landing-root .content {
    padding: 18px;
}

.landing-root .paragraph {
    margin: 0;
    color: rgba(15, 23, 42, 0.72);
    font-weight: 600;
}

/* =========================
   ✅ BANNER FULL HEIGHT
========================= */
.landing-root .banner {
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

.landing-root .banner__inner {
    padding: 44px 0;
    max-width: 820px;
    color: #fff;
}

.landing-root .banner__kicker {
    display: inline-block;
    font-weight: 900;
    letter-spacing: 0.22em;
    font-size: 0.75rem;
    color: rgba(214, 163, 58, 0.95);
    margin-bottom: 10px;
}

.landing-root .banner__title {
    margin: 0 0 12px;
    font-size: clamp(1.9rem, 3.2vw, 2.8rem);
    line-height: 1.12;
    font-weight: 900;
}

.landing-root .banner__desc {
    margin: 0 0 18px;
    color: rgba(255, 255, 255, 0.78);
    max-width: 64ch;
    font-weight: 600;
}

.landing-root .banner__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.landing-root .banner::after {
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
.landing-root .page {
    padding: 0px;
}

.landing-root .row {
    margin-top: 16px;
    display: grid;
    gap: 16px;
}

.landing-root .row--two {
    grid-template-columns: 1.35fr 0.65fr;
    align-items: stretch;
}

.landing-root .row--split {
    grid-template-columns: 1fr 1fr;
    align-items: stretch;
}

/* =========================
   SEJARAH SIMPLE
========================= */
.landing-root .mini-list {
    margin: 12px 0 0;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 10px;
}

.landing-root .mini-list li {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 12px 12px;
    border-radius: 14px;
    background: rgba(214, 163, 58, 0.1);
    border: 1px solid rgba(214, 163, 58, 0.18);
}

.landing-root .mini-list svg {
    margin-top: 2px;
    color: rgba(214, 163, 58, 0.95);
}

.landing-root .mini-list strong {
    display: block;
    font-weight: 900;
    font-size: 0.95rem;
    margin-bottom: 2px;
}
.landing-root .mini-list span {
    display: block;
    color: rgba(15, 23, 42, 0.7);
    font-weight: 600;
    font-size: 0.9rem;
}

/* =========================
   FOTO PENDIRI
========================= */
.landing-root .photo {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.landing-root .photo__wrap {
    flex: 1;
    background: #0b1220;
    overflow: hidden;
}

.landing-root .photo__wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.landing-root .photo__cap {
    padding: 14px 16px;
    border-top: 1px solid rgba(10, 20, 40, 0.08);
    background: #fff;
}
.landing-root .photo__cap strong {
    display: block;
    font-weight: 900;
    margin-bottom: 3px;
}
.landing-root .photo__cap span {
    color: rgba(15, 23, 42, 0.65);
    font-weight: 700;
    font-size: 0.9rem;
}

/* =========================
   MITRA LOGO
========================= */
.landing-root .logos {
    display: grid;
    gap: 12px;
    margin-top: 12px;
    grid-template-columns: repeat(auto-fit, minmax(210px, 210px));
    justify-content: center;
}

.landing-root .logo {
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

.landing-root .logo img {
    width: 170px;
    max-height: 100px;
    object-fit: contain;
    filter: grayscale(1);
    opacity: 0.82;
    transition: 0.15s ease;
}

.landing-root .logo:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 46px rgba(10, 20, 40, 0.14);
}

.landing-root .logo:hover img {
    filter: grayscale(0);
    opacity: 1;
}

/* =========================
   PENGUMUMAN SIMPLE
========================= */
.landing-root .note {
    display: grid;
    gap: 12px;
    margin-top: 12px;
}

.landing-root .note__item {
    padding: 14px 14px;
    border-radius: 16px;
    background: #fff;
    border: 1px solid rgba(10, 20, 40, 0.06);
    box-shadow: 0 14px 34px rgba(10, 20, 40, 0.08);
}

.landing-root .note__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 6px;
}

.landing-root .badge {
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(214, 163, 58, 0.14);
    border: 1px solid rgba(214, 163, 58, 0.22);
    font-weight: 900;
    font-size: 0.78rem;
    color: rgba(15, 23, 42, 0.92);
}

.landing-root .date {
    color: rgba(15, 23, 42, 0.55);
    font-weight: 800;
    font-size: 0.82rem;
}

.landing-root .note__title {
    margin: 0 0 6px;
    font-weight: 900;
    font-size: 0.98rem;
}

.landing-root .note__desc {
    margin: 0;
    color: rgba(15, 23, 42, 0.68);
    font-weight: 600;
    font-size: 0.92rem;
}

/* =========================
   LOKER
========================= */
.landing-root .jobs {
    display: grid;
    gap: 12px;
    margin-top: 12px;
}

.landing-root .job {
    padding: 14px 14px;
    border-radius: 16px;
    background: #fff;
    border: 1px solid rgba(10, 20, 40, 0.06);
    box-shadow: 0 14px 34px rgba(10, 20, 40, 0.08);
    transition:
        transform 0.15s ease,
        box-shadow 0.15s ease;
}

.landing-root .job:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 46px rgba(10, 20, 40, 0.14);
}

.landing-root .job__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
}

.landing-root .job__title {
    margin: 0;
    font-weight: 900;
    font-size: 1rem;
}

.landing-root .job__type {
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(7, 26, 51, 0.08);
    border: 1px solid rgba(7, 26, 51, 0.12);
    font-weight: 900;
    font-size: 0.78rem;
    color: rgba(7, 26, 51, 0.88);
    white-space: nowrap;
}

.landing-root .job__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 14px;
    color: rgba(15, 23, 42, 0.65);
    font-weight: 700;
    font-size: 0.88rem;
    margin-bottom: 10px;
}

.landing-root .job__meta span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.landing-root .job__meta svg {
    color: rgba(214, 163, 58, 0.95);
}

.landing-root .job__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 10px;
}

/* =========================
   ✅ FOOTER
========================= */
.landing-root .footer {
    background: linear-gradient(180deg, #050b16, #030713);
    color: rgba(255, 255, 255, 0.88);
    border-top: 1px solid rgba(255, 255, 255, 0.07);
    margin-top: 26px;
    padding-left: 10%;
    padding-right: 10%;
    padding-top: 20px;
    padding-bottom: 20px;
}

.landing-root .footer__top {
    padding: 34px 0 26px;
}

.landing-root .footer__grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr 1fr;
    gap: 22px;
    align-items: start;
}

.landing-root .footer__brand {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.landing-root .footer__brandRow {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
}
.landing-root .footer__logoImg {
    height: 46px; /* atau 40px sesuai footer */
    width: auto;
    max-width: 220px; /* batasi biar tidak melebar berlebihan */
    object-fit: contain;
    display: block;

    padding: 8px;
    border-radius: 14px;

    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.landing-root .footer__desc {
    margin: 0;
    color: rgba(255, 255, 255, 0.72);
    font-weight: 600;
    font-size: 0.92rem;
    max-width: 40ch;
}

.landing-root .footer__col h4 {
    margin: 0 0 12px;
    font-size: 0.95rem;
    letter-spacing: 0.08em;
    font-weight: 900;
    color: rgba(255, 255, 255, 0.92);
}

.landing-root .footer__col p,
.landing-root .footer__col a {
    margin: 0 0 8px;
    color: rgba(255, 255, 255, 0.72);
    font-weight: 600;
    font-size: 0.92rem;
    display: block;
}
.landing-root .footer__col a:hover {
    color: #fff;
    text-decoration: underline;
    text-underline-offset: 3px;
}

.landing-root .footer__contactItem {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-bottom: 10px;
    color: rgba(255, 255, 255, 0.72);
    font-weight: 600;
    font-size: 0.92rem;
}
.landing-root .footer__contactItem svg {
    color: rgba(214, 163, 58, 0.95);
    margin-top: 2px;
}

.landing-root .footer__socialText {
    margin: 0 0 10px;
    color: rgba(255, 255, 255, 0.72);
    font-weight: 600;
    font-size: 0.92rem;
}

.landing-root .social {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 6px;
}
.landing-root .social a {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    transition:
        transform 0.15s ease,
        border-color 0.15s ease;
}
.landing-root .social a:hover {
    transform: translateY(-2px);
    border-color: rgba(214, 163, 58, 0.6);
}

.landing-root .footer__bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.07);
    padding: 14px 0;
    color: rgba(255, 255, 255, 0.55);
    font-weight: 600;
    font-size: 0.86rem;
}

.landing-root .footer__bottomRow {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

/* =========================
   REVEAL
========================= */
.landing-root .reveal {
    opacity: 0;
    transform: translateY(16px);
    filter: blur(1.5px);
    transition:
        opacity 0.7s ease,
        transform 0.7s ease,
        filter 0.7s ease;
    transition-delay: calc(var(--d, 0) * 1ms);
}
.landing-root .reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
    filter: blur(0);
}

/* =========================
   MODAL DETAIL
========================= */
.landing-root .loker-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}

.landing-root .loker-modal__title {
    font-weight: 900;
    font-size: 1rem;
}

.landing-root .loker-modal__close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid rgba(15, 23, 42, 0.12);
    background: #fff;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
}

.landing-root .loker-modal__body {
    padding: 16px 18px;
}

.landing-root .loker-detail__judul {
    margin: 0 0 10px;
    font-weight: 900;
}

.landing-root .loker-detail__meta {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.landing-root .pill {
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(7, 26, 51, 0.08);
    border: 1px solid rgba(7, 26, 51, 0.12);
    font-weight: 800;
    font-size: 0.82rem;
}

.landing-root .loker-detail__info {
    display: grid;
    gap: 6px;
    color: rgba(15, 23, 42, 0.75);
    font-weight: 650;
    margin-bottom: 12px;
}

.landing-root .loker-detail__section {
    margin-top: 14px;
}

.landing-root .sec-title {
    font-weight: 900;
    margin-bottom: 6px;
}

.landing-root .sec-text {
    color: rgba(15, 23, 42, 0.75);
    font-weight: 600;
}

.landing-root .preline {
    white-space: pre-line;
}

.landing-root .req-list {
    margin: 0;
    padding-left: 18px;
    color: rgba(15, 23, 42, 0.75);
    font-weight: 650;
}

.landing-root .loker-detail__actions {
    margin-top: 16px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* States */
.landing-root .error-state {
    color: #b91c1c;
    font-weight: 800;
}
.landing-root .empty-state {
    color: rgba(15, 23, 42, 0.55);
    font-weight: 700;
    padding: 10px 0 0;
}

.landing-root .loading-state {
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(15, 23, 42, 0.65);
    font-weight: 700;
}
.landing-root .spinner {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid rgba(15, 23, 42, 0.2);
    border-top-color: rgba(15, 23, 42, 0.7);
    animation: spin 0.7s linear infinite;
}
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 1100px) {
    .landing-root .footer__grid {
        grid-template-columns: 1.2fr 1fr 1fr;
    }
}

@media (max-width: 980px) {
    .landing-root .row--two {
        grid-template-columns: 1fr;
    }
    .landing-root .row--split {
        grid-template-columns: 1fr;
    }
    .landing-root .logos {
        grid-template-columns: repeat(3, 1fr);
    }
    .landing-root .nav__menu {
        display: none;
    }

    .landing-root .container {
        padding-left: 6%;
        padding-right: 6%;
    }

    .landing-root .footer__grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 560px) {
    .landing-root .logos {
        grid-template-columns: repeat(auto-fit, minmax(170px, 170px));
    }

    .landing-root .logo img {
        width: 150px;
        max-height: 58px;
    }

    .landing-root .container {
        padding-left: 5%;
        padding-right: 5%;
    }

    .landing-root .footer__grid {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion: reduce) {
    .landing-root * {
        transition: none !important;
    }
    .landing-root .reveal {
        opacity: 1;
        transform: none;
        filter: none;
    }
}

.alert {
    width: 100%;
    box-sizing: border-box;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #fff;

    font-family: Arial, sans-serif;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.04);
    margin-bottom: 12px;
}

.alert-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0; /* penting biar text bisa wrap */
}

.alert-icon {
    width: 18px;
    height: 18px;
    border-radius: 999px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    flex: 0 0 auto;
}

.alert-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.alert-title {
    font-size: 13px;
    font-weight: 700;
}

.alert-msg {
    font-size: 12px;
    opacity: 0.9;
    word-break: break-word;
    white-space: normal;
}

/* close button */
.alert-close {
    flex: 0 0 auto;

    width: 28px;
    height: 28px;

    border: 1px solid #d1d5db;
    background: #fff;
    border-radius: 8px;

    cursor: pointer;
    font-size: 16px;
    line-height: 1;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    transition: 0.15s ease;
}

.alert-close:hover {
    background: #f3f4f6;
}

.alert-close:active {
    transform: scale(0.98);
}

/* SUCCESS */
.alert-success {
    border-color: #bbf7d0;
    background: #f0fdf4;
    color: #166534;
}

.alert-success .alert-icon {
    background: #22c55e;
    color: #fff;
}

/* ERROR */
.alert-error {
    border-color: #fecaca;
    background: #fef2f2;
    color: #7f1d1d;
}

.alert-error .alert-icon {
    background: #ef4444;
    color: #fff;
}

/* Biar tinggi section tidak kebanyakan kosong */
#mitra .block {
    padding: 22px 24px;
}

/* Header lebih rapih */
#mitra .head-row {
    margin-bottom: 14px;
}
#mitra .title {
    margin: 0;
    line-height: 1.2;
}
#mitra .sub {
    margin: 8px 0 0;
    opacity: 0.8;
}

/* Strip container + fade edges (kesan premium) */
.logo-strip {
    position: relative;
    padding: 14px 8px;
    border-radius: 14px;
    border: 1px solid rgba(0, 0, 0, 0.06);
    background: rgba(255, 255, 255, 0.7);
    overflow: hidden;
}

/* Fade kiri/kanan */
.logo-strip::before,
.logo-strip::after {
    content: '';
    position: absolute;
    top: 0;
    width: 64px;
    height: 100%;
    z-index: 2;
    pointer-events: none;
}
.logo-strip::before {
    left: 0;
    background: linear-gradient(
        to right,
        rgba(255, 255, 255, 1),
        rgba(255, 255, 255, 0)
    );
}
.logo-strip::after {
    right: 0;
    background: linear-gradient(
        to left,
        rgba(255, 255, 255, 1),
        rgba(255, 255, 255, 0)
    );
}

/* Swiper: auto scroll “continuous” */
.logo-swiper .swiper-wrapper {
    transition-timing-function: linear !important;
}
.logo-slide {
    width: auto !important;
}

/* Chip logo (bukan kotak besar) */
.logo-chip {
    height: 150px;
    min-width: 200px; /* stabil */
    padding: 10px 16px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

/* Logo: konsisten tinggi, tidak dipaksa jadi kotak */
.logo-chip img {
    height: 100px;
    max-width: 140px;
    width: auto;
    object-fit: contain;
    /* filter: grayscale(100%); */
    opacity: 0.72;
    transition: 180ms ease;
}

.logo-chip:hover img {
    /* filter: grayscale(0%); */
    opacity: 1;
    transform: translateY(-1px);
}
</style>
