<template>
    <AppLayout :employeeId="employeeId">
        <AdminDashboard v-if="user.role_id == 1" />
        <EmployeeDashboard v-if="user.role_id == 2" />
    </AppLayout>
</template>

<script>
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import AdminDashboard from './AdminDashboard.vue';
import EmployeeDashboard from './EmployeeDashboard.vue';

export default {
    components: {
        AppLayout,
        AdminDashboard,
        EmployeeDashboard,
    },

    computed: {
        roleName() {
            return this.user.role?.role_name ?? '-';
        },

        userName() {
            return this.user.name ?? '-';
        },

        employeeId() {
            return this.user.employee?.id ?? null;
        },
    },
    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,
        };
    },

    mounted() {
        console.log(this.user);
    },
};
</script>
