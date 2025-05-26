<template>
    <div
        class="flex items-center rounded-lg border border-[#E2E8F0] bg-[#F1F5F9] focus-within:ring-0"
        :class="{
            'px-3 py-[1px]': icon,
            'cursor-not-allowed bg-gray-100': disabled,
        }"
    >
        <img
            v-if="icon && iconType === 'svg'"
            :src="icon"
            :alt="iconAlt"
            class="text-gray-400"
            width="20"
            height="20"
        />
        <v-icon
            v-else-if="icon && iconType === 'mdi'"
            :icon="icon"
            size="small"
            class="text-[#193CB8]"
        ></v-icon>
        <input
            :type="type"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'flex-1 border-none bg-transparent text-sm outline-none placeholder:text-gray-500 focus:outline-none focus:ring-0',
                { 'ml-1': icon },
                {
                    'cursor-not-allowed text-gray-500': disabled,
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
