<template>
    <div
        class="w-full h-px dark:bg-dark-border bg-border"
        :class="class"
        :style="{ height: `${size}px` }"
    ></div>
</template>

<script setup>
defineProps({
    size: {
        type: Number,
        default: 1,
    },
    class: {
        type: String,
        default: "",
    },
});
</script>
