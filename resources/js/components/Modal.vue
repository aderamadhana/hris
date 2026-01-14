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
    z-index: 900;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
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
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(2, 6, 23, 0.08);
    max-height: min(82vh, 760px);
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 100%;
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

/* Mobile specific */
@media (max-width: 480px) {
    .modal-root {
        padding: 12px;
    }
    .modal-panel {
        border-radius: 14px;
        max-height: calc(100vh - 24px);
    }
}

/* ===========================
   IMPORTANT: SLOT CONTENT
   =========================== */
:deep(.modal-header) {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 18px 0;
    margin-bottom: 6px;
    flex-shrink: 0;
}

:deep(.modal-header h3) {
    margin: 0;
    font-size: 17px;
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
    padding: 0 18px;
    margin: 4px 0 10px;
    font-size: 12.5px;
    color: #4b5563;
    line-height: 1.5;
    flex-shrink: 0;
}

:deep(.modal-body) {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 0 18px;
    margin-bottom: 10px;
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    min-height: 0;
}

/* Form groups */
:deep(.form-group) {
    display: flex;
    flex-direction: column;
    margin-bottom: 0;
}

:deep(.field-label) {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

:deep(.required) {
    color: #ef4444;
    margin-left: 2px;
}

/* Form inputs - CRITICAL: Prevent overflow */
:deep(.form-input) {
    width: 100%;
    max-width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    color: #374151;
    background: white;
    box-sizing: border-box;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

/* Select specific styling */
:deep(select.form-input) {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L2 4h8z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 12px;
    padding-right: 36px;
    cursor: pointer;
}

:deep(.form-input:focus) {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

:deep(.error-text) {
    font-size: 12px;
    color: #ef4444;
    margin-top: 4px;
    display: block;
}

:deep(.hint) {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;
    margin: 0;
}

/* Download button */
:deep(.download-template-btn) {
    width: 100%;
    max-width: 100%;
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
    box-sizing: border-box;
}
:deep(.download-template-btn:hover) {
    background: #eff6ff;
    border-color: #2563eb;
    color: #2563eb;
}

/* Footer */
:deep(.modal-footer) {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding: 12px 18px 16px;
    border-top: 1px solid #f3f4f6;
    margin-top: auto;
    flex-shrink: 0;
}

/* Button styles - Pill shaped like screenshot */
:deep(.modal-footer .btn-cancel),
:deep(.modal-footer .btn-primary) {
    padding: 9px 18px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
:deep(.modal-footer .btn-cancel) {
    background: #6b7280;
    color: #fff;
}
:deep(.modal-footer .btn-cancel:hover) {
    background: #4b5563;
}
:deep(.modal-footer .btn-primary) {
    background: #3b82f6;
    color: #fff;
}
:deep(.modal-footer .btn-primary:hover) {
    background: #2563eb;
}
:deep(.modal-footer .btn-primary:disabled),
:deep(.modal-footer .btn-cancel:disabled) {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Mobile optimizations */
@media (max-width: 480px) {
    :deep(.modal-header) {
        padding: 14px 16px 0;
    }
    :deep(.modal-header h3) {
        font-size: 16px;
    }
    :deep(.modal-text) {
        padding: 0 16px;
        font-size: 12px;
    }
    :deep(.modal-body) {
        padding: 0 16px;
        gap: 8px;
    }
    :deep(.form-input) {
        font-size: 14px;
        padding: 9px 11px;
    }
    :deep(select.form-input) {
        padding-right: 34px;
    }
    :deep(.modal-footer) {
        padding: 12px 16px 14px;
        gap: 6px;
    }
    :deep(.modal-footer .btn-cancel),
    :deep(.modal-footer .btn-primary) {
        padding: 8px 16px;
        font-size: 12.5px;
    }
}
</style>
