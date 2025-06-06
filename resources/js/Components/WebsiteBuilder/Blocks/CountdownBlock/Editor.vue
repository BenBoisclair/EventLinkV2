<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Toggle from "@/Components/Forms/Toggle.vue";
import Checkbox from "@/Components/UI/Checkbox.vue";
import Section from "@/Components/UI/Section.vue";
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
import type { CountdownBlockProps } from "@/types/blocks";
import { useBlockEditor } from "@/Composables/useBlockEditor";
import { type PropType } from "vue";

const props = defineProps({
    initialProps: {
        type: Object as PropType<CountdownBlockProps>,
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

const { currentProps, updateProperty } = useBlockEditor<CountdownBlockProps>(
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
                        id="countdown-background-color"
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
                class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </div>

        <div>
            <InputLabel
                value="Finished Countdown Text"
                class="mb-1 dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.finishedText"
                @update:model-value="updateProperty('finishedText', $event)"
                class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <InputLabel
                    value="Use Event Dates"
                    class="dark:text-dark-text-primary"
                />
                <Toggle
                    :model-value="currentProps.useEventDates ?? true"
                    @update:model-value="
                        updateProperty('useEventDates', $event)
                    "
                />
            </div>
        </div>

        <div v-if="!currentProps.useEventDates">
            <InputLabel
                value="Custom Start Date"
                class="mb-1 dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.startDate ?? ''"
                @update:model-value="updateProperty('startDate', $event)"
                type="datetime-local"
                class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary dark:[color-scheme:dark]"
            />
        </div>

        <div>
            <InputLabel
                value="Display Units"
                class="mb-1 dark:text-dark-text-primary"
            />
            <div
                class="flex flex-col gap-2 p-2 border rounded-md border-dark-border"
            >
                <div
                    v-for="unit in ['Days', 'Hours', 'Minutes', 'Seconds']"
                    :key="unit"
                    class="flex items-center justify-between"
                >
                    <InputLabel
                        :value="`Show ${unit}`"
                        class="dark:text-dark-text-secondary"
                    />
                    <Toggle
                        :model-value="(currentProps[`show${unit}` as keyof CountdownBlockProps] as boolean) ?? false"
                        @update:model-value="
                            updateProperty(`show${unit}`, $event)
                        "
                    />
                </div>
            </div>
        </div>

        <!-- <div>
            <InputLabel
                value="Button Settings"
                class="mb-1 dark:text-dark-text-primary"
            />
            <div class="flex items-center justify-between">
                <InputLabel
                    value="Enable Button"
                    class="dark:text-dark-text-primary"
                />
                <Toggle
                    :model-value="currentProps.buttonEnabled ?? false"
                    @update:model-value="
                        updateProperty('buttonEnabled', $event)
                    "
                />
            </div>

            <template v-if="currentProps.buttonEnabled">
                <InputLabel
                    value="Button Text"
                    class="dark:text-dark-text-primary"
                />
                <Input
                    :model-value="currentProps.buttonText"
                    @update:model-value="updateProperty('buttonText', $event)"
                    class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
                />

                <InputLabel
                    value="Button Link (URL)"
                    class="dark:text-dark-text-primary"
                />
                <Input
                    :model-value="currentProps.buttonLink"
                    @update:model-value="updateProperty('buttonLink', $event)"
                    type="url"
                    placeholder="https://example.com"
                    class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
                />
            </template>
        </div> -->
    </div>
</template>
