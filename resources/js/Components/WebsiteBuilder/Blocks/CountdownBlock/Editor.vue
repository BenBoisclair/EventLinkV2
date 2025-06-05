<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Toggle from "@/Components/Forms/Toggle.vue";
import Checkbox from "@/Components/UI/Checkbox.vue";
import Section from "@/Components/UI/Section.vue";
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
        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <InputLabel value="Title" class="dark:text-dark-text-primary" />
            <Input
                :model-value="currentProps.title"
                @update:model-value="updateProperty('title', $event)"
                class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </Section>


        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <div class="flex items-center justify-between">
                <InputLabel
                    value="Use Event Dates"
                    class="dark:text-dark-text-primary"
                />
                <Toggle
                    :model-value="currentProps.useEventDates"
                    @update:model-value="
                        updateProperty('useEventDates', $event)
                    "
                />
            </div>
        </Section>

        <Section class="flex-shrink-0 space-y-4 dark:bg-dark-surface">
            <div class="text-sm font-medium dark:text-dark-text-primary">
                Display Units
            </div>
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
                    :model-value="currentProps[`show${unit}`]"
                    @update:model-value="updateProperty(`show${unit}`, $event)"
                />
            </div>
        </Section>

        <Section
            v-if="!currentProps.useEventDates"
            class="flex-shrink-0 space-y-2 dark:bg-dark-surface"
        >
            <InputLabel
                value="Custom Start Date"
                class="dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.startDate"
                @update:model-value="updateProperty('startDate', $event)"
                type="datetime-local"
                class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary dark:[color-scheme:dark]"
            />
        </Section>

        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <InputLabel
                value="Finished Countdown Text"
                class="dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.finishedText"
                @update:model-value="updateProperty('finishedText', $event)"
                class="block w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </Section>

        <Section class="flex-shrink-0 space-y-4 dark:bg-dark-surface">
            <div class="text-sm font-medium dark:text-dark-text-primary">
                Button Settings
            </div>
            <div class="flex items-center justify-between">
                <InputLabel
                    value="Enable Button"
                    class="dark:text-dark-text-primary"
                />
                <Checkbox
                    :model-value="currentProps.buttonEnabled"
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
        </Section>
    </div>
</template>
