<template>
    <div class="camera-wrapper">
        <!-- Video Shell -->
        <div class="video-shell" v-show="!capturedImage">
            <div class="video-header">
                <span class="status-dot" :class="{ active: !!stream }"></span>
                <span class="status-text">
                    {{
                        stream
                            ? 'Kamera aktif'
                            : cameraError
                              ? 'Kamera bermasalah'
                              : 'Menghubungkan kamera...'
                    }}
                </span>

                <!-- Reload camera (top-right) -->
                <button
                    type="button"
                    class="reload-cam-btn"
                    @click="reloadCamera"
                    :disabled="isReloading || isCapturing || isSubmitting"
                    title="Muat ulang kamera"
                >
                    <font-awesome-icon
                        :icon="faSync"
                        :class="{ 'fa-spin': isReloading }"
                    />
                </button>
            </div>

            <video ref="video" autoplay playsinline class="video-feed"></video>
        </div>

        <!-- Captured Image Preview -->
        <div class="captured-preview" v-if="capturedImage">
            <img :src="capturedImage" alt="Foto Presensi" />
        </div>

        <!-- Camera Actions -->
        <div class="camera-actions">
            <!-- When image captured: show Retake + Submit -->
            <template v-if="capturedImage">
                <Button
                    variant="secondary"
                    size="lg"
                    @click="retakePhoto"
                    :disabled="isSubmitting || isCapturing || isReloading"
                >
                    <font-awesome-icon :icon="faRotate" class="btn-ic" />
                    Ambil Ulang Foto
                </Button>

                <Button
                    variant="success"
                    size="lg"
                    @click="submitPresensi()"
                    :disabled="isSubmitting || !capturedBlob || isReloading"
                >
                    <font-awesome-icon
                        :icon="isSubmitting ? faSpinner : faCircleCheck"
                        class="btn-ic"
                        :class="{ 'fa-spin': isSubmitting }"
                    />
                    {{ isSubmitting ? 'Menyimpan...' : 'Konfirmasi Presensi' }}
                </Button>
            </template>

            <!-- When no image: show Capture -->
            <template v-else>
                <Button
                    variant="primary"
                    size="lg"
                    @click="capture"
                    :disabled="
                        locationStatus === 'idle' || isCapturing || isReloading
                    "
                    :title="
                        !inRange
                            ? locationStatus === 'poor_gps'
                                ? locationStatusHint
                                : 'Di luar jangkauan presensi. Ambil ulang lokasi.'
                            : 'Ambil foto untuk presensi'
                    "
                >
                    <font-awesome-icon
                        :icon="isCapturing ? faSpinner : faCamera"
                        class="btn-ic"
                        :class="{ 'fa-spin': isCapturing }"
                    />
                    {{ isCapturing ? 'Mengambil Foto...' : 'Ambil Foto' }}
                </Button>
            </template>
        </div>

        <!-- Camera / Location Hints -->
        <div v-if="cameraError" class="camera-hint error">
            <font-awesome-icon
                :icon="faTriangleExclamation"
                class="hint-icon"
            />
            <span>{{ cameraError }} Klik tombol muat ulang kamera.</span>
        </div>

        <div
            v-if="!inRange && locationStatus !== 'idle'"
            class="camera-hint error"
        >
            <font-awesome-icon
                :icon="faTriangleExclamation"
                class="hint-icon"
            />
            <span v-if="locationStatus === 'poor_gps'">
                GPS kurang akurat. Pindah ke area terbuka / dekat jendela, lalu
                ambil ulang lokasi.
            </span>
            <span v-else>
                Anda di luar jangkauan presensi. Ambil ulang lokasi terlebih
                dahulu.
            </span>
        </div>

        <div v-if="inRange && !capturedImage" class="camera-hint success">
            <font-awesome-icon :icon="faCircleCheck" class="hint-icon" />
            <span
                >Lokasi valid. Silakan ambil foto untuk melakukan
                presensi.</span
            >
        </div>

        <div v-if="capturedImage" class="camera-hint info">
            <font-awesome-icon :icon="faCircleInfo" class="hint-icon" />
            <span>
                Periksa foto Anda. Klik "Ambil Ulang" jika ingin mengulang, atau
                "Konfirmasi" untuk melanjutkan.
            </span>
        </div>
    </div>
</template>

<script>
import Button from '@/components/Button.vue';
import axios from 'axios';
import { triggerAlert } from '../utils/alert';

import {
    faCamera,
    faCircleCheck,
    faCircleInfo,
    faRotate,
    faSpinner,
    faSync,
    faTriangleExclamation,
} from '@fortawesome/free-solid-svg-icons';

