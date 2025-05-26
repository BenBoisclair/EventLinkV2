<script setup lang="ts">
import type { Exhibitor } from '@/types/exhibitor';
import { resolveStorageUrl } from '@/utils/storage';
import { computed, ref, watch } from 'vue';

interface Props {
    modelValue: boolean; // Controls visibility (v-model)
    exhibitor: Exhibitor | null;
    backgroundColor?: string | null; // Add background color prop
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const isVisible = ref(props.modelValue);

watch(
    () => props.modelValue,
    (newValue) => {
        isVisible.value = newValue;
    },
);

const closeDisplay = () => {
    emit('update:modelValue', false);
};

// Computed property for the banner URL using resolveStorageUrl
const bannerUrl = computed(() => {
    const resolvedUrl = resolveStorageUrl(props.exhibitor?.banner_path);
    // Use placeholder if the resolved URL is empty (path was null/undefined or helper returned empty)
    return resolvedUrl || '/images/placeholder-banner.jpg';
});

// Computed classes for responsive styling
const overlayClasses = computed(() => [
    'fixed inset-0 z-50 items-center justify-center flex transition-opacity duration-300 ease-in-out',
    {
        'opacity-100 pointer-events-auto': isVisible.value,
        'opacity-0 pointer-events-none': !isVisible.value,
    },
    'bg-black/70', // Semi-transparent background for both views
]);

const contentClasses = computed(() => [
    'relative w-full max-w-full h-full md:h-auto md:max-h-[90vh] md:max-w-4xl  transition-transform duration-300 ease-in-out',
    {
        'scale-100': isVisible.value,
        'scale-95': !isVisible.value,
    },
]);

const imageClasses = computed(() => [
    'block w-full h-full object-contain md:rounded-lg  -mt-14 md:mt-0 md:max-h-[90vh]', // Add rounding here if desired
]);
</script>

<template>
    <Teleport to="body">
        <div :class="overlayClasses" @click.self="closeDisplay">
            <div
                v-if="isVisible && exhibitor"
                :class="contentClasses"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="`exhibitor-banner-title-${exhibitor.id}`"
            >
                <!-- Close Button -->
                <button
                    type="button"
                    class="absolute z-10 p-2 text-white rounded-full right-2 top-2 bg-black/30 hover:bg-black/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-black/30"
                    aria-label="Close"
                    @click="closeDisplay"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        ></path>
                    </svg>
                </button>

                <!-- Banner Image -->
                <img
                    :id="`exhibitor-banner-title-${exhibitor.id}`"
                    :src="bannerUrl"
                    :alt="`${exhibitor.name} Banner`"
                    :class="imageClasses"
                    loading="lazy"
                />
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
/* Add any additional specific styles if needed */
</style>
