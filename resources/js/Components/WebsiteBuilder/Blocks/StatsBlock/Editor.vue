<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Button from "@/Components/UI/Button.vue";
import IconButton from "@/Components/UI/IconButton.vue";
import Section from "@/Components/UI/Section.vue";
import Toggle from "@/Components/Forms/Toggle.vue";
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
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
                        id="stats-background-color"
                        class="w-full h-10"
                    />
                </div>
            </div>
        </div>

        <div>
            <InputLabel
                value="Stats Items"
                class="mb-1 dark:text-dark-text-primary"
            />
            <div class="flex flex-col gap-2">
                <div
                    v-for="(stat, index) in currentProps.stats"
                    :key="index"
                    class="flex items-center min-w-0 gap-2"
                >
                    <Input
                        :model-value="stat.title"
                        @update:model-value="
                            updateArrayItem('stats', index, { title: $event })
                        "
                        placeholder="Title"
                        class="flex-1 min-w-0 dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary dark:placeholder-dark-text-tertiary"
                    />
                    <Input
                        :model-value="stat.value"
                        @update:model-value="
                            updateArrayItem('stats', index, { value: $event })
                        "
                        placeholder="Value"
                        class="flex-1 min-w-0 dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary dark:placeholder-dark-text-tertiary"
                    />
                    <IconButton
                        title="Remove Stat"
                        variant="danger"
                        size="sm"
                        @click="removeArrayItem('stats', index)"
                    />
                </div>
                <div class="flex mt-1">
                    <Button
                        text="Add Stat"
                        variant="outline-primary"
                        @click="
                            addArrayItem('stats', {
                                title: 'New Stat',
                                value: '0',
                            })
                        "
                        class="w-full py-1 text-sm"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
