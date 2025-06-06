<script setup lang="ts">
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Toggle from "@/Components/Forms/Toggle.vue";
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
import type { CanvasBlockProps } from '@/types/blocks';
import { useBlockEditor } from "@/Composables/useBlockEditor";
import { type PropType } from "vue";

const props = defineProps({
    initialProps: {
        type: Object as PropType<CanvasBlockProps>,
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

const { currentProps, updateProperty } = useBlockEditor<CanvasBlockProps>(props.blockIdProp);
</script>

<template>
    <div v-if="currentProps" class="flex flex-col h-full gap-5">
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
                        id="canvas-background-color"
                        class="w-full h-10"
                    />
                </div>
            </div>
        </div>

        <div class="dark:text-dark-text-secondary p-4 text-sm text-gray-500">
            <!-- Placeholder for Canvas Block Editor -->
            <p>Click on the canvas in the preview area to draw.</p>
            <p class="mt-2">No configuration options available yet.</p>
            <!-- Editor controls like brush size, color etc. could be added here -->
        </div>
    </div>
</template>