export default {
    name: 'CameraCapture',
    components: { Button },

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
            // icons exposed to template
            faCircleCheck,
            faTriangleExclamation,
            faCircleInfo,
            faRotate,
            faCamera,
            faSpinner,
            faSync,

            stream: null,
            capturedImage: null,
            capturedBlob: null,

            isCapturing: false,
            isSubmitting: false,
            isReloading: false,

            cameraError: '',
        };
    },

    mounted() {
        this.initCamera();
    },

    beforeUnmount() {
        this.stopCamera();
    },

    methods: {
        // ================= CAMERA =================
        async initCamera() {
            this.cameraError = '';

            try {
                if (!navigator.mediaDevices?.getUserMedia) {
                    this.cameraError = 'Browser tidak mendukung akses kamera.';
                    triggerAlert('error', this.cameraError);
                    return;
                }

                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                    },
                    audio: false,
                });

                if (this.$refs.video) {
                    this.$refs.video.srcObject = this.stream;
                }
            } catch (error) {
                console.error('Camera error:', error);

                let errorMessage = 'Tidak dapat mengakses kamera.';
                if (error?.name === 'NotAllowedError') {
                    errorMessage =
                        'Akses kamera ditolak. Mohon izinkan akses kamera di browser.';
                } else if (error?.name === 'NotFoundError') {
                    errorMessage = 'Kamera tidak ditemukan di perangkat Anda.';
                } else if (error?.name === 'NotReadableError') {
                    errorMessage = 'Kamera sedang digunakan aplikasi lain.';
                }

                this.cameraError = errorMessage;
                triggerAlert('error', errorMessage);
            }
        },

        stopCamera() {
            try {
                if (this.$refs.video) this.$refs.video.srcObject = null;
            } catch (_) {}

            if (this.stream) {
                this.stream.getTracks().forEach((track) => track.stop());
                this.stream = null;
            }
        },

        async reloadCamera() {
            this.isReloading = true;
            try {
                // reset preview/state
                this.capturedImage = null;
                this.capturedBlob = null;
                this.cameraError = '';

                // restart stream
                this.stopCamera();
                await this.$nextTick();
                await this.initCamera();
            } finally {
                this.isReloading = false;
            }
        },

        // ================= CAPTURE =================
        async capture() {
            if (!this.currentLat || !this.currentLng) {
                triggerAlert(
                    'error',
                    'Lokasi belum terdeteksi. Ambil lokasi terlebih dahulu.',
                );
                return;
            }

            if (!this.$refs.video || !this.stream) {
                triggerAlert(
                    'error',
                    'Kamera belum siap. Coba muat ulang kamera.',
                );
                return;
            }

            this.isCapturing = true;

            try {
                const canvas = document.createElement('canvas');
                const video = this.$refs.video;

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);

                const blob = await new Promise((resolve) => {
                    canvas.toBlob(resolve, 'image/jpeg', 0.8);
                });

                if (!blob) {
                    triggerAlert(
                        'error',
                        'Gagal membuat file foto. Coba lagi.',
                    );
                    return;
                }

                this.capturedBlob = blob;
                this.capturedImage = canvas.toDataURL('image/jpeg', 0.8);
            } catch (error) {
                console.error('Capture error:', error);
                triggerAlert('error', 'Gagal mengambil foto. Coba lagi.');
            } finally {
                this.isCapturing = false;
            }
        },

        // ================= SUBMIT PRESENSI =================
        async submitPresensi(photoBlob) {
            const blobToSend = photoBlob || this.capturedBlob;
            if (!blobToSend) {
                triggerAlert(
                    'error',
                    'Foto belum tersedia. Ambil foto terlebih dahulu.',
                );
                return;
            }

            this.isSubmitting = true;

            try {
                const formData = new FormData();

                formData.append('employee_id', this.employeeId);
                formData.append('user_id', this.userId);
                formData.append('perusahaan_id', this.perusahaanId);
                formData.append('divisi_id', this.divisiId);

                formData.append('latitude', this.currentLat);
                formData.append('longitude', this.currentLng);
                formData.append('accuracy', this.accuracy);
                formData.append('alamat', this.branchAddress || '-');

                formData.append('target_latitude', this.targetLat);
                formData.append('target_longitude', this.targetLng);
                formData.append('radius_presensi', this.radiusPresensi);
                formData.append('jarak_dari_lokasi', this.distanceMeters);
                formData.append('in_range', this.inRange ? 1 : 0);

                formData.append(
                    'jenis_presensi',
                    this.isCheckedIn ? 'pulang' : 'masuk',
                );
                formData.append('waktu', new Date().toISOString());
                formData.append(
                    'tanggal',
                    new Date().toISOString().split('T')[0],
                );

                formData.append(
                    'foto',
                    blobToSend,
                    `presensi_${Date.now()}.jpg`,
                );

                const response = await axios.post('/presensi/store', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                    onUploadProgress: (progressEvent) => {
                        if (!progressEvent.total) return;
                        const percentCompleted = Math.round(
                            (progressEvent.loaded * 100) / progressEvent.total,
                        );
                        console.log(`Upload: ${percentCompleted}%`);
                    },
                });

                if (response?.data?.success) {
                    triggerAlert('success', 'Presensi berhasil disimpan!');

                    this.stopCamera();

                    // NOTE: router.visit di kode kamu sebelumnya tidak di-import.
                    // Pastikan router tersedia (mis. Inertia router) sebelum dipakai.
                    setTimeout(() => {
                        if (typeof router !== 'undefined'?.visit)
                            router.visit('/karyawan/presensi/riwayat');
                        else
                            window.location.href = '/karyawan/presensi/riwayat';
                    }, 1500);
                } else {
                    triggerAlert(
                        'error',
                        response?.data?.message || 'Gagal menyimpan presensi',
                    );
                }
            } catch (error) {
                console.error('Submit error:', error);

                let errorMessage = 'Gagal menyimpan presensi. Coba lagi.';
                if (error?.response) {
                    errorMessage = error.response.data?.message || errorMessage;
                } else if (error?.request) {
                    errorMessage =
                        'Tidak ada koneksi internet. Periksa jaringan Anda.';
                }

                triggerAlert('error', errorMessage);
            } finally {
                this.isSubmitting = false;
                this.capturedImage = null;
                this.capturedBlob = null;
            }
        },

        // ================= HELPER =================
        retakePhoto() {
            this.capturedImage = null;
            this.capturedBlob = null;

            // kalau stream mati, nyalakan lagi
            if (!this.stream) this.reloadCamera();
        },
    },
};
</script>

