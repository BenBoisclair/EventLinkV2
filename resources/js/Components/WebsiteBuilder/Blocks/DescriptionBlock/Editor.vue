<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Textarea from "@/Components/Forms/Textarea.vue";
import Section from "@/Components/UI/Section.vue";
import Toggle from "@/Components/Forms/Toggle.vue";
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
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
        <div>
            <InputLabel
                value="Background Color"
                class="mb-1 dark:text-dark-text-primary"
            />
            <div class="space-y-3">
                <Toggle
                    :model-value="currentProps.useThemeBackground ?? true"
                    @update:model-value="
                        (value) => {
                            updateProperty('useThemeBackground', value);
                            // If switching to custom color and no backgroundColor is set, provide a default
                            if (!value && !currentProps.backgroundColor) {
                                updateProperty('backgroundColor', '#3b82f6');
                            }
                        }
                    "
                    label="Use Theme Color"
                />

                <div v-if="currentProps.useThemeBackground === false">
                    <ColorPalettePicker
                        :model-value="currentProps.backgroundColor || '#3b82f6'"
                        @update:model-value="
                            updateProperty('backgroundColor', $event)
                        "
                        label="Custom Background Color"
                        id="description-background-color"
                        class="w-full h-10"
                    />
                </div>
            </div>
        </div>

        <div>
            <InputLabel
                for="title"
                value="Title"
                class="mb-1 dark:text-dark-text-primary"
            />
            <Input
                id="title"
                :model-value="currentProps.title"
                @update:model-value="updateProperty('title', $event)"
                type="text"
                class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </div>
        <div>
            <InputLabel
                for="description"
                value="Description"
                class="mb-1 dark:text-dark-text-primary"
            />
            <Textarea
                id="description"
                :model-value="currentProps.description"
                @update:model-value="updateProperty('description', $event)"
                class="mt-1 resize-none dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
                :rows="6"
            />
        </div>
    </div>
</template>
