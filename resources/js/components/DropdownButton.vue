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

/* BUTTON - Matching the image style */
.dropdown-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    background: #2563eb;
    color: #ffffff;

    padding: 10px 20px;
    border: none;
    border-radius: 10px;

    font-size: 14px;
    font-weight: 600;
    cursor: pointer;

    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    transition: all 0.2s ease;
}

.dropdown-btn:hover {
    background: #1d4ed8;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
}

.dropdown-btn:active {
    transform: scale(0.98);
}

.dropdown-btn:focus-visible {
    outline: 2px solid rgba(37, 99, 235, 0.5);
    outline-offset: 2px;
}

.caret {
    font-size: 10px;
    transition: transform 0.2s ease;
}

.caret-open {
    transform: rotate(180deg);
}

/* DROPDOWN MENU */
.dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;

    min-width: 200px;
    padding: 8px;

    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;

    box-shadow:
        0 10px 30px rgba(0, 0, 0, 0.1),
        0 4px 12px rgba(0, 0, 0, 0.05);

    z-index: 1000;
    animation: dropdownSlide 0.2s ease-out;
}

/* DROPDOWN ITEMS */
.dropdown-menu ::v-deep a,
.dropdown-menu ::v-deep button,
.dropdown-menu ::v-deep .dropdown-item {
    display: flex;
    align-items: center;

    width: 100%;
    padding: 10px 14px;

    background: transparent;
    border: none;
    border-radius: 8px;

    font-size: 14px;
    font-weight: 500;
    color: #374151;
    text-decoration: none;
    text-align: left;

    cursor: pointer;
    transition: all 0.15s ease;
}

.dropdown-menu ::v-deep a:hover,
.dropdown-menu ::v-deep button:hover,
.dropdown-menu ::v-deep .dropdown-item:hover {
    background: #f3f4f6;
    color: #1f2937;
}

/* DIVIDER */
.dropdown-menu ::v-deep .dropdown-divider {
    height: 1px;
    margin: 6px 0;
    background: #e5e7eb;
}

/* DESTRUCTIVE ACTION */
.dropdown-menu ::v-deep .danger {
    color: #dc2626;
}

.dropdown-menu ::v-deep .danger:hover {
    background: #fef2f2;
    color: #b91c1c;
}

/* ANIMATION */
@keyframes dropdownSlide {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
