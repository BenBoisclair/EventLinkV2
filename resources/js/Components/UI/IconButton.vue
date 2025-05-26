<script setup lang="ts">
defineProps({
    ariaLabel: { type: String, required: true },
    title: { type: String, default: 'Delete' },
    variant: { type: String, default: 'danger' }, // 'danger', 'secondary', etc.
    size: { type: String, default: 'sm' }, // 'xs', 'sm', 'md'
    icon: { type: String, default: '$trashCanOutline' }, // Default to trash icon alias
});

const emit = defineEmits<{
    (e: 'click', event: MouseEvent): void;
}>();

const handleClick = (event: MouseEvent) => {
    emit('click', event);
};

// Define base styles and variant/size specific styles
const baseClasses =
    'flex-shrink-0 rounded p-1 flex items-center justify-center';
const variantClasses = {
    danger: 'text-red-500 hover:bg-red-100 hover:text-red-700 dark:text-dark-status-red dark:hover:bg-dark-status-red/20 dark:hover:text-dark-status-red',
    secondary:
        'text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-dark-text-tertiary dark:hover:bg-dark-surface-elevated dark:hover:text-dark-text-secondary',
    // Add more variants if needed
};
const sizeClasses = {
    xs: 'w-4 h-4', // 16px
    sm: 'w-5 h-5', // 20px
    md: 'w-6 h-6', // 24px
};
</script>

<template>
    <button
        type="button"
        :class="[
            baseClasses,
            variantClasses[variant as keyof typeof variantClasses] ||
                variantClasses.secondary,
            sizeClasses[size as keyof typeof sizeClasses] || sizeClasses.sm,
        ]"
        :aria-label="ariaLabel"
        :title="title"
        @click="handleClick"
    >
        <v-icon :icon="icon" :size="size"></v-icon>
    </button>
</template>
