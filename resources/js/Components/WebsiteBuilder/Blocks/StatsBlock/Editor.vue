<script setup lang="ts">
// Example usage of useBlockEditor composable:
// const { currentProps, updateProperty, updateArrayItem, addArrayItem, removeArrayItem } = useBlockEditor<StatsBlockProps>(props.blockIdProp);
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Button from "@/Components/UI/Button.vue";
import IconButton from "@/Components/UI/IconButton.vue";
import Section from "@/Components/UI/Section.vue";
import type { StatsBlockProps } from "@/types/blocks";
import { useBlockEditor } from "@/Composables/useBlockEditor";
import { type PropType } from "vue";

const props = defineProps({
    initialProps: {
        type: Object as PropType<StatsBlockProps>,
        required: true,
    },
    websiteId: {
        type: Number,
        required: true,
    },
    blockIdProp: {
        type: String,
        required: true,
    },
});

const {
    currentProps,
    updateProperty,
    updateArrayItem,
    addArrayItem,
    removeArrayItem,
} = useBlockEditor<StatsBlockProps>(props.blockIdProp);
</script>

<template>
    <div v-if="currentProps" class="flex flex-col h-full gap-5">

        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <InputLabel
                value="Stats Items"
                class="dark:text-dark-text-primary"
            />
            <div
                v-for="(stat, index) in currentProps.stats"
                :key="index"
                class="flex items-center space-x-2"
            >
                <Input
                    :model-value="stat.title"
                    @update:model-value="
                        updateArrayItem('stats', index, { title: $event })
                    "
                    placeholder="Title"
                    class="dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary dark:placeholder-dark-text-tertiary"
                />
                <Input
                    :model-value="stat.value"
                    @update:model-value="
                        updateArrayItem('stats', index, { value: $event })
                    "
                    placeholder="Value"
                    class="dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary dark:placeholder-dark-text-tertiary"
                />
                <IconButton
                    title="Remove Stat"
                    variant="danger"
                    size="sm"
                    @click="removeArrayItem('stats', index)"
                />
            </div>
            <Button
                text="Add Stat"
                variant="outline-secondary"
                size="sm"
                @click="
                    addArrayItem('stats', { title: 'New Stat', value: '0' })
                "
                class="mt-3 w-fit"
            />
        </Section>
    </div>
</template>
