<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Textarea from "@/Components/Forms/Textarea.vue";
import Section from "@/Components/UI/Section.vue";
import type { DescriptionBlockProps } from "@/types/blocks";
import { useBlockEditor } from "@/Composables/useBlockEditor";
import { type PropType } from "vue";

const props = defineProps({
    initialProps: {
        type: Object as PropType<DescriptionBlockProps>,
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

const { currentProps, updateProperty } = useBlockEditor<DescriptionBlockProps>(
    props.blockIdProp
);
</script>

<template>
    <div class="flex flex-col h-full gap-5" v-if="currentProps">
        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <InputLabel
                for="title"
                value="Title"
                class="dark:text-dark-text-primary"
            />
            <Input
                id="title"
                :model-value="currentProps.title"
                @update:model-value="updateProperty('title', $event)"
                type="text"
                class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </Section>
        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <InputLabel
                for="description"
                value="Description"
                class="dark:text-dark-text-primary"
            />
            <Textarea
                id="description"
                :model-value="currentProps.description"
                @update:model-value="updateProperty('description', $event)"
                class="mt-1 dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
                :rows="6"
            />
        </Section>
    </div>
</template>
