<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
import Section from "@/Components/UI/Section.vue";
import type { ExhibitorShowcaseBlockProps } from "@/types/blocks";
import { useBlockEditor } from "@/composables/useBlockEditor";
import { type PropType } from "vue";

const props = defineProps({
    initialProps: {
        type: Object as PropType<ExhibitorShowcaseBlockProps>,
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

const { currentProps, updateProperty } =
    useBlockEditor<ExhibitorShowcaseBlockProps>(props.blockIdProp);
</script>

<template>
    <div class="flex flex-col h-full gap-5" v-if="currentProps">
        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <InputLabel
                value="Block Title"
                class="dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.title"
                @update:model-value="updateProperty('title', $event)"
                placeholder="Enter the title for the exhibitor section"
                class="w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </Section>

        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <ColorPalettePicker
                :model-value="currentProps.titleColor"
                @update:model-value="updateProperty('titleColor', $event)"
                label="Title Color"
            />
            <ColorPalettePicker
                :model-value="currentProps.backgroundColor"
                @update:model-value="updateProperty('backgroundColor', $event)"
                label="Background Color"
            />
        </Section>
    </div>
</template>
