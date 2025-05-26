<script setup lang="ts">
// No editor options for CanvasBlock yet
import type { CanvasBlockProps } from '@/types/blocks';

defineProps<{
    initialProps: CanvasBlockProps;
    websiteId: string;
    blockIdProp: string;
}>();

// Future editor controls will go here
</script>

<template>
    <div class="dark:text-dark-text-secondary p-4 text-sm text-gray-500">
        <!-- Placeholder for Canvas Block Editor -->
        <p>Click on the canvas in the preview area to draw.</p>
        <p class="mt-2">No configuration options available yet.</p>
        <!-- Editor controls like brush size, color etc. could be added here -->
    </div>
</template>
