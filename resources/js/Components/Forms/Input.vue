<template>
    <div
        class="dark:border-dark-border dark:bg-dark-surface bg-surface flex items-center rounded-lg border border-[#CAD5E2] focus-within:ring-0"
        :class="{
            'px-3 py-[1px]': icon,
            'dark:bg-dark-surface-elevated cursor-not-allowed bg-gray-100':
                disabled,
        }"
    >
        <img
            v-if="icon && iconType === 'svg'"
            :src="icon"
            :alt="iconAlt"
            class="dark:text-dark-text-tertiary text-gray-400"
            width="20"
            height="20"
        />
        <v-icon
            v-else-if="icon && iconType === 'mdi'"
            :icon="icon"
            size="small"
            class="dark:text-dark-primary text-primary"
        ></v-icon>
        <input
            :type="type"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'dark:text-dark-text-primary dark:placeholder:text-dark-text-tertiary flex-1 border-none bg-transparent text-sm outline-none focus:outline-none focus:ring-0',
                { 'ml-1': icon },
                {
                    'dark:text-dark-text-tertiary cursor-not-allowed text-gray-500':
                        disabled,
                },
            ]"
            :disabled="disabled"
        />
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    icon: {
        type: String,
        default: null,
    },
    iconType: {
        type: String,
        default: 'svg',
        validator: (value) => ['svg', 'mdi'].includes(value),
    },
    iconAlt: {
        type: String,
        default: 'Icon',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>
