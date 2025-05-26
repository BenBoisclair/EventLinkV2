<template>
    <label :for="forInput" class="block text-sm font-medium text-[#374151]">
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
        required: false,
        default: null,
    },
});
</script>