<style scoped>
/* ========== Base ========== */
.camera-wrapper {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 14px;
    min-width: 0;
}

.btn-ic {
    margin-right: 8px;
}

/* ========== Video / Preview Shell ========== */
.video-shell,
.captured-preview {
    position: relative;
    width: 100%;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: radial-gradient(
        circle at top,
        #dbeafe 0%,
        #e5e7eb 35%,
        #f3f4f6 100%
    );
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12);
}

/* Video element */
.video-feed {
    width: 100%;
    display: block;
    aspect-ratio: 4 / 3;
    object-fit: cover;

    /* adaptif: tinggi ikut viewport */
    max-height: min(480px, 55vh);
}

/* Captured preview image */
.captured-preview img {
    width: 100%;
    display: block;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    max-height: min(480px, 55vh);
}

/* ========== Overlay Header (Status + Reload) ========== */
.video-header {
    position: absolute;
    top: 10px;
    left: 12px;
    right: 12px;
    z-index: 2;

    display: flex;
    align-items: center;
    gap: 8px;

    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.72);
    color: #f9fafb;

    backdrop-filter: blur(6px);
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #94a3b8;
}

.status-dot.active {
    background: #22c55e;
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.25);
}

.status-text {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    min-width: 0;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* reload button stays on the far right */
.reload-cam-btn {
    margin-left: auto;
    border: 0;
    background: transparent;
    color: #f9fafb;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 999px;
    opacity: 0.95;
    line-height: 1;
}

.reload-cam-btn:hover {
    background: rgba(255, 255, 255, 0.12);
}

.reload-cam-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ========== Actions ========== */
.camera-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    width: 100%;
}

/* make buttons behave nicely even if Button component wraps */
.camera-actions > * {
    flex: 1 1 240px;
    min-width: 220px;
}

/* ========== Hints ========== */
.camera-hint {
    width: 100%;
    display: flex;
    align-items: flex-start;
    gap: 8px;

    font-size: 13px;
    line-height: 1.35;

    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid transparent;
    background: #f8fafc;
    color: #334155;
}

.camera-hint .hint-icon {
    margin-top: 2px;
    flex: 0 0 auto;
}

/* variants */
.camera-hint.error {
    background: #fef2f2;
    border-color: #fecaca;
    color: #991b1b;
}

.camera-hint.success {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #166534;
}

.camera-hint.info {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1e40af;
}

/* ========== Responsive Breakpoints ========== */
@media (max-width: 768px) {
    .video-feed,
    .captured-preview img {
        max-height: min(520px, 62vh);
        aspect-ratio: 3 / 4; /* lebih enak di HP (portrait feel) */
    }

    .camera-actions > * {
        flex: 1 1 100%;
        min-width: 0;
    }

    .video-header {
        top: 8px;
        left: 10px;
        right: 10px;
        padding: 6px 10px;
    }

    .status-text {
        font-size: 10.5px;
    }
}

@media (max-width: 420px) {
    .video-feed,
    .captured-preview img {
        max-height: 65vh;
    }

    .video-header {
        padding: 6px 8px;
    }

    .reload-cam-btn {
        padding: 4px 6px;
    }
}
</style>
