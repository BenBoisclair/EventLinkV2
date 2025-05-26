<script setup lang="ts">
const emit = defineEmits<{
    (e: 'create'): void;
}>();
</script>

<template>
    <div
        @click="emit('create')"
        class="bg-surface dark:bg-dark-surface dark:border-dark-border dark:hover:border-dark-primary flex h-[200px] cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-6 shadow-sm transition-shadow hover:border-primary hover:shadow-md"
    >
        <v-icon
            icon="$plus"
            class="w-10 h-10 mb-2 text-gray-400 dark:text-dark-text-tertiary"
        />
        <span
            class="text-lg font-medium text-gray-500 dark:text-dark-text-secondary"
            >Create Event</span
        >
    </div>
</template>
