<template>
    <select
        ref="el"
        class="filter-input"
        :multiple="isMultiple"
        :disabled="disabled"
    >
        <slot />
    </select>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

// Import di component biar tidak tergantung urutan import di app.ts
import jQuery from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';

const props = defineProps({
    modelValue: { type: [String, Number, Array, null], default: '' },
    settings: { type: Object, default: () => ({}) },
    disabled: { type: Boolean, default: false },
    multiple: { type: Boolean, default: null }, // null = auto dari modelValue
});

const emit = defineEmits(['update:modelValue']);
const el = ref(null);

let $; // instance jquery yang dipakai
let $el;
let observer = null;
let refreshTimer = null;

const isMultiple = computed(() => {
    if (props.multiple !== null) return props.multiple;
    return Array.isArray(props.modelValue);
});

function ensureSelect2Attached() {
    // pakai window.$ kalau ada, kalau tidak pakai import jQuery
    $ = window.$ || jQuery;
    window.$ = window.jQuery = $;

    // kalau select2 belum attach ke $.fn, attach manual
    if (!$.fn || !$.fn.select2) {
        try {
            // beberapa build select2 butuh dipanggil sebagai function
            if (typeof select2 === 'function') select2($);
        } catch (e) {
            console.error('Gagal attach Select2 ke jQuery:', e);
        }
    }
}

function buildOptions() {
    // default: search selalu muncul
    return {
        width: '100%',
        minimumResultsForSearch: 0,
        ...props.settings,
    };
}

function setValue(v) {
    if (!$el) return;
    $el.val(v).trigger('change.select2');
}

function init() {
    if (!$el) return;
    if (!$.fn.select2) {
        console.error(
            'Select2 belum ter-load. Pastikan select2 terinstall dan berhasil di-attach ke jQuery.',
        );
        return;
    }

    $el.select2(buildOptions());
    setValue(props.modelValue);

    $el.on('change.select2', () => {
        emit('update:modelValue', $el.val());
    });
}

function destroy() {
    if (!$el) return;
    try {
        $el.off('change.select2');
        // destroy hanya kalau sudah pernah init
        if ($el.data('select2')) $el.select2('destroy');
    } catch (e) {
        // ignore
    }
}

function scheduleRefresh() {
    clearTimeout(refreshTimer);
    refreshTimer = setTimeout(() => {
        const current = props.modelValue;
        destroy();
        init();
        setValue(current);
    }, 0);
}

onMounted(() => {
    ensureSelect2Attached();
    $el = $(el.value);

    init();

    // refresh jika slot option berubah (misalnya opsi dinamis)
    observer = new MutationObserver(() => scheduleRefresh());
    observer.observe(el.value, { childList: true, subtree: true });
});

watch(
    () => props.modelValue,
    (v) => setValue(v),
);

watch(
    () => props.settings,
    () => scheduleRefresh(),
    { deep: true },
);

watch(
    () => props.disabled,
    (v) => {
        if ($el) $el.prop('disabled', v);
    },
);

onBeforeUnmount(() => {
    if (observer) observer.disconnect();
    clearTimeout(refreshTimer);
    destroy();
});
</script>
