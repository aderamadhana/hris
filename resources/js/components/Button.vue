<script setup>
import { computed, useAttrs } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary', // primary | secondary | ghost | danger
    },
    size: {
        type: String,
        default: 'md', // sm | md | lg
    },
    block: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    iconOnly: {
        type: Boolean,
        default: false,
    },
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
    attrs.class, // biar class tambahan tetap kepakai
]);
</script>

<template>
    <button :class="classes" :disabled="disabled || loading" v-bind="attrs">
        <span v-if="loading" class="btn-spinner" aria-hidden="true"></span>
        <span class="btn-label">
            <slot />
        </span>
    </button>
</template>

<style scoped>
.btn-base {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 999px;
    border: 1px solid transparent;
    cursor: pointer;
    font-weight: 500;
    letter-spacing: 0.01em;
    white-space: nowrap;
    transition:
        background-color 0.14s ease,
        border-color 0.14s ease,
        color 0.14s ease,
        box-shadow 0.14s ease,
        transform 0.08s ease;
    outline: none;
}

/* SIZE */
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

/* BLOCK */
.btn--block {
    width: 100%;
}

/* ICON ONLY */
.btn--icon-only {
    padding-inline: 10px;
}

/* VARIANT: PRIMARY */
.btn--primary {
    background: linear-gradient(135deg, #0ea5e9, #3b82f6);
    border-color: transparent;
    color: #ffffff;
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
}

.btn--primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 10px 22px rgba(37, 99, 235, 0.45);
}

.btn--primary:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 6px 14px rgba(37, 99, 235, 0.35);
}

/* VARIANT: SECONDARY */
.btn--secondary {
    background: #e5e7eb;
    border-color: #d1d5db;
    color: #111827;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
}

.btn--secondary:hover:not(:disabled) {
    background: #d1d5db;
}

/* VARIANT: GHOST */
.btn--ghost {
    background: #ffffff;
    border-color: #e5e7eb;
    color: #111827;
}

.btn--ghost:hover:not(:disabled) {
    background: #f9fafb;
}

/* VARIANT: DANGER */
.btn--danger {
    background: #fee2e2;
    border-color: #fecaca;
    color: #b91c1c;
}

.btn--danger:hover:not(:disabled) {
    background: #fecaca;
}

/* DISABLED & LOADING */
button:disabled,
.is-loading {
    opacity: 0.6;
    cursor: default;
    box-shadow: none;
    transform: none;
}

/* SPINNER */
.btn-spinner {
    width: 16px;
    height: 16px;
    border-radius: 999px;
    border: 2px solid rgba(255, 255, 255, 0.6);
    border-top-color: rgba(255, 255, 255, 1);
    animation: spin 0.7s linear infinite;
}

.btn--secondary .btn-spinner,
.btn--ghost .btn-spinner,
.btn--danger .btn-spinner {
    border-color: rgba(148, 163, 184, 0.8);
    border-top-color: rgba(55, 65, 81, 1);
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
