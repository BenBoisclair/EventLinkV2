<template>
    <div class="flex min-h-[300px] flex-col items-center justify-center py-12">
        <img src="/icons/website.svg" alt="Website" class="w-16 h-16 mb-4" />
        <h2 class="mb-2 text-xl font-bold text-primary">No website yet</h2>
        <p class="mb-6 text-center text-text-muted">
            Create a website for your event to showcase all the important
            information
        </p>
        <Button
            @click="emit('createWebsite')"
            text="Create Website"
            icon="$plus"
            :disabled="disabled"
        />
    </div>
</template>

<script setup>
import Button from '@/Components/UI/Button.vue';

const props = defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['createWebsite']);
</script>
