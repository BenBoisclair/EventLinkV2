<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Spacer from "@/Components/UI/Spacer.vue";

defineProps({
    editorTitle: {
        type: String,
        required: true,
    },
    confirmText: {
        type: String,
        default: "Save",
    },
    backText: {
        type: String,
        default: "Cancel",
    },
});

const emit = defineEmits<{ (e: "confirm"): void; (e: "back"): void }>();

const handleConfirm = () => {
    emit("confirm");
};

const handleBack = () => {
    emit("back");
};
</script>

<template>
    <div class="flex flex-col h-full dark:bg-dark-surface">
        <h4
            class="p-4 text-lg font-semibold text-gray-700 dark:text-dark-text-primary"
        >
            <slot name="title">
                {{ editorTitle }}
            </slot>
        </h4>

        <Spacer :size="1" class="mb-3" />

        <div class="flex p-4 mb-4 grow">
            <slot></slot>
        </div>

        <div
            class="flex justify-end p-4 space-x-2 border-t dark:border-dark-border"
        >
            <Button
                :text="backText"
                icon="$arrowLeft"
                variant="outline-toned"
                size="sm"
                @click="handleBack"
            />
            <Button
                :text="confirmText"
                icon="$contentSave"
                variant="primary"
                size="sm"
                @click="handleConfirm"
            />
        </div>
    </div>
</template>
