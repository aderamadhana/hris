<template>
    <div class="app">
        <Header />
        <div class="body">
            <Sidebar />
            <Content>
                <slot />
            </Content>
        </div>
        <AlertPopup />
    </div>
</template>

<script setup>
import AlertPopup from '@/components/AlertPopup.vue';
import Content from './Content.vue';
import Header from './Header.vue';
import Sidebar from './Sidebar.vue';
</script>

<style scoped>
.body {
    display: flex;
    min-height: calc(100vh - 60px);
}
</style>
