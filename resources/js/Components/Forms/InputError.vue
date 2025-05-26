<template>
    <div v-show="message">
        <p class="dark:text-dark-status-red text-sm text-red-600">
            {{ message }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    message: {
        type: String,
        default: '',
    },
});
</script>
