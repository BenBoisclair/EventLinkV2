<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";

defineProps({
    editorTitle: {
        type: String,
        required: true,
    },
    showConfirm: {
        type: Boolean,
        default: true,
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
    <div class="flex flex-col h-full p-4 dark:bg-dark-surface">
        <h4
            class="mb-3 font-semibold text-gray-700 dark:text-dark-text-primary text-md"
        >
            <slot name="title">
                {{ editorTitle }}
            </slot>
        </h4>

        <div class="flex mb-4 overflow-y-auto grow">
            <slot></slot>
        </div>

        <div
            class="flex justify-end pt-3 space-x-2 border-t dark:border-dark-border"
        >
            <Button
                :text="backText"
                icon="$arrowLeft"
                variant="outline-toned"
                size="sm"
                @click="handleBack"
            />
            <Button
                v-if="showConfirm"
                :text="confirmText"
                icon="$contentSave"
                variant="primary"
                size="sm"
                @click="handleConfirm"
            />
        </div>
    </div>
</template>
