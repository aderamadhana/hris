<template>
    <div class="modal-root" @click="$emit('close')">
        <div class="modal-backdrop"></div>

        <!-- stop click supaya klik di panel tidak menutup -->
        <div class="modal-panel" :class="sizeClass" @click.stop>
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    emits: ['close'],
    props: {
        size: { type: String, default: 'md' }, // sm | md | lg | xl
    },
    computed: {
        sizeClass() {
            return `modal-${this.size}`;
        },
    },
};
</script>

<style scoped>
/* ROOT & BACKDROP */
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

/* PANEL */
.modal-panel {
    position: relative;
    z-index: 1;
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(2, 6, 23, 0.08);
    max-height: min(82vh, 760px);
    display: flex;
    flex-direction: column;
}

/* SIZE VARIANTS */
.modal-panel.modal-sm {
    width: min(520px, 100%);
}
.modal-panel.modal-md {
    width: min(760px, 100%);
}
.modal-panel.modal-lg {
    width: min(980px, 100%);
}
.modal-panel.modal-xl {
    width: min(1200px, 100%);
}

/* ===========================
   IMPORTANT: SLOT CONTENT
   =========================== */
:deep(.modal-header) {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 18px 20px 0;
    margin-bottom: 8px;
}

:deep(.modal-header h3) {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    line-height: 1.4;
}

:deep(.modal-close) {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    color: #6b7280;
    padding: 4px;
    border-radius: 6px;
    transition: all 0.2s;
    flex-shrink: 0;
}
:deep(.modal-close:hover) {
    background: #f3f4f6;
    color: #111827;
}

:deep(.modal-text) {
    padding: 0 20px;
    margin: 6px 0 12px;
    font-size: 13px;
    color: #4b5563;
    line-height: 1.5;
}

:deep(.modal-body) {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 0 20px;
    margin-bottom: 12px;
    flex: 1;
    overflow-y: auto;
}

/* form */
:deep(.form-label) {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}
:deep(.form-control) {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    color: #374151;
    background: white;
}

:deep(.hint) {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;
    margin: 0;
}

/* Download button full-width seperti screenshot */
:deep(.download-template-btn) {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 14px;
    background: transparent;
    color: #3b82f6;
    border: 1px solid #3b82f6;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}
:deep(.download-template-btn:hover) {
    background: #eff6ff;
    border-color: #2563eb;
    color: #2563eb;
}

/* footer */
:deep(.modal-footer) {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 20px 18px;
    border-top: 1px solid #f3f4f6;
    margin-top: auto;
}

/* kalau mau pill button seperti screenshot */
:deep(.modal-footer .btn-cancel),
:deep(.modal-footer .btn-primary) {
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}
:deep(.modal-footer .btn-cancel) {
    background: #6b7280;
    color: #fff;
}
:deep(.modal-footer .btn-primary) {
    background: #3b82f6;
    color: #fff;
}
:deep(.modal-footer .btn-primary:disabled),
:deep(.modal-footer .btn-cancel:disabled) {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
