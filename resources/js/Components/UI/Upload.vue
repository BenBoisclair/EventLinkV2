<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps<{
    text?: string;
    subtext?: string;
    variant?: 'default' | 'row';
    loading?: boolean;
    error?: boolean;
    previewImageUrl?: string | null;
}>();

const emit = defineEmits(['update:error']);

const internalError = ref(props.error);
const containerRef = ref<HTMLDivElement | null>(null);
const useColumnLayout = ref(false);
const threshold = 300; // px

watch(
    () => props.error,
    (newError) => {
        internalError.value = newError;
        if (newError) {
            setTimeout(() => {
                internalError.value = false;
            }, 3000);
        }
    },
);

const setupResizeObserver = () => {
    if (!containerRef.value) return;

    const observer = new ResizeObserver((entries) => {
        for (const entry of entries) {
            useColumnLayout.value = entry.contentRect.width < threshold;
        }
    });

    observer.observe(containerRef.value);

    // Return the observer so it can be disconnected later
    return observer;
};

let resizeObserver: ResizeObserver | null = null;

onMounted(() => {
    resizeObserver = setupResizeObserver();
    // Initial check in case the component mounts at a small size
    if (containerRef.value) {
        useColumnLayout.value = containerRef.value.offsetWidth < threshold;
    }
});

onUnmounted(() => {
    if (resizeObserver) {
        resizeObserver.disconnect();
    }
});

const containerClasses = computed(() => {
    const base = [
        'relative overflow-hidden flex w-full flex-1 items-center rounded-lg border-2 border-dashed p-4 transition-colors duration-300 ease-in-out',
    ];

    // Layout
    if (
        useColumnLayout.value ||
        !(props.variant === 'row' || props.previewImageUrl)
    ) {
        base.push('flex-col gap-2 justify-center p-20');
    } else {
        base.push('flex-row gap-4');
    }

    // State-based classes
    if (internalError.value) {
        // Error State
        base.push(
            'bg-red-100 border-red-500 dark:bg-dark-status-red/20 dark:border-dark-status-red',
        );
    } else if (props.loading) {
        // Loading State
        base.push(
            'border-border bg-background-light dark:border-dark-border dark:bg-dark-surface cursor-default',
        );
    } else {
        // Normal State + Interaction
        base.push(
            'border-border bg-background-light dark:border-dark-border dark:bg-dark-surface',
            'hover:bg-gray-100 dark:hover:bg-dark-surface-elevated',
            'active:bg-gray-200 dark:active:bg-dark-surface-elevated',
            'cursor-pointer',
        );
    }

    return base;
});

const progressStyle = computed(() => ({
    width: props.loading ? '100%' : '0%',
    transition: 'width 8s linear',
    opacity: props.loading ? 0.5 : 0,
}));
</script>

<template>
    <div :class="containerClasses" ref="containerRef">
        <div
            class="pointer-events-none absolute left-0 top-0 h-full bg-success"
            :style="progressStyle"
        ></div>

        <img
            v-if="previewImageUrl && !loading && !internalError"
            :src="previewImageUrl"
            alt="Preview"
            class="dark:border-dark-border z-10 h-16 max-w-[50%] flex-shrink-0 rounded border object-contain"
        />

        <v-icon
            v-if="loading"
            size="24"
            class="dark:text-dark-primary z-10 animate-spin text-primary"
            :class="{ 'ml-auto': previewImageUrl }"
        >
            $loading
        </v-icon>
        <v-icon
            v-else-if="internalError"
            size="24"
            class="dark:text-dark-status-red z-10 text-danger"
            :class="{ 'ml-auto': previewImageUrl }"
        >
            $alertCircleOutline
        </v-icon>
        <v-icon
            v-else-if="!previewImageUrl"
            size="24"
            class="dark:text-dark-primary z-10 text-primary"
        >
            $cloudUploadOutline
        </v-icon>

        <div class="relative z-10 flex-grow">
            <p
                :class="[
                    'font-medium',
                    internalError
                        ? 'dark:text-dark-status-red text-danger'
                        : 'dark:text-dark-primary text-primary',
                    useColumnLayout ||
                    !(props.variant === 'row' || props.previewImageUrl)
                        ? 'text-center'
                        : 'text-left',
                ]"
            >
                {{
                    loading
                        ? 'Uploading...'
                        : internalError
                          ? 'Upload Failed'
                          : text || 'Upload'
                }}
            </p>
            <p
                :class="[
                    'text-sm',
                    internalError
                        ? 'dark:text-dark-status-red text-danger'
                        : 'dark:text-dark-text-secondary text-text-muted',
                    useColumnLayout ||
                    !(props.variant === 'row' || props.previewImageUrl)
                        ? 'text-center'
                        : 'text-left',
                ]"
            >
                {{
                    loading
                        ? 'Please wait...'
                        : internalError
                          ? 'Please try again.'
                          : subtext || 'or drag and drop your file here'
                }}
            </p>
        </div>
    </div>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
