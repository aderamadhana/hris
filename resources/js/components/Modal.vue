<template>
    <!-- Root modal: nutup seluruh layar -->
    <div class="modal-root">
        <!-- Backdrop gelap transparan -->
        <div class="modal-backdrop"></div>

        <!-- Panel konten di tengah -->
        <div class="modal-panel" :class="sizeClass" @click.stop>
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        size: {
            type: String,
            default: 'sm', // md | lg | xl
        },
    },
    computed: {
        sizeClass() {
            return `modal-${this.size}`;
        },
    },
};
</script>

<style scoped>
/* default */
.modal-sm {
    width: 20vw;
    max-width: 480px;
}

.modal-md {
    width: 40vw;
    max-width: 720px;
}

/* LARGE */
.modal-lg {
    width: 80vw;
    max-width: 1200px;
}

/* EXTRA LARGE (optional) */
.modal-xl {
    width: 95vw;
    max-width: 1600px;
}
.modal-root {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

/* dst: .modal-header, .modal-text, .modal-body, .hint, .modal-footer */
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #111827;
}

.modal-close {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
    color: #6b7280;
}

.modal-text {
    margin: 8px 0 14px;
    font-size: 15px;
    color: #4b5563;
}

.modal-body {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 14px;
}

.modal-body input[type='file'] {
    font-size: 14px;
}

.hint {
    font-size: 14px;
    color: #6b7280;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.modal-root {
    position: fixed;
    inset: 0;
    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 20px;
}

.modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(2px);
}

.modal-panel {
    position: relative;
    z-index: 1;

    width: min(760px, 100%);
    max-height: min(82vh, 760px);

    background: #fff;
    border-radius: 18px;
    overflow: hidden;

    box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(2, 6, 23, 0.08);
}

/* kalau kamu punya sizeClass */
.modal-panel.modal-sm {
    width: min(520px, 100%);
}
.modal-panel.modal-md {
    width: min(760px, 100%);
}
.modal-panel.modal-lg {
    width: min(980px, 100%);
}

@media (max-width: 480px) {
    .modal-root {
        padding: 12px;
    }
    .modal-panel {
        border-radius: 14px;
        max-height: 86vh;
    }
}
</style>
