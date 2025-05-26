<template>
    <div
        :class="[bgClass, size, 'flex items-center justify-center rounded-lg']"
    >
        <template v-if="image">
            <img
                :src="image"
                :alt="name + ' avatar'"
                :class="['object-cover', size, 'rounded-lg']"
            />
        </template>
        <template v-else>
            <span :class="['font-semibold', textClass]">
                {{ name ? name.charAt(0).toUpperCase() : "G" }}
            </span>
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
const props = defineProps({
    name: { type: String, default: "" },
    image: { type: String, default: "" },
    bgClass: { type: String, default: "dark:bg-dark-accent bg-surface" },
    textClass: {
        type: String,
        default: "dark:text-dark-text-primary text-accent",
    },
    size: { type: String, default: "h-[30px] w-[30px]" },
});
</script>
