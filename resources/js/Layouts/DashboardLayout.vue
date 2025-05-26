<script setup lang="ts">
import NavigationBar from "@/Components/NavigationBar.vue";

interface Props {
    showNavigation?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showNavigation: true,
});
</script>

<template>
    <div
        class="flex flex-col h-screen min-h-screen overflow-hidden bg-background-light"
    >
        <NavigationBar v-if="showNavigation" />

        <main class="flex flex-col flex-1 overflow-auto">
            <slot />
        </main>
    </div>
</template>

<style scoped>
/* Additional styles can be added here if needed */
</style>
