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
            <Button variant="primary" size="lg" @click="capture">
                📸 Ambil Kehadiran
            </Button>

            <Button variant="ghost" size="md" @click="refreshCamera">
                🔁 Muat ulang kamera
            </Button>
        </div>
    </div>
</template>

<script setup>
import Button from '@/components/Button.vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { triggerAlert } from '../utils/alert';

const video = ref(null);
const stream = ref(null);

const initCamera = async () => {
    try {
        const s = await navigator.mediaDevices.getUserMedia({ video: true });
        stream.value = s;
        if (video.value) {
            video.value.srcObject = s;
        }
    } catch (e) {
        console.error(e);
        triggerAlert('error', 'Gagal mengakses kamera. Periksa izin browser.');
    }
};

onMounted(() => {
    initCamera();
});

onBeforeUnmount(() => {
    if (stream.value) {
        stream.value.getTracks().forEach((t) => t.stop());
    }
});

const capture = () => {
    triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
};

const refreshCamera = () => {
    triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
    // if (stream.value) {
    //     stream.value.getTracks().forEach((t) => t.stop());
    //     stream.value = null;
    // }
    // initCamera();
};
</script>

<style scoped>
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
