<template>
    <div
        :class="[
            'bg-surface rounded-lg border-[1px] border-border p-4',
            'dark:bg-dark-surface dark:border-dark-border',
            $attrs.class,
        ]"
    >
        <h2
            v-if="title"
            class="dark:text-dark-primary text-[24px] font-bold text-primary"
        >
            {{ title }}
        </h2>
        <slot></slot>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: false,
    },
});
</script>
