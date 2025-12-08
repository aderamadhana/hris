<template>
    <div class="tabs-root">
        <!-- HEADER TAB -->
        <div class="tabs-header">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                type="button"
                class="tab-pill"
                :class="{ active: tab.key === internalActive }"
                @click="select(tab.key)"
            >
                <span class="tab-label">{{ tab.label }}</span>
            </button>
        </div>

        <!-- BODY TAB + ANIMASI -->
        <div class="tabs-body">
            <Transition name="tab-fade" mode="out-in">
                <div :key="internalActive">
                    <slot :name="internalActive" />
                </div>
            </Transition>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    tabs: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const internalActive = ref(
    props.modelValue ?? (props.tabs[0] && props.tabs[0].key) ?? null,
);

// sinkron v-model
watch(
    () => props.modelValue,
    (val) => {
        if (val && val !== internalActive.value) internalActive.value = val;
    },
);

// kalau daftar tab berubah
watch(
    () => props.tabs,
    (tabs) => {
        if (!tabs.length) return;
        if (!tabs.find((t) => t.key === internalActive.value)) {
            internalActive.value = tabs[0].key;
            emit('update:modelValue', internalActive.value);
            emit('change', internalActive.value);
        }
    },
    { deep: true },
);

const select = (key) => {
    if (key === internalActive.value) return;
    internalActive.value = key;
    emit('update:modelValue', key);
    emit('change', key);
};
</script>

<style scoped>
.tabs-root {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* STRIP TAB ATAS – desain sebelumnya */
.tabs-header {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 6px;
    border-radius: 999px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
}

/* TOMBOL TAB */
.tab-pill {
    position: relative;
    border: none;
    cursor: pointer;
    border-radius: 999px;
    padding: 7px 16px;
    background: transparent;
    font-size: 14px;
    font-weight: 500;
    color: #4b5563;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition:
        background-color 0.15s ease,
        color 0.15s ease,
        box-shadow 0.15s ease,
        transform 0.1s ease;
}

.tab-pill:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

/* TAB AKTIF – gradient simple */
.tab-pill.active {
    background: linear-gradient(135deg, #0ea5e9, #6366f1);
    color: #ffffff;
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
}

.tab-pill.active::after {
    content: '';
    position: absolute;
    inset-inline: 16px;
    bottom: -4px;
    height: 3px;
    border-radius: 999px;
    background: rgba(56, 189, 248, 0.95);
}

.tab-label {
    white-space: nowrap;
}

/* BODY TAB DI DALAM CARD */
.tabs-body {
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
    border: 1px solid #e5e7eb;
    padding: 16px 18px 14px;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
}

/* ANIMASI TRANSISI TAB */
.tab-fade-enter-active,
.tab-fade-leave-active {
    transition:
        opacity 0.18s ease,
        transform 0.18s ease;
}

.tab-fade-enter-from {
    opacity: 0;
    transform: translateY(4px);
}

.tab-fade-enter-to {
    opacity: 1;
    transform: translateY(0);
}

.tab-fade-leave-from {
    opacity: 1;
    transform: translateY(0);
}

.tab-fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

/* RESPONSIVE */
@media (max-width: 640px) {
    .tabs-header {
        border-radius: 14px;
    }

    .tab-pill {
        font-size: 13px;
        padding: 6px 12px;
    }

    .tabs-body {
        padding: 14px 14px 12px;
    }
}
</style>
