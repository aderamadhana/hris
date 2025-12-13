<template>
    <div class="dropdown" ref="dropdown">
        <button
            class="dropdown-btn"
            @click.stop="toggle"
            :aria-expanded="isOpen.toString()"
        >
            {{ label }}
            <span class="caret">▼</span>
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
            if (!this.$refs.dropdown.contains(event.target)) {
                this.close();
            }
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
    gap: 6px;

    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;

    padding: 9px 16px;
    border: none;
    border-radius: 8px;

    font-size: 14px;
    font-weight: 500;
    cursor: pointer;

    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    transition: all 0.2s ease;
}

.dropdown-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(37, 99, 235, 0.35);
}

.dropdown-btn:active {
    transform: translateY(0);
    box-shadow: 0 3px 8px rgba(37, 99, 235, 0.25);
}

.dropdown-btn:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.35);
}

.caret {
    font-size: 10px;
    opacity: 0.8;
}

/* DROPDOWN MENU */
.dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;

    min-width: 190px;
    padding: 6px;

    background: #ffffff;
    border-radius: 10px;
    border: 1px solid rgba(0, 0, 0, 0.05);

    box-shadow:
        0 10px 25px rgba(0, 0, 0, 0.12),
        0 4px 10px rgba(0, 0, 0, 0.08);

    z-index: 1000;
    animation: dropdownFade 0.15s ease-out;
}

/* ITEM */
.dropdown-menu ::v-deep a,
.dropdown-menu ::v-deep button {
    display: flex;
    align-items: center;

    width: 100%;
    padding: 10px 12px;

    background: transparent;
    border: none;
    border-radius: 8px;

    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
    text-decoration: none;

    cursor: pointer;
    transition:
        background 0.15s ease,
        color 0.15s ease;
}

.dropdown-menu ::v-deep a:hover,
.dropdown-menu ::v-deep button:hover {
    background: #f1f5f9;
    color: #1d4ed8;
}

/* DESTRUCTIVE ACTION */
.dropdown-menu ::v-deep .danger {
    color: #dc2626;
}

.dropdown-menu ::v-deep .danger:hover {
    background: #fee2e2;
    color: #b91c1c;
}

/* ANIMATION */
@keyframes dropdownFade {
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
