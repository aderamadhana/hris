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
    --bg: #e5e7eb;
    --bg-hover: #d1d5db;
    --bg-active: #cbd5f5;
    --border: #d1d5db;
    --color: #111827;
    --shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
}

/* GHOST */
.btn--ghost {
    --bg: #ffffff;
    --bg-hover: #f9fafb;
    --bg-active: #f3f4f6;
    --border: #e5e7eb;
    --color: #111827;
}

/* DANGER */
.btn--danger {
    --bg: #fee2e2;
    --bg-hover: #fecaca;
    --bg-active: #fca5a5;
    --border: #fecaca;
    --color: #b91c1c;
}

/* SUCCESS */
.btn--success {
    --bg: #dcfce7;
    --bg-hover: #bbf7d0;
    --bg-active: #86efac;
    --border: #bbf7d0;
    --color: #166534;
}

/* WARNING */
.btn--warning {
    --bg: #fef3c7;
    --bg-hover: #fde68a;
    --bg-active: #fcd34d;
    --border: #fde68a;
    --color: #92400e;
}

/* OUTLINE */
.btn--outline {
    --bg: transparent;
    --bg-hover: #f9fafb;
    --bg-active: #f3f4f6;
    --border: #d1d5db;
    --color: #111827;
}

/* ================= SPINNER ================= */
.btn-spinner {
    width: 16px;
    height: 16px;
    border-radius: 999px;
    border: 2px solid rgba(255, 255, 255, 0.6);
    border-top-color: #ffffff;
    animation: spin 0.7s linear infinite;
}

.btn--secondary .btn-spinner,
.btn--ghost .btn-spinner,
.btn--danger .btn-spinner,
.btn--success .btn-spinner,
.btn--warning .btn-spinner,
.btn--outline .btn-spinner {
    border-color: rgba(148, 163, 184, 0.7);
    border-top-color: #374151;
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
