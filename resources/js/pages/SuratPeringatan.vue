<template>
    <AppLayout>
        <div v-if="surat_peringatan" class="sp-info-card">
            <div class="sp-info-head">
                <div>
                    <div class="sp-info-title">
                        Informasi Surat Peringatan Terakhir
                    </div>
                    <div class="sp-info-sub">
                        Nomor:
                        <strong>{{ surat_peringatan.nomor_sp }}</strong> •
                        Tanggal:
                        <strong>{{
                            formatTanggal(surat_peringatan.tanggal_sp)
                        }}</strong>
                    </div>
                </div>

                <div class="sp-badge">
                    {{ surat_peringatan.tingkat }}
                </div>
            </div>

            <!-- FILE -->
            <div class="sp-info-file">
                <div class="sp-file-row">
                    <span class="sp-file-label">File:</span>

                    <template v-if="surat_peringatan.file_url">
                        <a
                            :href="surat_peringatan.file_url"
                            target="_blank"
                            rel="noopener"
                            class="sp-file-link"
                        >
                            Buka / Download
                        </a>
                    </template>

                    <template v-else>
                        <span>-</span>
                    </template>
                </div>

                <!-- PREVIEW PDF -->
                <div
                    v-if="
                        surat_peringatan.file_url &&
                        isPdf(surat_peringatan.file_url)
                    "
                    class="sp-preview"
                >
                    <iframe
                        :src="surat_peringatan.file_url"
                        width="100%"
                        height="520"
                        style="border: 0"
                    ></iframe>
                </div>

                <!-- PREVIEW IMAGE (opsional) -->
                <div
                    v-else-if="
                        surat_peringatan.file_url &&
                        isImage(surat_peringatan.file_url)
                    "
                    class="sp-preview"
                >
                    <img
                        :src="surat_peringatan.file_url"
                        alt="Preview Surat Peringatan"
                        style="max-width: 100%; border-radius: 8px"
                    />
                </div>

                <!-- Jika file bukan pdf/image -->
                <div
                    v-else-if="surat_peringatan.file_url"
                    class="sp-preview sp-preview-note"
                >
                    File tidak bisa dipreview. Silakan klik “Buka / Download”.
                </div>
            </div>
        </div>
        <div
            v-else
            ref="root"
            class="ud ud--sp-empty"
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
                        <!-- Icon dokumen + checklist -->
                        <svg
                            class="ud__icon"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M7 3h7l3 3v15a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M14 3v3h3"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M8 13l2 2 5-5"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <!-- Pulse (tetap) -->
                        <div class="ud__pulse"></div>
                    </div>

                    <h1 class="ud__title">Tidak Ada Surat Peringatan</h1>
                    <p class="ud__subtitle">
                        Karyawan ini belum memiliki surat peringatan yang
                        tersimpan.
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
                            Jika baru saja membuat surat peringatan, coba muat
                            ulang halaman. Jika memang tidak ada, maka tidak ada
                            file SP yang bisa ditampilkan di sini.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    components: {
        AppLayout,
        Button,
    },
    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,

            surat_peringatan: null,
            offsetTop: 0,
            parentPad: { t: 0, r: 0, b: 0, l: 0 },
        };
    },

    computed: {
        // Gabungkan semua computed properties dalam satu blok
        hasAnyEarnings() {
            const slip = this.slip;
            if (!slip) return false;

            return (
                slip.earnings?.length > 0 ||
                slip.allowances?.length > 0 ||
                slip.additional_earnings?.length > 0
            );
        },

        isSlipEmpty() {
            const s = this.slip;
            if (!s) return true;

            console.log(s);

            const hasAnyRows =
                (Array.isArray(s.earnings) && s.earnings.length) ||
                (Array.isArray(s.allowances) && s.allowances.length) ||
                (Array.isArray(s.additional_earnings) &&
                    s.additional_earnings.length) ||
                (Array.isArray(s.deductions) && s.deductions.length);

            const hasAnyTotals =
                Number(s?.total_income || 0) !== 0 ||
                Number(s?.take_home_pay || 0) !== 0 ||
                Number(s?.grand_total || 0) !== 0;

            return !hasAnyRows && !hasAnyTotals;
        },

        combinedEarnings() {
            const earnings = this.slip?.earnings || [];
            const allowances = this.slip?.allowances || [];

            return [...earnings, ...allowances];
        },

        totalPendapatan() {
            const earningsTotal = this.slip?.total_earnings || 0;
            const allowancesTotal = this.slip?.total_allowances || 0;

            return earningsTotal + allowancesTotal;
        },
    },

    mounted() {
        this.fetchSuratPeringatan();
        this.recalcLayout();
        window.addEventListener('resize', this.recalcLayout, { passive: true });
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.recalcLayout);
    },

    watch: {
        // Watch untuk auto-load ketika period ID berubah
        selectedGajiPeriodId(newVal) {
            if (newVal) {
                this.loadSlip();
            }
        },
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
        async fetchSuratPeringatan() {
            try {
                const employeeId = this.user?.employee_id;
                // Pastikan endpoint return data dari table `shift`
                // format ideal: [{id, nama_shift, keterangan}]
                const { data } = await axios.get(
                    '/dashboard/surat-peringatan/' + employeeId,
                );
                this.surat_peringatan = data.data.suratPeringatanTerakhir;
            } catch (e) {
                console.warn('Gagal fetch shift options:', e);
                this.shiftOptions = [];
            }
        },

        formatTanggal(dateStr) {
            if (!dateStr) return '-';
            // input: YYYY-MM-DD
            const [y, m, d] = dateStr.split('-');
            if (!y || !m || !d) return dateStr;
            return `${d}/${m}/${y}`;
        },

        isPdf(url) {
            return /\.(pdf)(\?.*)?$/i.test(url || '');
        },

        isImage(url) {
            return /\.(jpg|jpeg|png|webp)(\?.*)?$/i.test(url || '');
        },
    },
};
</script>

