<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Section from "@/Components/UI/Section.vue";
import Toggle from "@/Components/Forms/Toggle.vue";
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
import type { ExhibitorShowcaseBlockProps } from "@/types/blocks";
import { useBlockEditor } from "@/Composables/useBlockEditor";
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
                        id="exhibitor-showcase-background-color"
                        class="w-full h-10"
                    />
                </div>
            </div>
        </div>

        <div>
            <InputLabel
                value="Title"
                class="mb-1 dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.title"
                @update:model-value="updateProperty('title', $event)"
                placeholder="Enter the title for the exhibitor section"
                class="w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </div>
    </div>
</template>
