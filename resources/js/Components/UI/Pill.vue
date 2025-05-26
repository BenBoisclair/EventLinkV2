<script setup lang="ts">
import { computed } from 'vue';

type PillVariant = 'success' | 'warning' | 'danger' | 'info' | 'secondary';

interface Props {
    text: string;
    variant?: PillVariant;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'secondary',
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'success':
            return 'border-2 border-success/30 bg-success/10 text-success dark:border-dark-status-green/30 dark:bg-dark-status-green/10 dark:text-dark-status-green';
        case 'warning':
            return 'border-2 border-warning/30 bg-warning/10 text-warning dark:border-dark-status-yellow/30 dark:bg-dark-status-yellow/10 dark:text-dark-status-yellow';
        case 'danger':
            return 'border border-danger/30 bg-danger/10 text-danger dark:border-dark-status-red/30 dark:bg-dark-status-red/10 dark:text-dark-status-red';
        case 'info':
            return 'border border-status-blue/30 bg-status-blue/10 text-status-blue dark:border-dark-status-blue/30 dark:bg-dark-status-blue/10 dark:text-dark-status-blue';
        case 'secondary':
        default:
            return 'border border-text-muted/30 bg-gray-50 text-text-muted dark:border-dark-text-tertiary/30 dark:bg-dark-text-tertiary/10 dark:text-dark-text-secondary';
    }
});
</script>

<template>
    <div
        class="inline-flex items-center px-3 py-1 text-sm font-bold rounded-full w-fit"
        :class="variantClasses"
    >
        {{ text }}
    </div>
</template>
