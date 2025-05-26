<template>
    <div
        class="w-1.5 flex-shrink-0 cursor-col-resize bg-gray-300 transition-colors duration-150 hover:bg-blue-500"
        @mousedown="$emit('mousedown', $event)"
    ></div>
</template>

<script setup lang="ts">
defineEmits<{
    (e: 'mousedown', event: MouseEvent): void;
}>();
</script>
