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

.modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
}

.modal-panel {
    position: relative;
    /* max-width: 480px; */
    width: 100%;
    margin: 0 16px;
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.4);
    padding: 20px 22px 16px;
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
</style>
