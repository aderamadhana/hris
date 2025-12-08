<template>
    <header class="header" ref="headerRef">
        <!-- LEFT: LOGO / BRAND + CABANG -->
        <div class="header-left">
            <div class="logo-mark">H</div>
            <div class="logo-text">
                <div class="logo-title">HRIS</div>
                <div class="logo-subtitle">Human Resources</div>
            </div>

            <!-- SWITCH CABANG -->
            <div class="branch-wrapper">
                <button
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
                </button>

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
                    <div class="user-name">Admin</div>
                    <div class="user-role">System Administrator</div>
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
                <Link href="/profile" class="user-menu-item">
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

<script setup>
import { Link } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';
// import { router } from '@inertiajs/vue3'; // kalau nanti mau kirim ke backend

const headerRef = ref(null);

const userMenuOpen = ref(false);
const branchMenuOpen = ref(false);

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
    if (userMenuOpen.value) branchMenuOpen.value = false;
};

const toggleBranchMenu = () => {
    branchMenuOpen.value = !branchMenuOpen.value;
    if (branchMenuOpen.value) userMenuOpen.value = false;
};

const handleClickOutside = (event) => {
    if (headerRef.value && !headerRef.value.contains(event.target)) {
        userMenuOpen.value = false;
        branchMenuOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    window.removeEventListener('click', handleClickOutside);
});

/** FORMAT HARI & TANGGAL (LOKAL ID) **/
const now = new Date();
const dayName = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
}).format(now);
const dateText = new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
}).format(now);

/** CABANG DUMMY – sesuaikan nanti dengan data real */
const branches = ref([
    { id: 'all', name: 'Semua Cabang' },
    { id: 'malang', name: 'Malang' },
    { id: 'surabaya', name: 'Surabaya' },
    { id: 'bandung', name: 'Bandung' },
    { id: 'bekasi', name: 'Bekasi' },
]);

const activeBranch = ref(branches.value[0]);

const selectBranch = (branch) => {
    activeBranch.value = branch;
    branchMenuOpen.value = false;

    // TODO: integrasi dengan backend kalau perlu
    // router.get(route('dashboard'), { cabang: branch.id }, { preserveState: true });
    console.log('Cabang aktif:', branch.id);
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
