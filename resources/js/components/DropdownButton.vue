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

    gap: 10px; /* lebih lega */
    white-space: nowrap; /* label + caret jangan pecah */

    background: #2563eb;
    color: #ffffff;

    padding: 10px 18px; /* sedikit dipadatkan biar proporsional */
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 10px;

    font-size: 14px;
    font-weight: 600;
    cursor: pointer;

    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    transition:
        background-color 0.2s ease,
        box-shadow 0.2s ease,
        transform 0.1s ease;
}

.dropdown-btn:hover {
    background: #1d4ed8;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
}

.dropdown-btn:active {
    transform: translateY(1px); /* lebih “natural” daripada scale */
}

.dropdown-btn:focus-visible {
    outline: 2px solid rgba(37, 99, 235, 0.45);
    outline-offset: 3px;
}

/* CARET */
.caret {
    font-size: 12px; /* sedikit lebih besar biar kebaca */
    line-height: 1;
    margin-left: 2px; /* tambahan jarak halus */
    opacity: 0.9;

    display: inline-flex;
    align-items: center;

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

    min-width: 220px;
    padding: 8px;

    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;

    box-shadow:
        0 14px 34px rgba(0, 0, 0, 0.12),
        0 6px 16px rgba(0, 0, 0, 0.06);

    z-index: 1000;
    transform-origin: top left;
    animation: dropdownSlide 0.16s ease-out;
}

/* kalau mau menu align kanan (opsional)
.dropdown.right .dropdown-menu{
  left: auto;
  right: 0;
  transform-origin: top right;
}
*/

/* ITEMS */
.dropdown-menu ::v-deep a,
.dropdown-menu ::v-deep button,
.dropdown-menu ::v-deep .dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px; /* jarak icon ↔ teks */

    width: 100%;
    padding: 10px 12px;

    background: transparent;
    border: 1px solid transparent;
    border-radius: 10px;

    font-size: 14px;
    font-weight: 600; /* biar konsisten dengan style tombol */
    color: #374151;
    text-decoration: none;
    text-align: left;

    cursor: pointer;
    transition:
        background-color 0.15s ease,
        color 0.15s ease,
        border-color 0.15s ease;
}

.dropdown-menu ::v-deep a:hover,
.dropdown-menu ::v-deep button:hover,
.dropdown-menu ::v-deep .dropdown-item:hover {
    background: #f3f4f6;
    border-color: #e5e7eb;
    color: #111827;
}

/* DIVIDER */
.dropdown-menu ::v-deep .dropdown-divider {
    height: 1px;
    margin: 6px 0;
    background: #e5e7eb;
}

/* DANGER */
.dropdown-menu ::v-deep .danger {
    color: #dc2626;
}

.dropdown-menu ::v-deep .danger:hover {
    background: #fef2f2;
    border-color: #fecaca;
    color: #b91c1c;
}

/* ANIMATION */
@keyframes dropdownSlide {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
