<template>
    <div
        class="bg-surface dark:bg-dark-surface dark:border-dark-border max-w-[300px] rounded-lg border border-border px-6 py-4"
    >
        <div class="flex items-center gap-2 mb-4">
            <v-icon
                :icon="statistic.icon || defaultIcon"
                size="small"
                class="dark:text-dark-primary text-primary"
            />
            <span class="font-bold dark:text-dark-primary text-primary">{{
                statistic.name
            }}</span>
        </div>
        <div class="flex flex-col items-end">
            <div
                class="dark:text-dark-text-primary text-[36px] font-bold text-toned"
            >
                {{ statistic.count }}
            </div>
            <div class="text-sm dark:text-dark-text-secondary text-text-dimmed">
                {{ statistic.title || 'Total' }}
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    statistic: {
        type: Object,
        required: true,
        validator: (stat) => {
            return (
                typeof stat === 'object' && 'count' in stat && 'name' in stat
            );
        },
    },
    defaultIcon: {
        type: String,
        default: 'storeOutline',
    },
});
</script>
