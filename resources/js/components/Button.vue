<script setup>
import { computed, useAttrs } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'primary' }, // primary | secondary | ghost | danger | success | warning | outline
    size: { type: String, default: 'md' }, // sm | md | lg
    block: Boolean,
    loading: Boolean,
    disabled: Boolean,
    iconOnly: Boolean,
});

const attrs = useAttrs();

const classes = computed(() => [
    'btn-base',
    `btn--${props.variant}`,
    `btn--${props.size}`,
    {
        'btn--block': props.block,
        'btn--icon-only': props.iconOnly,
        'is-loading': props.loading,
    },
    attrs.class,
]);
</script>

<template>
    <button :class="classes" :disabled="disabled || loading" v-bind="attrs">
        <span v-if="loading" class="btn-spinner" aria-hidden="true" />
        <span class="btn-label">
            <slot />
        </span>
    </button>
</template>

<style scoped>
/* ================= BASE ================= */
/* ================= BASE ================= */
.btn-base {
    --bg: #fff;
    --bg-hover: #f3f4f6;
    --bg-active: #e5e7eb;
    --border: transparent;
    --color: #111827;
    --shadow: none;
    --shadow-hover: none;
    --shadow-active: none;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding-inline: 16px;
    border-radius: 999px;
    border: 1px solid var(--border);
    background: var(--bg);
    color: var(--color);
    font-weight: 500;
    letter-spacing: 0.01em;
    white-space: nowrap;
    cursor: pointer;
    box-shadow: var(--shadow);
    transition:
        background-color 0.14s ease,
        border-color 0.14s ease,
        color 0.14s ease,
        box-shadow 0.14s ease,
        transform 0.08s ease;
}

/* ================= STATE ================= */
.btn-base:hover:not(:disabled) {
    background: var(--bg-hover);
    box-shadow: var(--shadow-hover);
    transform: translateY(-1px);
}

.btn-base:active:not(:disabled) {
    background: var(--bg-active);
    box-shadow: var(--shadow-active);
    transform: translateY(0);
}

button:disabled,
.is-loading {
    opacity: 0.6;
    cursor: default;
    box-shadow: none;
    transform: none;
}

/* ================= SIZE ================= */
.btn--sm {
    padding: 6px 12px;
    font-size: 13px;
}
.btn--md {
    padding: 9px 16px;
    font-size: 14px;
}
.btn--lg {
    padding: 11px 20px;
    font-size: 15px;
}

/* ================= MODIFIER ================= */
.btn--block {
    width: 100%;
}
.btn--icon-only {
    padding-inline: 10px;
}

/* ================= VARIANTS ================= */

/* PRIMARY */
.btn--primary {
    --bg: linear-gradient(135deg, #0ea5e9, #3b82f6);
    --bg-hover: linear-gradient(135deg, #0284c7, #2563eb);
    --bg-active: linear-gradient(135deg, #0369a1, #1d4ed8);
    --color: #ffffff;
    --shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
    --shadow-hover: 0 10px 22px rgba(37, 99, 235, 0.45);
    --shadow-active: 0 6px 14px rgba(37, 99, 235, 0.35);
}

/* SECONDARY */
.btn--secondary {
    --bg: linear-gradient(135deg, #475569, #64748b);
    --bg-hover: linear-gradient(135deg, #334155, #475569);
    --bg-active: linear-gradient(135deg, #1e293b, #334155);
    --color: #ffffff;
    --shadow: 0 8px 18px rgba(71, 85, 105, 0.35);
    --shadow-hover: 0 10px 22px rgba(71, 85, 105, 0.45);
    --shadow-active: 0 6px 14px rgba(71, 85, 105, 0.35);
}

/* GHOST */
.btn--ghost {
    --bg: rgba(255, 255, 255, 0.8);
    --bg-hover: rgba(248, 250, 252, 0.95);
    --bg-active: rgba(241, 245, 249, 1);
    --border: rgba(226, 232, 240, 0.8);
    --color: #334155;
    --shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    --shadow-hover: 0 6px 16px rgba(15, 23, 42, 0.1);
    --shadow-active: 0 3px 8px rgba(15, 23, 42, 0.08);
}

/* DANGER */
.btn--danger {
    --bg: linear-gradient(135deg, #ef4444, #dc2626);
    --bg-hover: linear-gradient(135deg, #dc2626, #b91c1c);
    --bg-active: linear-gradient(135deg, #b91c1c, #991b1b);
    --color: #ffffff;
    --shadow: 0 8px 18px rgba(239, 68, 68, 0.35);
    --shadow-hover: 0 10px 22px rgba(239, 68, 68, 0.45);
    --shadow-active: 0 6px 14px rgba(239, 68, 68, 0.35);
}

/* SUCCESS */
.btn--success {
    --bg: linear-gradient(135deg, #10b981, #059669);
    --bg-hover: linear-gradient(135deg, #059669, #047857);
    --bg-active: linear-gradient(135deg, #047857, #065f46);
    --color: #ffffff;
    --shadow: 0 8px 18px rgba(16, 185, 129, 0.35);
    --shadow-hover: 0 10px 22px rgba(16, 185, 129, 0.45);
    --shadow-active: 0 6px 14px rgba(16, 185, 129, 0.35);
}

/* WARNING */
.btn--warning {
    --bg: linear-gradient(135deg, #f59e0b, #d97706);
    --bg-hover: linear-gradient(135deg, #d97706, #b45309);
    --bg-active: linear-gradient(135deg, #b45309, #92400e);
    --color: #ffffff;
    --shadow: 0 8px 18px rgba(245, 158, 11, 0.35);
    --shadow-hover: 0 10px 22px rgba(245, 158, 11, 0.45);
    --shadow-active: 0 6px 14px rgba(245, 158, 11, 0.35);
}

/* OUTLINE */
.btn--outline {
    --bg: transparent;
    --bg-hover: rgba(59, 130, 246, 0.05);
    --bg-active: rgba(59, 130, 246, 0.1);
    --border: #3b82f6;
    --color: #3b82f6;
    --shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    --shadow-hover: 0 6px 16px rgba(59, 130, 246, 0.25);
    --shadow-active: 0 3px 8px rgba(59, 130, 246, 0.2);
}

/* ================= SPINNER ================= */
.btn-spinner {
    width: 16px;
    height: 16px;
    border-radius: 999px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #ffffff;
    animation: spin 0.7s linear infinite;
}

.btn--ghost .btn-spinner {
    border-color: rgba(51, 65, 85, 0.3);
    border-top-color: #334155;
}

.btn--outline .btn-spinner {
    border-color: rgba(59, 130, 246, 0.3);
    border-top-color: #3b82f6;
}

.btn-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
