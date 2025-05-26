<template>
    <label
        :for="forInput"
        class="dark:text-dark-text-secondary block text-sm font-medium text-gray-700"
    >
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>

<script setup>
defineProps({
    value: {
        type: String,
        default: '',
    },
    forInput: {
        type: String,
        required: false, // Made optional as not all inputs might have IDs
        default: null,
    },
});
</script>
