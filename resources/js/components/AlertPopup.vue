<template>
    <Teleport to="body">
        <div v-if="alerts.length" class="alert-container">
            <div
                v-for="a in alerts"
                :key="a.id"
                class="alert"
                :class="`alert-${a.type}`"
            >
                <div class="alert-icon">
                    <span v-if="a.type === 'success'">✅</span>
                    <span v-else-if="a.type === 'warning'">⚠️</span>
                    <span v-else-if="a.type === 'error'">⛔</span>
                    <span v-else>ℹ️</span>
                </div>

                <div class="alert-body">
                    <div class="alert-title">
                        <span v-if="a.type === 'success'">Berhasil</span>
                        <span v-else-if="a.type === 'warning'">Perhatian</span>
                        <span v-else-if="a.type === 'error'"
                            >Terjadi Kesalahan</span
                        >
                        <span v-else>Informasi</span>
                    </div>

                    <div class="alert-message">
                        <div v-html="a.message"></div>
                    </div>
                </div>

                <button
                    type="button"
                    class="alert-close"
                    @click="closeAlert(a.id)"
                >
                    ✕
                </button>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { alerts, closeAlert } from '../utils/alert';
</script>

<style scoped>
.alert-container {
    position: fixed;
    top: 16px;
    right: 16px;
    z-index: 2000;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* kartu alert */
.alert {
    min-width: 260px;
    max-width: 360px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 14px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25);
    font-size: 14px;
}

/* variasi warna */
.alert-success {
    border-left: 4px solid #22c55e;
}

.alert-warning {
    border-left: 4px solid #f59e0b;
}

.alert-error {
    border-left: 4px solid #ef4444;
}

.alert-info {
    border-left: 4px solid #3b82f6;
}

.alert-icon {
    font-size: 18px;
    margin-top: 2px;
}

.alert-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.alert-title {
    font-weight: 600;
    color: #111827;
}

.alert-message {
    color: #4b5563;
    font-size: 14px;
}

/* tombol close */
.alert-close {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    color: #9ca3af;
    padding: 0 2px;
}

.alert-close:hover {
    color: #4b5563;
}

/* responsive */
@media (max-width: 640px) {
    .alert-container {
        left: 12px;
        right: 12px;
        top: 12px;
    }

    .alert {
        min-width: auto;
        max-width: 100%;
    }
}
</style>
