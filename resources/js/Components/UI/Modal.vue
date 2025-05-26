<template>
    <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30"
        @click.self="handleClickOutside"
    >
        <div
            :class="[
                'relative w-full rounded-lg p-6',
                !contentBackgroundColor && 'bg-surface dark:bg-dark-surface',
                maxWidth,
                contentClass,
            ]"
            :style="{ backgroundColor: contentBackgroundColor }"
        >
            <!-- Close Button -->
            <button
                type="button"
                @click="closeModal"
                class="absolute text-gray-400 transition-colors dark:text-dark-text-tertiary dark:hover:text-dark-text-secondary right-3 top-3 hover:text-gray-600"
                aria-label="Close modal"
            >
                <v-icon icon="$close" size="small"></v-icon>
            </button>

            <div class="flex flex-col">
                <div class="flex items-center mb-4">
                    <h3 class="text-lg font-bold dark:text-dark-text-primary">
                        {{ title }}
                    </h3>
                </div>

                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { withDefaults } from 'vue';

// Combine defineProps and withDefaults
const props = withDefaults(
    defineProps<{
        modelValue: boolean;
        title: string;
        closeOnClickOutside?: boolean;
        maxWidth?: string; // Optional max-width class
        contentClass?: string; // Optional additional classes
        contentBackgroundColor?: string; // Add prop for background color
    }>(),
    {
        closeOnClickOutside: false,
        maxWidth: 'max-w-[800px]', // Set default max-width
        contentClass: '', // Default to empty string
        contentBackgroundColor: undefined, // Default background color prop
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const closeModal = () => {
    emit('update:modelValue', false);
};

const handleClickOutside = () => {
    if (props.closeOnClickOutside) {
        closeModal();
    }
};
</script>
