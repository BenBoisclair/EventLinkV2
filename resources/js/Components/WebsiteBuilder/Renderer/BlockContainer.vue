<script setup lang="ts">
interface BlockContainerProps {
    backgroundColor?: string;
}

const props = withDefaults(defineProps<BlockContainerProps>(), {
    backgroundColor: '#FFFFFF',
});
</script>

<template>
    <div
        class="relative w-full"
        :style="{ backgroundColor: props.backgroundColor }"
    >
        <slot />
    </div>
</template>
