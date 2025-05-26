<template>
    <div class="flex items-center">
        <input
            :id="id"
            type="checkbox"
            :checked="modelValue"
            :required="required"
            :disabled="disabled"
            @change="
                !disabled && $emit('update:modelValue', $event.target.checked)
            "
            class="dark:border-dark-border dark:bg-dark-surface dark:checked:bg-dark-primary dark:focus:ring-dark-primary dark:focus:ring-offset-dark-surface h-4 w-4 rounded border border-gray-600 text-primary focus:ring-primary"
            :class="{ 'cursor-not-allowed opacity-50': disabled }"
        />
        <label
            v-if="$slots.default"
            :for="id"
            class="dark:text-dark-text-secondary ml-2 block text-sm text-gray-900"
            :class="{ 'cursor-not-allowed opacity-50': disabled }"
        >
            <slot />
        </label>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: [Array, Boolean],
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String,
        default: () => `checkbox-${Math.random().toString(36).substring(7)}`,
    },
});

defineEmits(['update:modelValue']);
</script>
