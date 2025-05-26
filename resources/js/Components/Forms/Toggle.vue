<script setup lang="ts">
defineProps<{
    modelValue: boolean;
    label?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();
</script>

<template>
    <label class="relative inline-flex cursor-pointer items-center">
        <input
            type="checkbox"
            :checked="modelValue"
            @change="
                emit(
                    'update:modelValue',
                    ($event.target as HTMLInputElement).checked,
                )
            "
            class="peer sr-only"
        />
        <div
            class="dark:bg-dark-border dark:after:border-dark-text-tertiary dark:after:bg-dark-surface-elevated dark:peer-checked:bg-dark-primary h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white"
        ></div>
        <span
            v-if="label"
            class="dark:text-dark-text-secondary ml-3 font-medium"
            >{{ label }}</span
        >
    </label>
</template>
