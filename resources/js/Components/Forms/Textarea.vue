<template>
    <textarea
        :placeholder="placeholder"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :rows="rows"
        class="dark:bg-dark-surface dark:border-dark-border dark:text-dark-text-primary dark:placeholder:text-dark-text-tertiary block w-full rounded-lg border border-[#CAD5E2] p-3 text-sm outline-none focus:outline-none focus:ring-0"
    ></textarea>
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    rows: {
        type: Number,
        default: 4,
    },
});

defineEmits(['update:modelValue']);
</script>