<style scoped>
/* ===== Surat Peringatan Card ===== */
.sp-info-card {
    margin-top: 14px;
    padding: 16px 16px 14px;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 14px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
    box-shadow:
        0 10px 28px rgba(15, 23, 42, 0.06),
        0 2px 6px rgba(15, 23, 42, 0.04);
    position: relative;
    overflow: hidden;
}

/* accent line */
.sp-info-card::before {
    content: '';
    position: absolute;
    inset: 0 auto 0 0;
    width: 5px;
    background: linear-gradient(180deg, #ef4444 0%, #f59e0b 50%, #3b82f6 100%);
    opacity: 0.9;
}

/* header row */
.sp-info-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding-left: 10px; /* offset because of left accent */
}

.sp-info-title {
    font-size: 15px;
    font-weight: 800;
    letter-spacing: 0.2px;
    color: #0f172a; /* slate-900 */
    line-height: 1.2;
}

.sp-info-sub {
    margin-top: 6px;
    font-size: 13px;
    line-height: 1.45;
    color: rgba(15, 23, 42, 0.72);
}

/* badge */
.sp-badge {
    flex: 0 0 auto;
    padding: 7px 11px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 12px;
    letter-spacing: 0.4px;
    border: 1px solid rgba(15, 23, 42, 0.1);
    background: #f8fafc;
    color: #0f172a;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    user-select: none;
}

/* optional: warna badge berdasarkan tingkat (kalau mau) */
.sp-badge.sp1 {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.25);
    color: #1d4ed8;
}
.sp-badge.sp2 {
    background: rgba(245, 158, 11, 0.12);
    border-color: rgba(245, 158, 11, 0.28);
    color: #b45309;
}
.sp-badge.sp3 {
    background: rgba(239, 68, 68, 0.12);
    border-color: rgba(239, 68, 68, 0.28);
    color: #b91c1c;
}

.sp-info-file {
    margin-top: 14px;
    padding: 12px 12px 10px;
    border-radius: 12px;
    border: 1px solid rgba(15, 23, 42, 0.08);
    background: rgba(248, 250, 252, 0.8);
    margin-left: 10px; /* align with header offset */
}

/* file row */
.sp-file-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.sp-file-label {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
}

.sp-file-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 800;
    font-size: 13px;
    text-decoration: none;
    color: #ffffff;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow:
        0 10px 18px rgba(37, 99, 235, 0.18),
        0 2px 6px rgba(37, 99, 235, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.14);
    transition:
        transform 0.12s ease,
        box-shadow 0.12s ease,
        opacity 0.12s ease;
}

.sp-file-link:hover {
    transform: translateY(-1px);
    box-shadow:
        0 14px 22px rgba(37, 99, 235, 0.22),
        0 3px 8px rgba(37, 99, 235, 0.2);
    opacity: 0.98;
}

.sp-file-link:active {
    transform: translateY(0);
    box-shadow:
        0 10px 18px rgba(37, 99, 235, 0.16),
        0 2px 6px rgba(37, 99, 235, 0.16);
}

/* preview container */
.sp-preview {
    margin-top: 12px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(15, 23, 42, 0.1);
    background: #ffffff;
    box-shadow:
        0 12px 22px rgba(15, 23, 42, 0.06),
        0 2px 6px rgba(15, 23, 42, 0.04);
}

