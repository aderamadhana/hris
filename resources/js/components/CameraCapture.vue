<template>
    <div class="camera-wrapper">
        <div class="video-shell">
            <div class="video-header">
                <span class="status-dot"></span>
                <span class="status-text">Kamera aktif</span>
            </div>

            <video ref="video" autoplay playsinline class="video-feed"></video>
        </div>

        <div class="camera-actions">
            <Button
                variant="primary"
                size="lg"
                @click="inRange && capture()"
                :disabled="locationStatus == 'idle'"
                :title="
                    !inRange
                        ? locationStatus === 'poor_gps'
                            ? 'GPS kurang akurat. Pindah ke area terbuka lalu ambil ulang lokasi.'
                            : 'Di luar jangkauan presensi. Ambil ulang lokasi.'
                        : 'Ambil kehadiran'
                "
            >
                <font-awesome-icon :icon="['fas', 'camera']" class="btn-ic" />
                Ambil Kehadiran
            </Button>

            <!-- <Button variant="ghost" size="md" @click="refreshCamera">
                <font-awesome-icon :icon="['fas', 'rotate']" class="btn-ic" />
                Muat ulang kamera
            </Button> -->
        </div>

        <div v-if="!inRange" class="camera-hint">
            <font-awesome-icon
                :icon="['fas', 'triangle-exclamation']"
                class="btn-ic"
            />
            <span v-if="locationStatus === 'poor_gps'">
                GPS kurang akurat. Pindah ke area terbuka / dekat jendela, lalu
                ambil ulang lokasi.
            </span>
            <span v-else>
                Di luar jangkauan presensi. Ambil ulang lokasi dulu.
            </span>
        </div>
    </div>
</template>

<script>
import Button from '@/components/Button.vue';
import { triggerAlert } from '../utils/alert';

export default {
    name: 'CameraCapture',
    components: { Button },

    // ✅ WAJIB: deklarasikan props agar bisa diakses sebagai this.employeeId, dst
    props: {
        employeeId: { type: [Number, String], default: null },
        userId: { type: [Number, String], default: null },

        perusahaanId: { type: [Number, String], default: null },
        divisiId: { type: [Number, String], default: null },

        targetLat: { type: [Number, String], default: null },
        targetLng: { type: [Number, String], default: null },
        radiusPresensi: { type: [Number, String], default: null },

        currentCoords: { type: Object, default: null },
        currentLat: { type: [Number, String], default: null },
        currentLng: { type: [Number, String], default: null },
        accuracy: { type: [Number, String], default: null },

        distanceMeters: { type: [Number, String], default: null },
        distanceText: { type: String, default: '-' },
        inRange: { type: Boolean, default: false },

        locationStatus: { type: String, default: 'idle' },
        locationStatusLabel: { type: String, default: '' },
        locationStatusHint: { type: String, default: '' },
        locationLoading: { type: Boolean, default: false },
        locationError: { type: String, default: '' },

        branchAddress: { type: String, default: '' },
        lastLocationAt: { type: String, default: null },

        todayLabel: { type: String, default: '' },
        timeText: { type: String, default: '' },

        isCheckedIn: { type: Boolean, default: true },
    },

    data() {
        return {
            stream: null,
        };
    },

    mounted() {
        // Debug cepat (hapus kalau sudah beres)
        // console.log('CameraCapture props:', this.$props);
        console.log('props:', this.$props);
        console.log('attrs:', this.$attrs);
        this.initCamera();
    },

    beforeUnmount() {
        this.stopCamera();
    },

    methods: {
        async initCamera() {
            try {
                const s = await navigator.mediaDevices.getUserMedia({
                    video: true,
                });
                this.stream = s;
                if (this.$refs.video) this.$refs.video.srcObject = s;
            } catch (e) {
                console.error(e);
                triggerAlert(
                    'error',
                    'Gagal mengakses kamera. Periksa izin browser.',
                );
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach((t) => t.stop());
                this.stream = null;
            }
        },

        capture() {
            if (!this.inRange) {
                triggerAlert(
                    'error',
                    'Di luar jangkauan presensi. Ambil ulang lokasi dulu.',
                );
                return;
            }

            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },

        refreshCamera() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
            // this.stopCamera();
            // this.initCamera();
        },
    },
};
</script>

<style scoped>
.btn-ic {
    margin-right: 8px;
}

.camera-hint {
    margin-top: 8px;
    font-size: 13px;
    color: #b91c1c;
    display: flex;
    align-items: center;
    gap: 6px;
}
.camera-wrapper {
    display: flex;
    flex-direction: column;
    gap: 14px;
    width: 100%; /* full width */
}

/* frame video */
.video-shell {
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    background: radial-gradient(
        circle at top,
        #dbeafe 0%,
        #e5e7eb 35%,
        #f3f4f6 100%
    );
    border: 1px solid #e5e7eb;
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12);
    width: 100%; /* ikut lebar wrapper */
}

/* label kecil di pojok */
.video-header {
    position: absolute;
    top: 10px;
    left: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 9px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.7);
    color: #f9fafb;
    font-size: 11px;
    z-index: 2;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #22c55e;
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.25);
}

.status-text {
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

/* video responsif aspect ratio 4:3 */
.video-feed {
    width: 100%;
    display: block;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    max-height: 420px; /* boleh sesuaikan, tapi tetap responsif */
}

/* tombol-tombol */
.camera-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

/* di HP tombol utama full width */
@media (max-width: 640px) {
    .camera-actions > :first-child {
        width: 100%;
        justify-content: center;
    }
}
</style>
