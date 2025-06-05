<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";

defineProps({
    title: {
        type: String,
        default: "Add New Block",
    },
});

const emit = defineEmits<{ (e: "back"): void }>();

const handleBack = () => {
    emit("back");
};
</script>

<template>
    <div class="flex flex-col h-full p-4 dark:bg-dark-surface">
        <h4
            class="mb-3 font-semibold text-gray-700 dark:text-dark-text-primary text-md"
        >
            {{ title }}
        </h4>

        <div class="flex mb-4 overflow-y-auto grow">
            <slot></slot>
        </div>

        <div
            class="flex justify-end pt-3 space-x-2 border-t dark:border-dark-border"
        >
            <Button
                text="Cancel"
                icon="$arrowLeft"
                variant="outline-toned"
                size="sm"
                @click="handleBack"
            />
        </div>
    </div>
</template>