.sp-preview iframe {
    display: block;
    width: 100%;
    height: 540px;
    border: 0;
    background: #fff;
}

.sp-preview img {
    display: block;
    width: 100%;
    height: auto;
}

/* note if not previewable */
.sp-preview-note {
    margin-top: 10px;
    font-size: 13px;
    color: rgba(15, 23, 42, 0.72);
    padding: 10px 12px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.8);
    border: 1px dashed rgba(15, 23, 42, 0.18);
}

/* empty state */
.sp-info-empty {
    margin-top: 14px;
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px dashed rgba(15, 23, 42, 0.2);
    background: linear-gradient(
        180deg,
        rgba(248, 250, 252, 0.9) 0%,
        rgba(255, 255, 255, 1) 100%
    );
    color: rgba(15, 23, 42, 0.75);
}

/* responsive */
@media (max-width: 640px) {
    .sp-info-card {
        padding: 14px 14px 12px;
    }

    .sp-info-head {
        flex-direction: column;
        align-items: flex-start;
    }

    .sp-file-row {
        flex-direction: column;
        align-items: stretch;
    }

    .sp-file-link {
        justify-content: center;
        width: 100%;
    }

    .sp-preview iframe {
        height: 420px;
    }
}
/* ===============================
   COMPACT VARIANT: ud untuk empty SP
   =============================== */
.ud.ud--sp-empty {
    /* jangan full-screen */
    min-height: auto !important;
    height: auto !important;
    padding: 0 !important;
    margin-top: 14px;
    background: transparent !important;
    box-shadow: none !important;
    overflow: visible !important;
}

/* Matikan dekorasi raksasa yang biasanya via pseudo-element */
.ud.ud--sp-empty::before,
.ud.ud--sp-empty::after,
.ud.ud--sp-empty .ud__content::before,
.ud.ud--sp-empty .ud__content::after {
    content: none !important;
    display: none !important;
}

.ud.ud--sp-empty .ud__content {
    /* jangan center full page */
    min-height: auto !important;
    height: auto !important;
    padding: 0 !important;
    display: block !important;
    background: transparent !important;
}

/* Jadikan container sebagai card */
.ud.ud--sp-empty .ud__container {
    width: 100%;
    margin: 0;
    padding: 16px 16px 14px;
    border-radius: 14px;
    border: 1px solid rgba(15, 23, 42, 0.1);
    background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
    box-shadow:
        0 12px 26px rgba(15, 23, 42, 0.08),
        0 2px 6px rgba(15, 23, 42, 0.05);
    position: relative;
    overflow: hidden;
}

/* Accent line biar ada “feel” penting tapi tetap clean */
.ud.ud--sp-empty .ud__container::before {
    content: '';
    position: absolute;
    inset: 0 auto 0 0;
    width: 4px;
    background: linear-gradient(180deg, #2563eb 0%, #60a5fa 100%);
    opacity: 0.95;
}

/* icon jadi kecil & rapi */
.ud.ud--sp-empty .ud__iconWrap {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    margin-bottom: 10px;
    background: rgba(37, 99, 235, 0.1);
    border: 1px solid rgba(37, 99, 235, 0.25);
    color: #1d4ed8;
    position: relative;
}

/* Matikan pulse yang bikin “ramai” */
.ud.ud--sp-empty .ud__pulse {
    display: none !important;
}

.ud.ud--sp-empty .ud__icon {
    width: 45px;
    height: 45px;
}

/* typography */
.ud.ud--sp-empty .ud__title {
    font-size: 18px !important;
    font-weight: 800 !important;
    margin: 0 0 6px !important;
    color: #0f172a;
}

.ud.ud--sp-empty .ud__subtitle {
    font-size: 13px !important;
    line-height: 1.5 !important;
    margin: 0 0 12px !important;
    color: rgba(15, 23, 42, 0.72);
}

/* info box lebih halus */
.ud.ud--sp-empty .ud__info {
    margin-top: 10px;
    display: flex;
    gap: 10px;
    padding: 12px;
    border-radius: 12px;
    background: rgba(248, 250, 252, 0.9);
    border: 1px solid rgba(15, 23, 42, 0.08);
    color: rgba(15, 23, 42, 0.72);
}

.ud.ud--sp-empty .ud__infoIcon {
    width: 40px;
    height: 40px;
    flex: 0 0 auto;
    color: rgba(37, 99, 235, 0.85);
}

@media (max-width: 640px) {
    .ud.ud--sp-empty .ud__container {
        padding: 14px 14px 12px;
    }
    .ud.ud--sp-empty .ud__title {
        font-size: 16px !important;
    }
}
</style>
