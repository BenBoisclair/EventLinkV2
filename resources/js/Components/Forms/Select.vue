<script setup lang="ts">
import { computed } from 'vue';

interface Option {
    value: string | number | null;
    label: string;
}

defineOptions({
    inheritAttrs: false,
});

const props = defineProps<{
    modelValue: string | number | null;
    options: Option[];
    label?: string;
    id?: string;
    required?: boolean;
    disabled?: boolean;
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number | null): void;
}>();

const localId = computed(
    () =>
        props.id ||
        `select-input-${Math.random().toString(36).substring(2, 9)}`,
);

const selected = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const baseClasses =
    'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-dark-surface dark:border-dark-border dark:text-dark-text-primary dark:focus:border-dark-primary dark:focus:ring-dark-primary';
const disabledClasses =
    'disabled:opacity-75 disabled:bg-gray-100 dark:disabled:bg-dark-surface-elevated';
const errorClasses =
    'border-red-500 focus:border-red-500 focus:ring-red-500 dark:border-dark-status-red dark:focus:border-dark-status-red dark:focus:ring-dark-status-red';
</script>

<template>
    <div>
        <label
            v-if="label"
            :for="localId"
            class="dark:text-dark-text-secondary block text-sm font-medium text-gray-700"
        >
            {{ label }}
            <span v-if="required" class="dark:text-dark-status-red text-red-500"
                >*</span
            >
        </label>
        <select
            :id="localId"
            v-model="selected"
            v-bind="$attrs"
            :class="[baseClasses, { [errorClasses]: !!error }, disabledClasses]"
            :disabled="disabled"
            :required="required"
            class="mt-1"
        >
            <option
                v-for="option in options"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
        <p
            v-if="error"
            class="dark:text-dark-status-red mt-1 text-sm text-red-600"
        >
            {{ error }}
        </p>
    </div>
</template>
