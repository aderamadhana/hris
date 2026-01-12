<template>
    <div class="dropdown" ref="dropdown">
        <button
            class="dropdown-btn"
            @click.stop="toggle"
            :aria-expanded="isOpen.toString()"
        >
            {{ label }}
            <span class="caret" :class="{ 'caret-open': isOpen }">▼</span>
        </button>

        <div class="dropdown-menu" v-show="isOpen">
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    name: 'DropdownButton',

    props: {
        label: {
            type: String,
            default: 'Dropdown',
        },
    },

    data() {
        return {
            isOpen: false,
        };
    },

    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },

    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },

    methods: {
        toggle() {
            this.isOpen = !this.isOpen;
        },

        close() {
            this.isOpen = false;
        },

        handleClickOutside(event) {
            // if (!this.$refs.dropdown.contains(event.target)) {
            this.close();
            // }
        },
    },
};
</script>

<style scoped>
.dropdown {
    position: relative;
    display: inline-block;
}

/* BUTTON */
.dropdown-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    white-space: nowrap;

    /* lebih premium */
    background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
    color: #ffffff;

    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.18);

    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.2px;

    box-shadow:
        0 10px 18px rgba(37, 99, 235, 0.18),
        0 2px 6px rgba(0, 0, 0, 0.08);

    transition:
        transform 0.12s ease,
        box-shadow 0.2s ease,
        filter 0.2s ease;
}

.dropdown-btn:hover {
    filter: brightness(1.02);
    box-shadow:
        0 14px 26px rgba(37, 99, 235, 0.22),
        0 6px 14px rgba(0, 0, 0, 0.1);
}

.dropdown-btn:active {
    transform: translateY(1px);
}

.dropdown-btn:focus-visible {
    outline: 3px solid rgba(59, 130, 246, 0.35);
    outline-offset: 3px;
}

/* CARET */
.caret {
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    transform: translateY(1px);
    transition:
        transform 0.18s ease,
        opacity 0.18s ease;
}

.caret-open {
    transform: translateY(1px) rotate(180deg);
    opacity: 1;
}

/* DROPDOWN MENU */
.dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    left: 0;

    min-width: 260px;
    padding: 8px;

    /* lebih “floating” */
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);

    border-radius: 14px;
    border: 1px solid rgba(17, 24, 39, 0.08);

    box-shadow:
        0 22px 50px rgba(0, 0, 0, 0.16),
        0 10px 20px rgba(0, 0, 0, 0.08);

    z-index: 1000;
    transform-origin: top left;
    animation: dropdownPop 0.14s ease-out;
}

/* pointer kecil ke tombol */
.dropdown-menu::before {
    content: '';
    position: absolute;
    top: -6px;
    left: 18px;

    width: 12px;
    height: 12px;

    background: rgba(255, 255, 255, 0.92);
    border-left: 1px solid rgba(17, 24, 39, 0.08);
    border-top: 1px solid rgba(17, 24, 39, 0.08);
    transform: rotate(45deg);

    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* ITEMS */
.dropdown-menu ::v-deep .dropdown-item,
.dropdown-menu ::v-deep a,
.dropdown-menu ::v-deep button {
    display: flex;
    align-items: center;
    gap: 12px;

    width: 100%;
    padding: 12px 12px;

    border-radius: 12px;
    border: 1px solid transparent;
    background: transparent;

    font-size: 14px;
    font-weight: 600; /* bisa turunin ke 500 kalau mau lebih ringan */
    color: #111827;

    text-decoration: none;
    text-align: left;
    cursor: pointer;

    transition:
        background-color 0.14s ease,
        border-color 0.14s ease,
        transform 0.08s ease;
}

/* icon lebih rapi */
.dropdown-menu ::v-deep .icon {
    width: 16px;
    height: 16px;
    opacity: 0.9;
}

.dropdown-menu ::v-deep .dropdown-item:hover,
.dropdown-menu ::v-deep a:hover,
.dropdown-menu ::v-deep button:hover {
    background: rgba(37, 99, 235, 0.08);
    border-color: rgba(37, 99, 235, 0.12);
}

.dropdown-menu ::v-deep .dropdown-item:active,
.dropdown-menu ::v-deep a:active,
.dropdown-menu ::v-deep button:active {
    transform: translateY(1px);
}

/* divider */
.dropdown-menu ::v-deep .dropdown-divider {
    height: 1px;
    margin: 8px 6px;
    background: rgba(17, 24, 39, 0.08);
}

/* danger item (kalau ada) */
.dropdown-menu ::v-deep .danger {
    color: #dc2626;
}
.dropdown-menu ::v-deep .danger:hover {
    background: rgba(220, 38, 38, 0.08);
    border-color: rgba(220, 38, 38, 0.14);
}

@keyframes dropdownPop {
    from {
        opacity: 0;
        transform: translateY(-6px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
/* ALIGN MENU KE KIRI (menu melebar ke kiri dari tombol) */
.dropdown-menu {
    left: auto !important;
    right: 0 !important;
    transform-origin: top right;
}

/* pindahkan pointer (segitiga) ke sisi kanan */
.dropdown-menu::before {
    left: auto !important;
    right: 18px !important;
    transform-origin: center;
}
</style>
