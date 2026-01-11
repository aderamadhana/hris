<template>
    <AppLayout>
        <div
            ref="root"
            class="ud"
            :style="{
                '--ud-offset-top': offsetTop + 'px',
                '--ud-pl': parentPad.l + 'px',
                '--ud-pr': parentPad.r + 'px',
                '--ud-pt': parentPad.t + 'px',
                '--ud-pb': parentPad.b + 'px',
            }"
        >
            <div class="ud__content">
                <div class="ud__container">
                    <div class="ud__iconWrap">
                        <svg
                            class="ud__icon"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M14.7 6.3C15.1 5.9 15.1 5.3 14.7 4.9C14.3 4.5 13.7 4.5 13.3 4.9L8.7 9.5C8.3 9.9 8.3 10.5 8.7 10.9L13.3 15.5C13.7 15.9 14.3 15.9 14.7 15.5C15.1 15.1 15.1 14.5 14.7 14.1L10.8 10.2L14.7 6.3Z"
                                fill="currentColor"
                            />
                            <path
                                d="M9.3 6.3C8.9 5.9 8.9 5.3 9.3 4.9C9.7 4.5 10.3 4.5 10.7 4.9L15.3 9.5C15.7 9.9 15.7 10.5 15.3 10.9L10.7 15.5C10.3 15.9 9.7 15.9 9.3 15.5C8.9 15.1 8.9 14.5 9.3 14.1L13.2 10.2L9.3 6.3Z"
                                fill="currentColor"
                            />
                        </svg>
                        <div class="ud__pulse"></div>
                    </div>

                    <h1 class="ud__title">Halaman Dalam Pengembangan</h1>
                    <p class="ud__subtitle">
                        Fitur ini sedang dalam proses pengembangan dan akan
                        segera hadir
                    </p>

                    <div class="ud__info">
                        <svg
                            class="ud__infoIcon"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="2"
                            />
                            <path
                                d="M12 16V12"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                            <circle cx="12" cy="8" r="1" fill="currentColor" />
                        </svg>

                        <p>
                            Tim kami sedang bekerja keras untuk menghadirkan
                            pengalaman terbaik untuk Anda. Terima kasih atas
                            kesabaran Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/layouts/AppLayout.vue';

export default {
    name: 'UnderDevelopment',
    components: { AppLayout },

    data() {
        return {
            offsetTop: 0,
            parentPad: { t: 0, r: 0, b: 0, l: 0 },
        };
    },

    mounted() {
        this.recalcLayout();
        window.addEventListener('resize', this.recalcLayout, { passive: true });
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.recalcLayout);
    },

    methods: {
        reCalcParentPadding(el) {
            const parent = el?.parentElement;
            if (!parent) return;

            const cs = window.getComputedStyle(parent);
            const toNum = (v) => Math.max(0, Math.round(parseFloat(v) || 0));

            this.parentPad = {
                t: toNum(cs.paddingTop),
                r: toNum(cs.paddingRight),
                b: toNum(cs.paddingBottom),
                l: toNum(cs.paddingLeft),
            };
        },

        recalcOffsetTop(el) {
            if (!el) return;
            const rect = el.getBoundingClientRect();
            this.offsetTop = Math.max(0, Math.round(rect.top));
        },

        recalcLayout() {
            this.$nextTick(() => {
                const el = this.$refs.root;
                if (!el) return;

                // 1) baca padding container slot dari AppLayout (biar bisa di-cancel lokal)
                this.reCalcParentPadding(el);

                // 2) ukur ulang offsetTop setelah CSS var padding ter-apply
                this.$nextTick(() => {
                    this.recalcOffsetTop(el);
                });
            });
        },
    },
};
</script>

<style scoped>
/* scoped: tidak akan mempengaruhi halaman lain */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/*
  FULL-BLEED LOKAL:
  cancel padding dari wrapper content AppLayout tanpa ubah global/layout lain.
*/
.ud {
    margin-top: calc(-1 * var(--ud-pt));
    margin-right: calc(-1 * var(--ud-pr));
    margin-bottom: calc(-1 * var(--ud-pb));
    margin-left: calc(-1 * var(--ud-pl));

    width: calc(100% + var(--ud-pl) + var(--ud-pr));

    /* tingginya mengikuti viewport dikurangi posisi aktual (header/topbar ikut kehitung) */
    height: calc(100dvh - var(--ud-offset-top));
    min-height: calc(100dvh - var(--ud-offset-top));

    display: flex;
    background: #f7fafc;

    overflow: hidden; /* cegah animasi ring bikin scroll */
}

.ud__content {
    flex: 1;
    min-height: 0;
    width: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: clamp(16px, 3vw, 40px);
    overflow: hidden;
}

.ud__container {
    width: min(640px, 100%);
    text-align: center;
}

/* ICON */
.ud__iconWrap {
    position: relative;
    display: inline-block;
    margin-bottom: 18px;
}

.ud__icon {
    width: clamp(72px, 10vw, 96px);
    height: clamp(72px, 10vw, 96px);
    color: #38b2ac;
    animation: udFloat 3s ease-in-out infinite;
}

@keyframes udFloat {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

.ud__pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: clamp(96px, 12vw, 120px);
    height: clamp(96px, 12vw, 120px);
    border: 2px solid #38b2ac;
    border-radius: 999px;
    animation: udPulse 2s ease-out infinite;
    opacity: 0;
    pointer-events: none;
}

@keyframes udPulse {
    0% {
        transform: translate(-50%, -50%) scale(0.85);
        opacity: 0.9;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.25);
        opacity: 0;
    }
}

/* TYPO */
.ud__title {
    font-size: clamp(1.6rem, 3vw, 2.35rem);
    color: #2d3748;
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 10px;
}

.ud__subtitle {
    font-size: clamp(0.95rem, 1.6vw, 1.1rem);
    color: #718096;
    line-height: 1.55;
    margin-bottom: 18px;
}

/* INFO BOX */
.ud__info {
    max-width: 560px;
    margin: 0 auto;
    background: #e6fffa;
    border: 1px solid #81e6d9;
    border-radius: 14px;
    padding: 16px;

    display: flex;
    gap: 12px;
    align-items: flex-start;
    text-align: left;
}

.ud__infoIcon {
    width: 22px;
    height: 22px;
    color: #38b2ac;
    flex-shrink: 0;
    margin-top: 2px;
}

.ud__info p {
    color: #2c7a7b;
    line-height: 1.6;
    font-size: 0.95rem;
}

/* layar pendek / zoom */
@media (max-height: 720px) {
    .ud__iconWrap {
        margin-bottom: 12px;
    }
    .ud__subtitle {
        margin-bottom: 12px;
    }
    .ud__info {
        padding: 14px;
    }
}
</style>
