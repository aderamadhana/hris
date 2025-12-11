<template>
    <header class="header" ref="headerRef">
        <!-- LEFT: LOGO / BRAND + CABANG -->
        <div class="header-left">
            <div class="logo-image-sidebar">
                <img
                    src="/assets/images/logo.png"
                    alt="HRIS Logo"
                    loading="lazy"
                />
            </div>

            <!-- SWITCH CABANG -->
            <div class="branch-wrapper" v-if="user.role_id == 1">
                <!-- <button
                    type="button"
                    class="branch-pill"
                    @click.stop="toggleBranchMenu"
                >
                    <div class="branch-icon">🏢</div>
                    <div class="branch-info">
                        <div class="branch-label">Cabang</div>
                        <div class="branch-name">
                            {{ activeBranch.name }}
                        </div>
                    </div>
                    <span
                        class="branch-chevron"
                        :class="{ open: branchMenuOpen }"
                    >
                        ▾
                    </span>
                </button> -->

                <!-- MENU CABANG -->
                <div v-if="branchMenuOpen" class="branch-menu">
                    <button
                        v-for="b in branches"
                        :key="b.id"
                        type="button"
                        class="branch-menu-item"
                        :class="{ active: b.id === activeBranch.id }"
                        @click="selectBranch(b)"
                    >
                        <span class="branch-dot"></span>
                        <span class="branch-menu-name">{{ b.name }}</span>
                        <span
                            v-if="b.id === activeBranch.id"
                            class="branch-check"
                        >
                            ✔
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT: USER + DATE + DROPDOWN -->
        <div class="header-right">
            <div class="user-wrapper" @click.stop="toggleUserMenu">
                <div class="avatar">
                    <span>A</span>
                </div>

                <div class="user-info">
                    <div class="user-name">{{ user?.name }}</div>
                    <div class="user-role">{{ user?.role?.role_name }}</div>
                </div>

                <!-- PEMBATAS -->
                <div class="user-divider"></div>

                <!-- HARI & TANGGAL -->
                <div class="user-date">
                    <div class="user-day">{{ dayName }}</div>
                    <div class="user-date-text">{{ dateText }}</div>
                </div>

                <span class="chevron" :class="{ open: userMenuOpen }">▾</span>
            </div>

            <!-- DROPDOWN USER -->
            <div v-if="userMenuOpen" class="user-menu">
                <Link
                    href="#"
                    class="user-menu-item"
                    @click.prevent="goProfile"
                >
                    <span>👤</span>
                    <span>Profile</span>
                </Link>

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="user-menu-item danger"
                >
                    <span>🚪</span>
                    <span>Logout</span>
                </Link>
            </div>
        </div>
    </header>
</template>

<script>
import { Link, router, usePage } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },

    data() {
        const page = usePage();
        const branches = [
            { id: 'all', name: 'Semua Cabang' },
            { id: 'malang', name: 'Malang' },
            { id: 'surabaya', name: 'Surabaya' },
            { id: 'bandung', name: 'Bandung' },
            { id: 'bekasi', name: 'Bekasi' },
        ];

        return {
            user: page.props.auth.user,
            userMenuOpen: false,
            branchMenuOpen: false,

            branches,
            activeBranch: branches[0], // ✅ TIDAK null

            dayName: '',
            dateText: '',
        };
    },

    mounted() {
        // inisialisasi tanggal
        const now = new Date();
        this.dayName = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
        }).format(now);

        this.dateText = new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(now);

        this.activeBranch = this.branches[0];

        window.addEventListener('click', this.handleClickOutside);
    },

    beforeUnmount() {
        window.removeEventListener('click', this.handleClickOutside);
    },

    methods: {
        toggleUserMenu() {
            this.userMenuOpen = !this.userMenuOpen;
            if (this.userMenuOpen) this.branchMenuOpen = false;
        },

        toggleBranchMenu() {
            this.branchMenuOpen = !this.branchMenuOpen;
            if (this.branchMenuOpen) this.userMenuOpen = false;
        },

        handleClickOutside(event) {
            if (
                this.$refs.headerRef &&
                !this.$refs.headerRef.contains(event.target)
            ) {
                this.userMenuOpen = false;
                this.branchMenuOpen = false;
            }
        },

        selectBranch(branch) {
            this.activeBranch = branch;
            this.branchMenuOpen = false;

            console.log('Cabang aktif:', branch.id);
            // nanti bisa dikirim ke backend via Inertia router
        },

        goProfile() {
            router.visit(`/employee/profil/${this.user.id}`);
        },
    },
};
</script>

<style scoped>
/* WRAPPER CABANG */
.branch-wrapper {
    position: relative;
    margin-left: 24px;
}

/* PILL CABANG */
.branch-pill {
    margin-left: 16px;
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 10px;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
    cursor: pointer;
    font-size: 12px;
    color: #111827;
    white-space: nowrap;
}

.branch-icon {
    font-size: 16px;
}

.branch-text {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}

.branch-label {
    font-size: 10px;
    color: #9ca3af;
}

.branch-value {
    font-size: 12px;
    font-weight: 600;
}

.branch-chevron {
    font-size: 10px;
    color: #6b7280;
}

.branch-chevron.open {
    transform: rotate(180deg);
}

/* MENU CABANG */
.branch-menu {
    position: absolute;
    top: 110%;
    left: 0;
    min-width: 180px;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
    padding: 6px;
    z-index: 40;
}

.branch-menu-item {
    width: 100%;
    padding: 8px 10px;
    border-radius: 8px;
    border: none;
    background: transparent;
    text-align: left;
    font-size: 13px;
    cursor: pointer;
    color: #111827;
    transition: background-color 0.12s ease;
}

.branch-menu-item:hover {
    background: #f3f4f6;
}

.branch-menu-item.active {
    background: #eff6ff;
    color: #1d4ed8;
}

.branch-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #d1d5db;
}

.branch-menu-item.active .branch-dot {
    background: #1d4ed8;
}

.branch-menu-name {
    flex: 1;
}

.branch-check {
    font-size: 12px;
    color: #16a34a;
}

/* RESPONSIVE: kalau layar kecil, cabang agak dikecilkan */
@media (max-width: 768px) {
    .branch-wrapper {
        margin-left: 14px;
    }

    .branch-pill {
        padding: 5px 10px;
    }

    .branch-name {
        font-size: 12px;
    }

    .branch-menu {
        min-width: 180px;
    }
}
</style>
